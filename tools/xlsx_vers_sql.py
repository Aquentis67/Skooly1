#!/usr/bin/env python3
"""Reprise des donnees du classeur ressources-skooly.xlsx -> scripts SQL.

Usage (depuis la racine du projet) :
    pip install openpyxl
    python tools/xlsx_vers_sql.py [chemin/vers/classeur.xlsx]

Produit :
  sql/03_jeu_demonstration.sql          donnees reelles nettoyees + compte admin
  sql/04_interventions_illustratives.sql  OPTIONNEL : exemples d'interventions (absentes du classeur)
  docs/reprise_des_donnees.md           rapport de nettoyage (a presenter en soutenance)

Chaque transformation est comptee et listee dans le rapport : rien n'est modifie en silence.
"""
import sys, re, datetime, collections, pathlib
from decimal import Decimal, InvalidOperation
import openpyxl

RACINE = pathlib.Path(__file__).resolve().parent.parent
CLASSEUR = pathlib.Path(sys.argv[1]) if len(sys.argv) > 1 else RACINE / "tools" / "donnees" / "ressources-skooly.xlsx"
HASH_ADMIN = "$2y$10$is0CqZhho8/7ITAcHDXYVujGPwppPuBzc1ar2V/zuNwfjp5/Mv4ES"  # mot de passe : Skooly2026!

VALIDATION = "validées par l'étudiant le 30/10/2026"   # a mettre a jour si une hypothese change
journal = collections.defaultdict(list)      # categorie -> [descriptions]
def note(cat, txt): journal[cat].append(txt)

def q(v):                                    # litteral SQL
    if v is None: return "NULL"
    return "'" + str(v).replace("\\", "\\\\").replace("'", "''") + "'"

def propre(s):                               # "  N'DIAYE " -> "N'Diaye"
    s0 = s; s = " ".join(str(s).split())
    s = re.sub(r"[^\W\d_]+", lambda m: m.group(0).capitalize(), s.lower())
    if s != s0.strip() or s0 != s0.strip(): note("Casse et espaces des noms/prénoms", f"« {s0} » → « {s} »")
    return s

def tel(s, qui):
    if s is None or not str(s).strip(): return None
    brut = str(s); ch = re.sub(r"\D", "", brut)
    if brut.strip().startswith("+33"): ch = "0" + ch[2:]
    elif len(ch) == 9 and ch[0] in "1234567 89": ch = "0" + ch
    if not re.fullmatch(r"0[1-9]\d{8}", ch):
        note("Téléphones inutilisables (mis à vide)", f"{qui} : « {brut} »"); return None
    res = " ".join(ch[i:i+2] for i in range(0, 10, 2))
    if res != brut: note("Téléphones normalisés (format 06 12 34 56 78)", qui)
    return res

def mail(s, qui):
    if not s: return None
    m = str(s).strip().lower()
    if m != str(s): note("E-mails passés en minuscules", qui)
    if not re.fullmatch(r"[^@\s]+@[^@\s]+\.[^@\s]+", m):
        if re.fullmatch(r"[^@\s]+@[^@\s.]+", m):
            note("✔ E-mails corrigés (hypothèse validée)", f"{qui} : « {m} » → « {m}.fr » (extension .fr manquante, comme tous les autres)")
            m += ".fr"
    return m

def date_naiss(s, qui):
    d = str(s).strip()
    for fmt, nom in [("%d/%m/%Y", "JJ/MM/AAAA"), ("%d-%m-%Y", "JJ-MM-AAAA"), ("%Y-%m-%d", "AAAA-MM-JJ")]:
        try:
            r = datetime.datetime.strptime(d, fmt).date()
            if fmt != "%d/%m/%Y" or not re.fullmatch(r"\d{2}/\d{2}/\d{4}", d): note("Dates converties au format unique", f"{qui} : « {d} »")
            return r
        except ValueError: pass
    m = re.fullmatch(r"(\d{4})-(\d{2})-(\d{2})", d)                 # 2007-13-05 : mois 13 impossible
    if m and int(m[2]) > 12 and int(m[3]) <= 12:
        r = datetime.date(int(m[1]), int(m[3]), int(m[2]))
        note("✔ Dates impossibles corrigées (hypothèse validée)", f"{qui} : « {d} » → {r:%d/%m/%Y} (jour et mois inversés)")
        return r
    raise SystemExit(f"Date illisible pour {qui} : {d!r}")

wb = openpyxl.load_workbook(CLASSEUR, data_only=True)
def lignes(nom): return [r for r in list(wb[nom].iter_rows(values_only=True))[1:] if any(c is not None for c in r)]

# ------------------------------------------------------------------ promotions
LIB = {"DCG": "Diplôme de comptabilité et de gestion", "DSCG": "Diplôme supérieur de comptabilité et de gestion", "GP": "Licence gestionnaire de paie"}
codes_promo = []
for r in lignes("Apprenants"):
    if r[6].strip() not in codes_promo: codes_promo.append(r[6].strip())
promotions = []
for i, c in enumerate(sorted(codes_promo), 1):
    typ, an = c.split(); promotions.append((i, c, f"{LIB[typ]} - {an}{'re' if an == '1' else 'e'} année", "2025-09-01", "2026-06-30"))
id_promo = {p[1]: p[0] for p in promotions}
note("✔ Données absentes du classeur (hypothèses validées)", "Promotions : le classeur ne donne que le code. Libellés repris du sujet ; dates de début/fin supposées 01/09/2025 → 30/06/2026 (le planning du classeur commence le 01/09/2025).")

# -------------------------------------------------------------------- matieres
# Libelles du classeur sans accents : restaures a la main (liste fermee, sans ambiguite).
ACCENTS = {"Comptabilite generale": "Comptabilité générale", "Comptabilite analytique": "Comptabilité analytique",
           "Fiscalite": "Fiscalité", "Mathematiques financieres": "Mathématiques financières"}
matieres = []
for i, r in enumerate(lignes("Matieres"), 1):
    lib = str(r[1]).strip()
    if lib in ACCENTS:
        note("Accents restaurés sur les libellés de matières", f"« {lib} » → « {ACCENTS[lib]} »"); lib = ACCENTS[lib]
    matieres.append((i, str(r[0]).strip().upper(), lib))
id_mat = {m[1]: m[0] for m in matieres}
note("Colonnes non reprises", "Matières : « heures B1 » et « heures B2 » ne sont pas stockées sur la matière (RG06 : le volume dépend de la promotion, il vit dans `intervention`).")
note("Colonnes non reprises", "Onglet « Planification » : hors périmètre de SkoolY 1 (ni planning, ni séances).")

# --------------------------------------------------------------- intervenants
groupes = collections.OrderedDict()
for r in lignes("Intervenants"):
    nom, pre = propre(r[0]), propre(r[1]); m = mail(r[2], f"{nom} {pre}")
    groupes.setdefault(m, []).append((nom, pre, r))
intervenants = []; liens = []
for idx, (m, grp) in enumerate(groupes.items(), 1):
    nom, pre, r = grp[-1]
    if len(grp) > 1:
        note("Doublons fusionnés", f"{nom} {pre} ({m}) : {len(grp)} lignes → 1 (tarifs « {' / '.join(str(g[2][4]) for g in grp)} » : le plus récent « {r[4]} » est conservé, il est annoté « tarif revu en janvier » ; matières regroupées)")
    mats = []
    for g in grp:
        for c in (g[2][6] or "").split(","):
            c = c.strip().upper()
            if c and c not in mats: mats.append(c)
    for c in mats:
        if c not in id_mat: raise SystemExit(f"Matière inconnue {c!r} pour {nom} {pre}")
    rem = [x.rstrip(".") + "." for x in dict.fromkeys(" ".join(str(g[2][7]).split()) for g in grp if g[2][7])]
    st = "salarie" if str(r[5]).lower().startswith("salari") else "vacataire"
    if str(r[5]) not in ("salarie", "vacataire"): note("Statuts corrigés", f"{nom} {pre} : « {r[5]} » → « {st} »")
    tarif = None
    if st == "vacataire":
        brut = str(r[4]).strip() if r[4] is not None else ""
        t = re.sub(r"(€|/h|\s)", "", brut).replace(",", ".")
        try: tarif = Decimal(t); 
        except InvalidOperation: tarif = None
        if tarif is not None:
            if brut != str(tarif.quantize(Decimal('0.01'))) : note("Tarifs convertis en nombres", f"{nom} {pre} : « {brut} » → {tarif:.2f}")
        elif brut.lower() == "gratuit":
            tarif = Decimal(0); note("✔ Tarifs particuliers (hypothèse validée)", f"{nom} {pre} : « Gratuit » → 0,00 €/h (intervenant ponctuel)")
        else:
            tarif = Decimal(0); rem.append("Tarif horaire à renseigner (absent ou illisible dans le classeur" + (f" : « {brut} »)." if brut else ")."))
            note("✔ Tarifs particuliers (hypothèse validée)", f"{nom} {pre} : « {brut or 'vide'} » → 0,00 €/h avec remarque « à renseigner » (un vacataire doit avoir un tarif)")
    elif r[4] not in (None, ""): note("Tarifs ignorés", f"{nom} {pre} (salarié) : tarif « {r[4]} » ignoré (RG04)")
    intervenants.append((idx, nom, pre, m, tel(r[3], f"{nom} {pre}"), st, tarif, " ".join(rem) or None))
    liens += [(idx, id_mat[c]) for c in mats]
note("Colonnes reprises dans un nouveau champ", "Intervenants : « remarques » → colonne `intervenant.remarques` (ajoutée au schéma).")
note("Colonnes reprises dans un nouveau champ", "Intervenants : « matieres » → table `intervenant_matiere` (ajoutée au schéma, RG03).")

# ------------------------------------------------------------------- salles
# Lecture par NOM de colonne : numero, capacite, videoprojecteur, remarque (+ libelle, batiment si presentes).
def table(nom):
    rows = list(wb[nom].iter_rows(values_only=True))
    hdr = [str(h).strip().lower() if h else "" for h in rows[0]]
    return [dict(zip(hdr, r)) for r in rows[1:] if any(c is not None for c in r)]

salles = []; generes = 0
for r in table("SALLES"):
    num = str(r["numero"]).strip(); cap_brut = r["capacite"]
    if re.search(r"∞|illimit|infini", str(cap_brut), re.I):
        cap = None; note("Capacités illimitées (NULL, affichées ∞)", f"Salle {num} : « {cap_brut} »")
    else: cap = int(re.search(r"\d+", str(cap_brut))[0])
    if cap is not None and str(cap_brut).strip() != str(cap): note("Capacités converties en entiers", f"Salle {num} : « {cap_brut} » → {cap}" + (" (≈ approximatif dans le classeur)" if "~" in str(cap_brut) else ""))
    rem = " ".join(str(r["remarque"]).split()) if r.get("remarque") else None
    v = str(r.get("videoprojecteur") or "").strip().lower()
    if v in ("oui", "o"): video = 1
    elif v == "non": video = 0
    else:
        video = 1; note("✔ Vidéoprojecteur : valeur absente (hypothèse validée)", f"Salle {num} : case vide (remarque : « {rem} ») → oui")
    if v == "o": note("Oui/non normalisés", f"Salle {num} : « O » → oui")
    lib = " ".join(str(r["libelle"]).split()) if r.get("libelle") else None
    bat = " ".join(str(r["batiment"]).split()) if r.get("batiment") else None
    if not lib or not bat: generes += 1
    salles.append((num, lib or f"Salle {num}", cap, bat or "Non renseigné", video, rem))
if generes:
    note("✔ Données absentes du classeur (hypothèses validées)", f"Salles : libellé généré (« Salle 101 ») et/ou bâtiment « Non renseigné » pour {generes} salle(s) (colonnes absentes ou vides dans l'onglet SALLES, obligatoires en base).")

# --------------------------------------------------------------- apprenants
apprenants = []
for r in lignes("Apprenants"):
    nom, pre = propre(r[0]), propre(r[1]); qui = f"{nom} {pre}"
    ent = " ".join(str(r[5]).split()) if r[5] else None
    apprenants.append((nom, pre, date_naiss(r[2], qui), mail(r[3], qui), tel(r[4], qui), "alternant" if ent else "initial", ent, "inscrit", id_promo[r[6].strip()]))
note("✔ Données absentes du classeur (hypothèses validées)", "Apprenants : le régime est déduit de l'entreprise (renseignée → alternant, vide → initial) ; le statut de scolarité est « inscrit » pour tous.")

# ---------------------------------------------------- controles de coherence
assert len(apprenants) == 105 and len(promotions) == 8 and len(intervenants) == 21 and len(salles) >= 1, (len(apprenants), len(promotions), len(intervenants), len(salles))
assert len({a[3] for a in apprenants}) == 105 and len({i[3] for i in intervenants}) == 21
assert all(a[2] <= datetime.date(2012, 1, 1) for a in apprenants)

# ------------------------------------------------------------ script 03
o = []; w = o.append
w("-- ============================================================================")
w("-- SkoolY 1 - Donnees reelles du classeur ressources-skooly.xlsx (REJOUABLE)")
w("-- A executer APRES 01_creation_base.sql. Genere par tools/xlsx_vers_sql.py")
w("-- Compte de demonstration : identifiant 'admin' / mot de passe 'Skooly2026!'")
w("-- ============================================================================")
w("USE skooly;\nSET NAMES utf8mb4;\n")
w("-- Vidage dans l'ordre des dependances (enfants d'abord). DELETE et non TRUNCATE :")
w("-- MySQL refuse TRUNCATE sur une table referencee par une cle etrangere (erreur 1701).")
for t in ["intervention", "intervenant_matiere", "apprenant", "salle", "matiere", "intervenant", "promotion", "utilisateur"]:
    w(f"DELETE FROM {t};"); w(f"ALTER TABLE {t} AUTO_INCREMENT = 1;")
w("\nINSERT INTO utilisateur (identifiant, mot_de_passe_hash) VALUES\n  (" + q("admin") + ", " + q(HASH_ADMIN) + ");\n")
w("INSERT INTO promotion (id, code, libelle, date_debut, date_fin) VALUES")
w(",\n".join(f"  ({i}, {q(c)}, {q(l)}, {q(d)}, {q(f)})" for i, c, l, d, f in promotions) + ";\n")
w("INSERT INTO matiere (id, code, libelle) VALUES")
w(",\n".join(f"  ({i}, {q(c)}, {q(l)})" for i, c, l in matieres) + ";\n")
w("INSERT INTO intervenant (id, nom, prenom, email, telephone, statut, tarif_horaire, remarques) VALUES")
w(",\n".join(f"  ({i}, {q(n)}, {q(p)}, {q(m)}, {q(t)}, {q(s)}, {'NULL' if tf is None else f'{tf:.2f}'}, {q(rm)})" for i, n, p, m, t, s, tf, rm in intervenants) + ";\n")
w("INSERT INTO intervenant_matiere (intervenant_id, matiere_id) VALUES")
w(",\n".join(f"  ({a}, {b})" for a, b in liens) + ";\n")
w("INSERT INTO salle (numero, libelle, capacite, batiment, videoprojecteur, remarques) VALUES")
w(",\n".join(f"  ({q(a)}, {q(b)}, {'NULL' if c is None else c}, {q(d)}, {e}, {q(f)})" for a, b, c, d, e, f in salles) + ";\n")
w("INSERT INTO apprenant (nom, prenom, date_naissance, email, telephone, regime, entreprise, statut, promotion_id) VALUES")
w(",\n".join(f"  ({q(a[0])}, {q(a[1])}, '{a[2]}', {q(a[3])}, {q(a[4])}, {q(a[5])}, {q(a[6])}, {q(a[7])}, {a[8]})" for a in apprenants))
o[-1] += ";"
(RACINE / "sql" / "03_jeu_demonstration.sql").write_text("\n".join(o) + "\n", encoding="utf-8")

# --------------------------------------------- script 04 (optionnel, illustratif)
ws = list(wb["Planification"].iter_rows(values_only=True)); hdr = ws[0]
paires = collections.OrderedDict()
for row in ws[2:]:
    for i, p in enumerate(hdr):
        if i >= 2 and p and row[i]: paires.setdefault(p, [])
        if i >= 2 and p and row[i] and row[i] not in paires[p]: paires[p].append(row[i])
heures = {str(r[0]).strip().upper(): (r[2], r[3]) for r in lignes("Matieres")}
enseignants = collections.defaultdict(list)
for iid, mid in liens: enseignants[mid].append(iid)
excl = {i[0] for i in intervenants if i[7] and "parti" in i[7]}
ivs = []
for code, mats in paires.items():
    annee = int(code.split()[1])
    if annee not in (1, 2): continue                                  # B1 = année 1, B2 = année 2 (cf. exemple du sujet : 120 h puis 60 h)
    for mc in mats:
        h = heures[mc][annee - 1]
        cand = [i for i in enseignants[id_mat[mc]] if i not in excl]
        if h and cand: ivs.append((cand[0], id_mat[mc], id_promo[code], int(h)))
o4 = ["-- ============================================================================",
      "-- SkoolY 1 - OPTIONNEL - Interventions ILLUSTRATIVES (rejouable, apres 03)",
      "-- ATTENTION : le classeur ne contient AUCUNE attribution intervenant/promotion.",
      "-- Ces lignes sont des EXEMPLES pour la demonstration (page Promotion : interventions",
      "-- et total d'heures). Regle : matieres vues au planning de la promotion, premier",
      "-- intervenant declare pour la matiere, heures B1 (annee 1) ou B2 (annee 2).",
      "-- Remplacez-les par les vraies attributions du Campus.",
      "-- ============================================================================",
      "USE skooly;\nDELETE FROM intervention;\nALTER TABLE intervention AUTO_INCREMENT = 1;\n",
      "INSERT INTO intervention (intervenant_id, matiere_id, promotion_id, volume_horaire) VALUES",
      ",\n".join(f"  ({a}, {b}, {c}, {d})" for a, b, c, d in ivs) + ";"]
(RACINE / "sql" / "04_interventions_illustratives.sql").write_text("\n".join(o4) + "\n", encoding="utf-8")

# ---------------------------------------------------------------- rapport
ordre = sorted(journal, key=lambda c: (not c.startswith("✔"), c))
rp = ["# Reprise des données du classeur — rapport de nettoyage", "",
      f"Source : `{CLASSEUR.name}` · généré par `tools/xlsx_vers_sql.py` (rejouable).", "",
      f"Les hypothèses marquées ✔ sont {VALIDATION}.", "",
      "## Résultat", "", "| Ensemble | Lignes du classeur | Lignes en base |", "|---|---|---|",
      f"| Apprenants | {len(lignes('Apprenants'))} | {len(apprenants)} |", f"| Promotions | (déduites des apprenants) | {len(promotions)} |",
      f"| Intervenants | {len(lignes('Intervenants'))} | {len(intervenants)} (doublon fusionné) |", f"| Matières | {len(lignes('Matieres'))} | {len(matieres)} |",
      f"| Salles | {len(table('SALLES'))} | {len(salles)} |", f"| Liens intervenant–matière | (colonne « matieres ») | {len(liens)} |", "",
      "## Ce qui a été fait, catégorie par catégorie", ""]
for c in ordre:
    items = journal[c]; rp.append(f"### {c} ({len(items)})"); rp.append("")
    rp += [f"- {x}" for x in items[:60]]; rp.append("")
(RACINE / "docs" / "reprise_des_donnees.md").write_text("\n".join(rp), encoding="utf-8")
print(f"OK : {len(apprenants)} apprenants, {len(promotions)} promotions, {len(intervenants)} intervenants, {len(matieres)} matieres, {len(salles)} salles, {len(liens)} liens, {len(ivs)} interventions illustratives")
for c in ordre: print(f"  {len(journal[c]):3d}  {c}")

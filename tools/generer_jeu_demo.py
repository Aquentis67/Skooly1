#!/usr/bin/env python3
"""Genere sql/03_jeu_demonstration.sql.

ATTENTION : donnees FICTIVES de remplacement. Le sujet demande les donnees
reelles du classeur ressources.xlsx (nettoyees). Remplacez ce jeu par le votre
des que vous avez converti le classeur (voir docs/INSTALLATION.md).
"""
import random, unicodedata, datetime, sys

HASH_ADMIN = "$2y$10$is0CqZhho8/7ITAcHDXYVujGPwppPuBzc1ar2V/zuNwfjp5/Mv4ES"  # Skooly2026!
rng = random.Random(2026)

def q(s):
    if s is None: return "NULL"
    return "'" + str(s).replace("\\", "\\\\").replace("'", "''") + "'"

def ascii_(s):
    s = unicodedata.normalize("NFD", s)
    s = "".join(c for c in s if unicodedata.category(c) != "Mn")
    return s.lower().replace(" ", "").replace("'", "").replace("-", "")

promotions = [
 (1,"DCG 1","Diplôme de comptabilité et de gestion - 1re année",18,2008),
 (2,"DCG 2","Diplôme de comptabilité et de gestion - 2e année",15,2007),
 (3,"DCG 3","Diplôme de comptabilité et de gestion - 3e année",14,2006),
 (4,"DSCG 1","Diplôme supérieur de comptabilité et de gestion - 1re année",12,2005),
 (5,"DSCG 2","Diplôme supérieur de comptabilité et de gestion - 2e année",10,2004),
 (6,"GP 1","Licence gestionnaire de paie - 1re année",14,2007),
 (7,"GP 2","Licence gestionnaire de paie - 2e année",12,2006),
 (8,"GP 3","Licence gestionnaire de paie - 3e année",10,2005),
]
assert sum(p[3] for p in promotions) == 105

matieres = [
 (1,"CG","Comptabilité générale"),(2,"CA","Comptabilité approfondie"),
 (3,"DSOC","Droit des sociétés"),(4,"DFIS","Droit fiscal"),
 (5,"DSOCIAL","Droit social"),(6,"ECO","Économie"),
 (7,"MGT","Management"),(8,"FIN","Finance d'entreprise"),
 (9,"CDG","Contrôle de gestion"),(10,"ANG","Anglais des affaires"),
 (11,"SIG","Systèmes d'information de gestion"),(12,"PAIE","Gestion de la paie"),
 (13,"AUDIT","Audit et commissariat aux comptes"),(14,"COMM","Communication professionnelle"),
]
code_mat = {m[1]: m[0] for m in matieres}

# (id, nom, prenom, statut, tarif, matieres enseignees)
intervenants = [
 (1,"Lambert","Isabelle","salarie",None,["CG","CA"]),
 (2,"Moreau","Philippe","salarie",None,["CG","AUDIT"]),
 (3,"Fournier","Nathalie","salarie",None,["DSOC","DFIS"]),
 (4,"Girard","Laurent","salarie",None,["DSOCIAL","PAIE"]),
 (5,"Rousseau","Céline","salarie",None,["ECO","MGT"]),
 (6,"Blanc","Antoine","salarie",None,["FIN","CDG"]),
 (7,"Faure","Valérie","salarie",None,["CA","CDG"]),
 (8,"Chevalier","Julien","salarie",None,["PAIE","DSOCIAL"]),
 (9,"Garnier","Sandrine","salarie",None,["COMM","ANG"]),
 (10,"Perrin","Olivier","salarie",None,["SIG"]),
 (11,"Robin","Marie-Claire","salarie",None,[]),   # suivi d'alternance seul (RG05)
 (12,"Masson","Étienne","salarie",None,[]),       # suivi d'alternance seul (RG05)
 (13,"Dupuis","Camille","vacataire","45.00",["ANG"]),
 (14,"Lemaire","Hugo","vacataire","52.50",["DFIS","FIN"]),
 (15,"Colin","Aurélie","vacataire","48.00",["DSOC","ECO"]),
 (16,"Renard","Mathieu","vacataire","60.00",["AUDIT","CDG"]),
 (17,"Gauthier","Léa","vacataire","40.00",["COMM","MGT"]),
 (18,"Marchand","Pierre","vacataire","55.00",["PAIE","DSOCIAL"]),
 (19,"Dumont","Nicolas","vacataire","65.00",["FIN","CA"]),
 (20,"Benoit","Claire","vacataire","42.00",["SIG","ECO"]),
 (21,"Leroy","Vincent","vacataire","70.00",["DFIS","AUDIT"]),
]
assert len(intervenants) == 21

salles = [
 ("101","Salle Comptabilité",30,"Bâtiment A",1,None),
 ("102","Salle Droit",30,"Bâtiment A",1,"Tableau blanc à remplacer d'ici janvier."),
 ("201","Amphithéâtre Pacioli",80,"Bâtiment B",1,"Accès PMR par l'entrée côté parking. Micro HF & sono disponibles."),
 ("202","Salle Paie",24,"Bâtiment B",1,None),
 ("012","Salle Projets",18,"Bâtiment A",0,"Pas de vidéoprojecteur : prévoir l'écran mobile."),
]

programme = {  # promotion -> [(matiere, heures)]
 1:[("CG",120),("ECO",60),("DSOC",50),("MGT",40),("ANG",40),("COMM",30)],
 2:[("CG",60),("DFIS",60),("DSOCIAL",50),("SIG",40),("ANG",30)],
 3:[("CA",80),("DFIS",40),("FIN",60),("MGT",30),("ANG",30)],
 4:[("CA",70),("FIN",60),("AUDIT",50),("CDG",50),("DFIS",40),("ANG",30)],
 5:[("AUDIT",60),("CDG",50),("FIN",50),("MGT",30),("ANG",30)],
 6:[("DSOCIAL",60),("PAIE",80),("CG",50),("COMM",30),("ANG",30)],
 7:[("PAIE",90),("DSOCIAL",70),("SIG",40),("ECO",30),("ANG",30)],
 8:[("PAIE",80),("DSOCIAL",60),("DSOC",40),("MGT",30),("COMM",30)],
}

prenoms = ["Emma","Lucas","Léa","Hugo","Chloé","Louis","Manon","Jules","Camille","Gabriel","Inès","Arthur",
 "Sarah","Raphaël","Lola","Adam","Zoé","Nathan","Jade","Théo","Clara","Enzo","Alice","Maxime","Eva","Tom",
 "Anaïs","Paul","Lina","Noah","Océane","Baptiste","Margaux","Axel","Julie","Mathis","Élise","Romain","Maëlle","Yanis"]
noms = ["Martin","Bernard","Dubois","Thomas","Robert","Richard","Petit","Durand","Leroy","Simon","Laurent","Michel",
 "Garcia","David","Bertrand","Roux","Vincent","Fontaine","Morel","André","Lefèvre","Mercier","Dupont","Lambert",
 "Bonnet","François","Martinez","Legrand","Garnier","Faure","Rousseau","Blanchard","Guerin","Muller","Henry",
 "Roussel","Nicolas","Perrot","Morin","Mathieu","Clément","Gauthier","Dumas","Lopez","Fleury","Barbier","Cordier","Marchal"]
entreprises = ["Cabinet EXPERTY","SARL Dupont Compta","Groupe Alvéo","Mairie de Gaillac","Boulangerie Fournil d'Oc",
 "Transports Vidal & Fils","Clinique des Lauriers","Cave coopérative de Rabastens","Agence Immobilière Sud-Ouest",
 "SAS Maison Duclos","Pharmacie du Centre","Bureau Veritas Toulouse"]

def date_rand(annee_centre):
    a = annee_centre + rng.choice([0,0,0,1,-1])
    return datetime.date(a, 1, 1) + datetime.timedelta(days=rng.randrange(0, 365))

def tel():
    return "0%d %02d %02d %02d %02d" % (rng.choice([6,7]), *[rng.randrange(100) for _ in range(4)])

apprenants, pris, emails = [], set(), set()
for pid, code, lib, n, annee in promotions:
    for _ in range(n):
        while True:
            p, nm = rng.choice(prenoms), rng.choice(noms)
            if (p, nm) not in pris: pris.add((p, nm)); break
        base = f"{ascii_(p)}.{ascii_(nm)}"; mail = f"{base}@example.fr"; k = 2
        while mail in emails: mail = f"{base}{k}@example.fr"; k += 1
        emails.add(mail)
        alt = rng.random() < 0.65
        apprenants.append({"nom":nm,"prenom":p,"naiss":date_rand(annee),"email":mail,"tel":tel(),
            "regime":"alternant" if alt else "initial","ent":rng.choice(entreprises) if alt else None,
            "statut":"inscrit","promo":pid})

# Cas particuliers utiles pour les tests / la demonstration
# RG02 : deux mineurs de la meme famille partagent l'adresse email d'un parent.
for i, prenom in zip([0, 1], ["Léo", "Inès"]):
    a = apprenants[i]
    a.update(nom="Vasseur", prenom=prenom, email="famille.vasseur@example.fr",
             naiss=datetime.date(2009, 3 + i, 14), promo=1)
# Un nom avec apostrophe (test d'echappement)
apprenants[2].update(nom="D'Alembert", prenom="Jean-Baptiste")
apprenants[2]["email"] = "jb.dalembert@example.fr"
for i in rng.sample(range(5, 105), 3): apprenants[i]["statut"] = "sorti"
for i in rng.sample([j for j in range(5,105) if apprenants[j]["statut"]=="inscrit"], 2): apprenants[i]["statut"] = "diplome"

enseignants = {}
for iid, *_r, mats in intervenants:
    for m in mats: enseignants.setdefault(m, []).append(iid)
interventions, vus = [], set()
for pid, cours in programme.items():
    for mat, h in cours:
        if pid == 1 and mat == "CG":      # volume reparti sur 2 intervenants
            parts = [(1, 70), (2, 50)]
        else:
            parts = [(rng.choice(enseignants[mat]), h)]
        for iid, vol in parts:
            t = (iid, code_mat[mat], pid); assert t not in vus; vus.add(t)
            interventions.append((iid, code_mat[mat], pid, vol))

out = []
w = out.append
w("-- ============================================================================")
w("-- SkoolY 1 - Jeu de demonstration (REJOUABLE) - a executer APRES 01_creation_base.sql")
w("-- Compte de demonstration : identifiant 'admin' / mot de passe 'Skooly2026!'")
w("-- (donnees fictives generees par tools/generer_jeu_demo.py)")
w("-- ============================================================================")
w("USE skooly;\nSET NAMES utf8mb4;")
w("-- Vidage dans l'ordre des dependances (enfants d'abord). DELETE et non TRUNCATE :")
w("-- MySQL refuse TRUNCATE sur une table referencee par une cle etrangere (erreur 1701).")
for t in ["intervention","apprenant","salle","matiere","intervenant","promotion","utilisateur"]:
    w(f"DELETE FROM {t};")
    w(f"ALTER TABLE {t} AUTO_INCREMENT = 1;")
w("")
w("INSERT INTO utilisateur (identifiant, mot_de_passe_hash) VALUES\n  (" + q("admin") + ", " + q(HASH_ADMIN) + ");\n")
w("INSERT INTO promotion (id, code, libelle, date_debut, date_fin) VALUES")
w(",\n".join(f"  ({pid}, {q(c)}, {q(l)}, '2026-09-01', '2027-06-30')" for pid,c,l,_n,_a in promotions) + ";\n")
w("INSERT INTO matiere (id, code, libelle) VALUES")
w(",\n".join(f"  ({i}, {q(c)}, {q(l)})" for i,c,l in matieres) + ";\n")
w("INSERT INTO intervenant (id, nom, prenom, email, telephone, statut, tarif_horaire) VALUES")
w(",\n".join(f"  ({i}, {q(n)}, {q(p)}, {q(ascii_(p)+'.'+ascii_(n)+'@experty.example')}, {q(tel())}, {q(s)}, {t if t else 'NULL'})"
             for i,n,p,s,t,_m in intervenants) + ";\n")
w("INSERT INTO salle (numero, libelle, capacite, batiment, videoprojecteur, remarques) VALUES")
w(",\n".join(f"  ({q(a)}, {q(b)}, {c}, {q(d)}, {e}, {q(r)})" for a,b,c,d,e,r in salles) + ";\n")
w("INSERT INTO apprenant (nom, prenom, date_naissance, email, telephone, regime, entreprise, statut, promotion_id) VALUES")
w(",\n".join(f"  ({q(a['nom'])}, {q(a['prenom'])}, '{a['naiss']}', {q(a['email'])}, {q(a['tel'])}, {q(a['regime'])}, {q(a['ent'])}, {q(a['statut'])}, {a['promo']})" for a in apprenants) + ";\n")
w("INSERT INTO intervention (intervenant_id, matiere_id, promotion_id, volume_horaire) VALUES")
w(",\n".join(f"  ({i}, {m}, {p}, {v})" for i,m,p,v in interventions) + ";")
open("sql/03_jeu_demonstration.sql", "w", encoding="utf-8").write("\n".join(out) + "\n")
print(len(apprenants), "apprenants;", len(interventions), "interventions")

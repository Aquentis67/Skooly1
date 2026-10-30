#!/usr/bin/env python3
"""Genere docs/schema.svg (diagramme du schema, utilise par docs/schema.md)."""
import pathlib
T = {
 "promotion":   ("PK id INT", "code VARCHAR(10) UNIQUE", "libelle VARCHAR(100)", "date_debut DATE", "date_fin DATE"),
 "apprenant":   ("PK id INT", "nom VARCHAR(60)", "prenom VARCHAR(60)", "date_naissance DATE", "email VARCHAR(150)", "telephone VARCHAR(20) NULL",
                 "regime ENUM(initial,alternant)", "entreprise VARCHAR(100) NULL", "statut ENUM(inscrit,diplome,sorti)", "FK promotion_id INT"),
 "intervention":("PK id INT", "FK intervenant_id INT", "FK matiere_id INT", "FK promotion_id INT", "volume_horaire DECIMAL(5,2)", "UNIQUE (intervenant, matiere, promotion)"),
 "intervenant": ("PK id INT", "nom VARCHAR(60)", "prenom VARCHAR(60)", "email VARCHAR(150) UNIQUE", "telephone VARCHAR(20) NULL", "statut ENUM(salarie,vacataire)", "tarif_horaire DECIMAL(6,2) NULL", "remarques VARCHAR(500) NULL"),
 "intervenant_matiere": ("PK id INT", "FK intervenant_id INT", "FK matiere_id INT", "UNIQUE (intervenant, matiere)"),
 "matiere":     ("PK id INT", "code VARCHAR(20) UNIQUE", "libelle VARCHAR(100)"),
 "salle":       ("PK id INT", "numero VARCHAR(10) UNIQUE", "libelle VARCHAR(60)", "capacite SMALLINT", "batiment VARCHAR(40)", "videoprojecteur BOOLEAN", "remarques VARCHAR(500) NULL"),
 "utilisateur": ("PK id INT", "identifiant VARCHAR(50) UNIQUE", "mot_de_passe_hash VARCHAR(255)"),
}
POS = {"promotion": (380, 20), "apprenant": (30, 230), "intervention": (380, 230), "intervenant": (730, 230),
       "matiere": (380, 480), "intervenant_matiere": (730, 470), "salle": (30, 500), "utilisateur": (730, 620)}
W, HEAD, ROW = 290, 28, 19
o = ['<svg xmlns="http://www.w3.org/2000/svg" width="1050" height="775" viewBox="0 0 1050 775" font-family="Helvetica, Arial, sans-serif" font-size="12">']
for n, cols in T.items():
    x, y = POS[n]; h = HEAD + ROW * len(cols)
    o.append(f'<rect x="{x}" y="{y}" width="{W}" height="{h}" fill="#fff" stroke="#0c3a6b" stroke-width="1.5" rx="4"/>')
    o.append(f'<rect x="{x}" y="{y}" width="{W}" height="{HEAD}" fill="#0c3a6b" rx="4"/>')
    o.append(f'<text x="{x+W/2}" y="{y+19}" fill="#fff" font-weight="bold" font-size="13" text-anchor="middle">{n.upper()}</text>')
    for i, c in enumerate(cols):
        col = "#c0392b" if c.startswith("FK") else "#222"; b = ' font-weight="bold"' if c.startswith("PK") else ""
        o.append(f'<text x="{x+8}" y="{y+HEAD+ROW*i+14}" fill="{col}"{b}>{c}</text>')
def rel(pts, l1, p1, l2, p2):
    o.append('<polyline points="' + " ".join(f"{a},{b}" for a, b in pts) + '" fill="none" stroke="#ff7a6b" stroke-width="2"/>')
    o.append(f'<text x="{p1[0]}" y="{p1[1]}" font-weight="bold" fill="#0c3a6b">{l1}</text><text x="{p2[0]}" y="{p2[1]}" font-weight="bold" fill="#0c3a6b">{l2}</text>')
rel([(380, 70), (175, 70), (175, 230)], "1", (362, 62), "0..N", (181, 222))             # promotion - apprenant
rel([(525, 20 + HEAD + ROW * 5), (525, 230)], "1", (531, 150), "0..N", (531, 222))       # promotion - intervention
rel([(730, 300), (670, 300)], "1", (714, 292), "0..N", (676, 292))                       # intervenant - intervention
rel([(525, 480), (525, 230 + HEAD + ROW * 6)], "1", (531, 472), "0..N", (531, 392))      # matiere - intervention
rel([(875, 230 + HEAD + ROW * 8), (875, 470)], "1", (881, 424), "0..N", (881, 462))      # intervenant - intervenant_matiere
rel([(670, 520), (730, 520)], "1", (676, 512), "0..N", (700, 512))                       # matiere - intervenant_matiere
o.append('<text x="30" y="725" fill="#555">SALLE et UTILISATEUR n’ont aucune relation avec les autres tables (planification et rôles hors périmètre de SkoolY 1).</text>')
o.append('<text x="30" y="742" fill="#555">INTERVENANT_MATIERE : matières qu’un intervenant peut enseigner (RG03). INTERVENTION : intervenant + matière + promotion + volume (RG06).</text>')
o.append('<text x="30" y="759" fill="#555">Clés étrangères : RESTRICT partout (RG09), sauf INTERVENANT_MATIERE (CASCADE : simple association).</text>')
o.append('</svg>')
pathlib.Path(__file__).resolve().parent.parent.joinpath("docs", "schema.svg").write_text("\n".join(o), encoding="utf-8")

# Reprise des données du classeur — rapport de nettoyage

Source : `ressources-skooly.xlsx` · généré par `tools/xlsx_vers_sql.py` (rejouable).

## Résultat

| Ensemble | Lignes du classeur | Lignes en base |
|---|---|---|
| Apprenants | 105 | 105 |
| Promotions | (déduites des apprenants) | 8 |
| Intervenants | 22 | 21 (doublon fusionné) |
| Matières | 11 | 11 |
| Salles | 5 | 5 |
| Liens intervenant–matière | (colonne « matieres ») | 28 |

## Ce qui a été fait, catégorie par catégorie

### ⚠ Dates impossibles corrigées (hypothèse à valider) (1)

- Ferreira Ana : « 2007-13-05 » → 13/05/2007 (jour et mois inversés)

### ⚠ Données absentes du classeur (hypothèses à valider) (3)

- Promotions : le classeur ne donne que le code. Libellés repris du sujet ; dates de début/fin supposées 01/09/2025 → 30/06/2026 (le planning du classeur commence le 01/09/2025).
- Salles : libellé généré (« Salle 101 ») et bâtiment « Non renseigné » (colonnes absentes du classeur, obligatoires en base) : à compléter dans l'application.
- Apprenants : le régime est déduit de l'entreprise (renseignée → alternant, vide → initial) ; le statut de scolarité est « inscrit » pour tous.

### ⚠ E-mails corrigés (hypothèse à valider) (1)

- Fischer Julie : « j.fischer@exemple » → « j.fischer@exemple.fr » (extension .fr manquante, comme tous les autres)

### ⚠ Tarifs particuliers (hypothèse à valider) (4)

- Vogel Anne : « a revoir » → 0,00 €/h avec remarque « à renseigner » (un vacataire doit avoir un tarif)
- Moreau Alain : « vide » → 0,00 €/h avec remarque « à renseigner » (un vacataire doit avoir un tarif)
- Blanc Pierre : « Gratuit » → 0,00 €/h (intervenant ponctuel)
- Caron Lea : « vide » → 0,00 €/h avec remarque « à renseigner » (un vacataire doit avoir un tarif)

### ⚠ Vidéoprojecteur : valeur absente (hypothèse à valider) (1)

- Salle 103 : case vide mais remarque « videoproj HS depuis fevrier » → il y a un vidéoprojecteur (en panne) → oui

### Accents restaurés sur les libellés de matières (4)

- « Comptabilite generale » → « Comptabilité générale »
- « Comptabilite analytique » → « Comptabilité analytique »
- « Fiscalite » → « Fiscalité »
- « Mathematiques financieres » → « Mathématiques financières »

### Capacités converties en entiers (2)

- Salle 102 : « 25 places » → 25
- Salle 103 : « ~30 » → 30 (≈ approximatif dans le classeur)

### Casse et espaces des noms/prénoms (40)

- « NGUYEN » → « Nguyen »
- « ostermann » → « Ostermann »
- « claire » → « Claire »
- « HOFFMANN » → « Hoffmann »
- « AMANDINE » → « Amandine »
- « marchand » → « Marchand »
- « martinez » → « Martinez »
- « HORVATH » → « Horvath »
- « ROUSSEAU » → « Rousseau »
- « RODRIGUES » → « Rodrigues »
- « COSTA » → « Costa »
- « RENAUD » → « Renaud »
- « kovacs » → « Kovacs »
- « keita » → « Keita »
- « FALL » → « Fall »
- « BELKACEM » → « Belkacem »
- « barbier » → « Barbier »
- « ROUSSEAU » → « Rousseau »
- « pires » → « Pires »
- « MARTINEZ » → « Martinez »
- « LAMBERT » → « Lambert »
- «  Traore  » → « Traore »
- « FARKAS » → « Farkas »
- « ferreira » → « Ferreira »
- « morel » → « Morel »
- « BENALI » → « Benali »
- « novak » → « Novak »
- « MARQUES » → « Marques »
- « HORVATH » → « Horvath »
- « ferreira » → « Ferreira »
- « MICHEL » → « Michel »
- « KELLER » → « Keller »
- « roth » → « Roth »
- « BARRY » → « Barry »
- « DA SILVA » → « Da Silva »
- « bauer » → « Bauer »
- « LOPEZ » → « Lopez »
- « N'DIAYE » → « N'Diaye »
- « schneider » → « Schneider »
- « MOREL » → « Morel »

### Colonnes non reprises (2)

- Matières : « heures B1 » et « heures B2 » ne sont pas stockées sur la matière (RG06 : le volume dépend de la promotion, il vit dans `intervention`).
- Onglet « Planification » : hors périmètre de SkoolY 1 (ni planning, ni séances).

### Colonnes reprises dans un nouveau champ (2)

- Intervenants : « remarques » → colonne `intervenant.remarques` (ajoutée au schéma).
- Intervenants : « matieres » → table `intervenant_matiere` (ajoutée au schéma, RG03).

### Dates converties au format unique (41)

- Marchand Salome : « 20-02-2008 »
- Sow Anais : « 21/8/2006 »
- Martinez Camille : « 1/11/2002 »
- Costa Elodie : « 5/10/2003 »
- Renaud Mehdi : « 14/9/2006 »
- Kovacs Julie : « 26/9/2005 »
- Ferreira Tom : « 17/6/2003 »
- Keita Laura : « 2007-04-27 »
- Nguyen Eva : « 2002-12-15 »
- Kovacs Zoe : « 21/1/2002 »
- Dumont Emma : « 27-07-2001 »
- Pires Emma : « 2008-10-20 »
- Garcia Celia : « 12/4/2006 »
- Wagner Mehdi : « 2004-05-11 »
- Hartmann Oceane : « 3/7/2006 »
- Noel Baptiste : « 28/2/2001 »
- Pires Fatou : « 27/6/2007 »
- Caron Paul : « 1/6/1999 »
- Noel Manon : « 2/3/2006 »
- Benali Raphael : « 2004-09-10 »
- Horvath Anais : « 21/7/2001 »
- Novak Chloe : « 14/7/2006 »
- Ferreira Baptiste : « 2004-10-16 »
- Michel Alexis : « 2007-04-05 »
- Keller Thomas : « 2001-07-22 »
- Diallo Fatou : « 9/5/2000 »
- Lambert Chloe : « 02-09-2002 »
- Barry Aissatou : « 2003-01-15 »
- Vogel Sophie : « 7/7/1997 »
- Da Silva Pedro : « 1999-11-30 »
- Weber Emma : « 8/6/2003 »
- Bauer Lucas : « 17-09-2000 »
- Fischer Hugo : « 2004-03-19 »
- Zimmermann Noah : « 5/1/2002 »
- Koch Mathis : « 26-10-1997 »
- N'Diaye Moussa : « 2001-02-08 »
- Ruiz Ana : « 19/8/2002 »
- Schneider Tom : « 02-12-1999 »
- Morel Adam : « 2000-06-24 »
- Haddad Nour : « 7/9/2001 »
- Dubois Clara : « 23-01-2002 »

### Doublons fusionnés (1)

- Ostermann Claire (c.ostermann@exemple.fr) : 2 lignes → 1 (tarifs « 52 / 55 » : le plus récent « 55 » est conservé, il est annoté « tarif revu en janvier » ; matières regroupées)

### E-mails passés en minuscules (2)

- Ostermann Claire
- Hoffmann Amandine

### Oui/non normalisés (1)

- Salle 102 : « O » → oui

### Statuts corrigés (1)

- Nguyen Lan : « salariee » → « salarie »

### Tarifs convertis en nombres (14)

- Ostermann Claire : « 55 » → 55.00
- Wurtz Jean-Marc : « 48,00 € » → 48.00
- Haas Sophie : « 60 €/h » → 60.00
- Kieffer Michel : « 45 » → 45.00
- Schaeffer Luc : « 50 » → 50.00
- Perrot Denis : « 55 » → 55.00
- Lorentz Eva : « 55 » → 55.00
- Diallo Amadou : « 49 » → 49.00
- Klein Sarah : « 53 » → 53.00
- Weber Thomas : « 51 » → 51.00
- Fischer Julie : « 46 » → 46.00
- Girard Nadia : « 54 » → 54.00
- Sow Fatou : « 52 » → 52.00
- Renaud Marc : « 58 » → 58.00

### Téléphones normalisés (format 06 12 34 56 78) (80)

- Nguyen Lan
- Ostermann Claire
- Wurtz Jean-Marc
- Haas Sophie
- Blanc Pierre
- Hoffmann Amandine
- Marchand Salome
- Bertrand Manon
- Martinez Camille
- Horvath Pauline
- Marchand Paul
- Rousseau Eva
- Rodrigues Alexis
- Renaud Mehdi
- Roux Quentin
- Kovacs Julie
- Faure Chloe
- Garnier Alexis
- Ferreira Tom
- Keita Laura
- Fall Nathan
- Belkacem Clara
- Marchand Clement
- Ziani Zoe
- Slimani Lina
- Nguyen Eva
- Barbier Mehdi
- Kovacs Zoe
- Rousseau Awa
- Bertrand Jade
- Schmitt Mehdi
- Pires Emma
- Garcia Celia
- Martinez Nour
- Martinez Maeva
- Lambert Enzo
- Kovacs Elodie
- Durand Ines
- Sanchez Maeva
- Wagner Mehdi
- Farkas Romain
- Noel Baptiste
- Novak Alice
- Garnier Nour
- Caron Paul
- Ferreira Laura
- Camara Celia
- Morel Emma
- Moreau Nathan
- Muller Salome
- Laurent Hugo
- Noel Manon
- Benali Raphael
- Horvath Anais
- Robin Hugo
- Marques Samuel
- Horvath Louis
- Hamdi Elodie
- Bauer Dylan
- Ferreira Baptiste

# SkoolY 1 — Application de gestion du Campus ExpertY

PHP 8 procédural · PDO · MySQL · Bootstrap 5 (servi localement) · aucun framework PHP.
Installation pas à pas : `docs/installation.pdf`. Schéma et justifications : `docs/schema.pdf`.

## Structure

| Dossier | Contenu |
|---|---|
| `public/` | **Seul dossier servi.** 1 fichier par page, `assets/css` (Bootstrap + `skooly.css`, l'unique surcharge), `assets/js` |
| `includes/` | `db.php` (connexion PDO) · `session.php` (contrôle de session, 1re ligne de chaque page interne) · `validation.php` · `fonctions_<ensemble>.php` (tout le SQL) · `helpers.php`, `formulaire.php`, `layout.php`, `vues.php` |
| `config/` | `config.example.php` versionné ; `config.php` (mot de passe) **jamais** versionné |
| `sql/` | `01_creation_base` · `02_creation_compte` · `03_jeu_demonstration` |
| `tools/` | `generer_jeu_demo.py` : génère un jeu de données **fictif** (à remplacer par le contenu réel de `ressources.xlsx`) |

## Conventions de nommage des pages

`xxx.php` (liste) · `xxx_form.php` (création **et** modification) · `xxx_supprimer.php` (confirmation + suppression) · `promotion.php`, `intervenant.php` (fiches).

## Aide-mémoire pour la démonstration (10 min)

| Étape | Où |
|---|---|
| Connexion / page interne sans session | `apprenants.php` tapé à la main → `login.php` |
| Création + modification d'un apprenant | `apprenant_form.php` |
| Contrôles de saisie (formulaire vide, e-mail sans `@`, 31/02/2005) | `apprenant_form.php` |
| Recherche 3 lettres + filtres + tri + pagination | `apprenants.php` ; test du tri inconnu : `apprenants.php?tri=nimportequoi` |
| Suppression refusée (RG09) | `promotion_supprimer.php?id=1` |
| Promotion : apprenants, interventions, total d'heures | `promotion.php?id=1` |
| Échappement | créer un apprenant « D'Alembert & Fils » |
| Déconnexion + bouton Retour | `logout.php` |

Sauvegardez la base avant de passer : `mysqldump -u root -p skooly > sauvegarde.sql`.

## Travail d'équipe

Dépôt Git privé, commits réguliers de **chaque** membre, branches par fonctionnalité si possible. Chacun doit savoir expliquer toutes les parties : relisez-vous mutuellement le code.

## Style « cahier / crayon »

| Élément | Origine |
|---|---|
| Panneaux dessinés à la main (`<wired-card>`) | [wired-elements](https://github.com/rough-stuff/wired-elements) (MIT, basé sur RoughJS et Lit) |
| Bundle local | `public/assets/js/wired.bundle.js` (30 Ko), construit depuis `tools/wired/entry.js` |
| Polices Patrick Hand et Permanent Marker | `public/assets/fonts/` (licence OFL), servies en local |
| Tout le reste du style | `public/assets/css/skooly.css` — **le seul fichier de surcharge** de Bootstrap |
| Licences des composants tiers | `public/assets/vendor/THIRD_PARTY_LICENSES.txt` (à conserver) |

**Pourquoi seulement `wired-card` et pas `wired-input` / `wired-button` ?** Ces composants cachent leur `<input>` dans un shadow DOM et ne sont pas associés aux `<form>` : leurs valeurs ne partiraient pas en POST, et le sujet interdit que JavaScript porte une fonction essentielle. Les vrais champs, boutons et tableaux restent donc natifs, habillés en CSS pur. Sans JavaScript, les panneaux gardent un trait de repli en CSS.

Pour changer le style des panneaux : `carte_debut()` / `carte_fin()` dans `includes/vues.php`. N'ajoutez pas l'attribut `fill` à `<wired-card>` : cette version de wired-elements est incompatible avec RoughJS 4.6 (le dessin plante). Le CSS garantit que le contenu reste visible même si le dessin échoue.

Reconstruire le bundle (Node.js requis, uniquement si vous modifiez `entry.js`) :

```
npm i wired-elements esbuild
npx esbuild tools/wired/entry.js --bundle --minify --format=iife --target=es2019 --legal-comments=none --outfile=public/assets/js/wired.bundle.js
```

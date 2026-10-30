---
title: "SkoolY 1 — Procédure d'installation"
subtitle: "Campus ExpertY · Projet 2.1 SLAM"
---

# 1. Logiciels à installer

| Logiciel | Version | Remarque |
|---|---|---|
| Serveur local tout-en-un : **XAMPP**, **WAMP** ou **Laragon** | PHP **8.0 ou plus**, MySQL 8 ou MariaDB 10.5+ | Installe Apache, PHP, MySQL et phpMyAdmin |
| Navigateur récent | Chrome, Firefox, Edge | |

Extensions PHP nécessaires : `pdo_mysql` (activée par défaut). `mbstring` est utilisée si elle est présente mais n'est pas obligatoire.
Aucun framework PHP ni gestionnaire de dépendances : **rien d'autre à installer** (Bootstrap est déjà dans `public/assets/`).

# 2. Récupérer le code

Décompressez l'archive (ou clonez le dépôt Git) dans un dossier de votre choix, par exemple `C:\skooly` ou `~/skooly`. Vous obtenez :

```
skooly/
├── config/      config.example.php   (modèle ; config.php n'est jamais versionné)
├── includes/    fonctions PHP, connexion, validation, session
├── public/      pages, CSS, JavaScript : SEUL dossier servi par le serveur web
├── sql/         01_creation_base.sql, 02_creation_compte.sql, 03_jeu_demonstration.sql, 04_interventions_illustratives.sql
├── tools/       reprise du classeur Excel (xlsx_vers_sql.py), génération du schéma
└── docs/
```

# 3. Créer la base (dans cet ordre)

Démarrez **Apache** et **MySQL** depuis le panneau de votre serveur local.

**Avant l'étape 2 ci-dessous**, ouvrez `sql/02_creation_compte.sql` dans un éditeur et remplacez `REMPLACER_PAR_UN_MOT_DE_PASSE` par un mot de passe de votre choix. Notez-le : il servira à l'étape 4.

| Ordre | Script | Rôle | Compte à utiliser |
|---|---|---|---|
| 1 | `sql/01_creation_base.sql` | Supprime la base `skooly` si elle existe et la recrée (tables, contraintes, clés étrangères) | administrateur (`root`) |
| 2 | `sql/02_creation_compte.sql` | Crée le compte `skooly_app` : droits SELECT, INSERT, UPDATE, DELETE **sur la base skooly uniquement** | administrateur (`root`) |
| 3 | `sql/03_jeu_demonstration.sql` | Charge les promotions, apprenants, intervenants, matières, salles, interventions et le compte de démonstration | administrateur (`root`) |

**Avec phpMyAdmin** : onglet *Importer*, choisir le fichier, *Exécuter* — pour chacun des scripts, dans l'ordre.

**En ligne de commande** :

```
mysql -u root -p < sql/01_creation_base.sql
mysql -u root -p < sql/02_creation_compte.sql
mysql -u root -p --default-character-set=utf8mb4 < sql/03_jeu_demonstration.sql
mysql -u root -p < sql/04_interventions_illustratives.sql    # optionnel
```

| 4 *(optionnel)* | `sql/04_interventions_illustratives.sql` | Charge des interventions **d'exemple** (le classeur n'en contient pas) pour que la page *Promotion* affiche des heures | administrateur (`root`) |

Le script 03 contient les données **réelles** du classeur du Campus, nettoyées (105 apprenants, 8 promotions, 21 intervenants, 11 matières, 5 salles). Le rapport de nettoyage est dans `docs/reprise_des_donnees.md`.

Les scripts 01, 03 et 04 sont **rejouables** : pour repartir d'une base propre, rejouez 01, puis 03 (et 04 si vous le souhaitez). Le compte du script 02 survit et n'a pas besoin d'être recréé.

# 4. Renseigner les identifiants de la base

1. Copiez `config/config.example.php` en `config/config.php`.
2. Ouvrez `config/config.php` et remplacez `A_RENSEIGNER` par le mot de passe choisi à l'étape 3 (script 02). Les autres valeurs conviennent si MySQL tourne sur `localhost:3306`.

`config.php` est exclu de Git (`.gitignore`) : ne le publiez jamais.

# 5. Lancer l'application

Le serveur web ne doit servir **que** le dossier `public/`.

* **Méthode la plus simple (PHP intégré)** — dans un terminal, depuis le dossier `skooly` :
  ```
  php -S localhost:8000 -t public
  ```
  puis ouvrir <http://localhost:8000/login.php>. (Sous XAMPP/WAMP/Laragon, utilisez l'exécutable `php` fourni par le pack si `php` n'est pas dans le PATH.)
* **Avec Apache** — faites pointer le `DocumentRoot` d'un hôte virtuel sur `skooly/public` (Laragon le fait automatiquement : placez `skooly` dans `www`, l'adresse devient `http://skooly.test/`).

# 6. Se connecter

| | |
|---|---|
| Identifiant | `admin` |
| Mot de passe | `Skooly2026!` |

Ce compte est créé par le script 03 (jamais par une page web). Le mot de passe est stocké sous forme d'empreinte `password_hash`.

# 7. Vérifier que tout fonctionne

* Taper `http://localhost:8000/apprenants.php` sans être connecté renvoie vers la page de connexion.
* Après connexion, le tableau de bord affiche : **105** apprenants, **8** promotions, **21** intervenants, **11** matières, **5** salles (et **24** interventions si le script 04 a été joué).

# 8. En cas de problème

| Symptôme | Cause probable |
|---|---|
| « Une erreur est survenue » dès la connexion | Mot de passe de `config/config.php` différent de celui du script 02, ou MySQL arrêté. Passez `'debug' => true` dans `config.php` pour voir le détail (jamais en production). |
| Accents illisibles (`Ã©`) | Le script 03 a été importé sans UTF-8 : rejouer 01 puis 03 avec `--default-character-set=utf8mb4` ou l'import phpMyAdmin (UTF-8). |
| « Unknown database skooly » | Le script 01 n'a pas été exécuté. |
| `Access denied for user 'skooly_app'` | Le script 02 n'a pas été exécuté, ou le mot de passe de `config.php` est faux. |
| Page blanche ou 404 sous Apache | Le `DocumentRoot` ne pointe pas sur `public/`. |

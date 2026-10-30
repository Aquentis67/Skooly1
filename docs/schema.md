---
title: "SkoolY 1 — Schéma de données"
subtitle: "Campus ExpertY · Projet 2.1 SLAM"
---

# 1. Vue d'ensemble

La base **skooly** (MySQL / MariaDB, moteur **InnoDB**, jeu de caractères **utf8mb4**, collation `utf8mb4_unicode_ci`) contient 8 tables. Chaque table a une clé primaire technique auto-incrémentée `id`, chaque relation est portée par une clé étrangère, et les contraintes (`UNIQUE`, `CHECK`, `NOT NULL`, `ENUM`) empêchent réellement d'enregistrer une donnée absurde, même si l'application était contournée.

![Schéma relationnel](schema.svg)

# 2. Description des tables

## promotion

| Colonne | Type | Contraintes |
|---|---|---|
| id | INT UNSIGNED | PK, auto-incrément |
| code | VARCHAR(10) | NOT NULL, UNIQUE (ex. « DCG 1 ») |
| libelle | VARCHAR(100) | NOT NULL |
| date_debut | DATE | NOT NULL |
| date_fin | DATE | NOT NULL, CHECK `date_fin > date_debut` |

## apprenant

| Colonne | Type | Contraintes |
|---|---|---|
| id | INT UNSIGNED | PK, auto-incrément (RG02) |
| nom, prenom | VARCHAR(60) | NOT NULL |
| date_naissance | DATE | NOT NULL (RG08) |
| email | VARCHAR(150) | NOT NULL, **non unique** (RG02) |
| telephone | VARCHAR(20) | NULL autorisé |
| regime | ENUM('initial','alternant') | NOT NULL, défaut `initial` |
| entreprise | VARCHAR(100) | NULL ; CHECK : obligatoire si `regime = 'alternant'` |
| statut | ENUM('inscrit','diplome','sorti') | NOT NULL, défaut `inscrit` |
| promotion_id | INT UNSIGNED | NOT NULL, FK → promotion(id), RESTRICT (RG01) |

## intervenant

| Colonne | Type | Contraintes |
|---|---|---|
| id | INT UNSIGNED | PK, auto-incrément |
| nom, prenom | VARCHAR(60) | NOT NULL |
| email | VARCHAR(150) | NOT NULL, **UNIQUE** (RG07) |
| telephone | VARCHAR(20) | NULL autorisé |
| statut | ENUM('salarie','vacataire') | NOT NULL (RG04) |
| tarif_horaire | DECIMAL(6,2) | NULL ; CHECK : `NULL` pour un salarié, `>= 0` pour un vacataire (RG04, RG08) — 0 = gratuit |
| remarques | VARCHAR(500) | NULL autorisé (décision 20) |

## matiere

| Colonne | Type | Contraintes |
|---|---|---|
| id | INT UNSIGNED | PK, auto-incrément |
| code | VARCHAR(20) | NOT NULL, UNIQUE (ex. « COMPTA-GEN », « INFO-BUREAU ») |
| libelle | VARCHAR(100) | NOT NULL |

*Pas de volume horaire ici : il dépend de la promotion (RG06).*

## salle

| Colonne | Type | Contraintes |
|---|---|---|
| id | INT UNSIGNED | PK, auto-incrément |
| numero | VARCHAR(10) | NOT NULL, UNIQUE |
| libelle | VARCHAR(60) | NOT NULL |
| capacite | SMALLINT UNSIGNED | NOT NULL, CHECK `capacite > 0` |
| batiment | VARCHAR(40) | NOT NULL |
| videoprojecteur | BOOLEAN | NOT NULL, défaut 0 (RG08) |
| remarques | VARCHAR(500) | NULL autorisé (ajout de l'équipe, voir décision 17) |

## intervenant_matiere

| Colonne | Type | Contraintes |
|---|---|---|
| id | INT UNSIGNED | PK, auto-incrément |
| intervenant_id | INT UNSIGNED | NOT NULL, FK → intervenant(id), **CASCADE** |
| matiere_id | INT UNSIGNED | NOT NULL, FK → matiere(id), **CASCADE** |
| | | UNIQUE (intervenant_id, matiere_id) |

*Matières qu'un intervenant peut enseigner (RG03) : une compétence, sans promotion ni volume.*

## intervention

| Colonne | Type | Contraintes |
|---|---|---|
| id | INT UNSIGNED | PK, auto-incrément |
| intervenant_id | INT UNSIGNED | NOT NULL, FK → intervenant(id), RESTRICT |
| matiere_id | INT UNSIGNED | NOT NULL, FK → matiere(id), RESTRICT |
| promotion_id | INT UNSIGNED | NOT NULL, FK → promotion(id), RESTRICT |
| volume_horaire | DECIMAL(5,2) | NOT NULL, CHECK `> 0` (RG06, RG08) |
| | | UNIQUE (intervenant_id, matiere_id, promotion_id) |

## utilisateur

| Colonne | Type | Contraintes |
|---|---|---|
| id | INT UNSIGNED | PK, auto-incrément |
| identifiant | VARCHAR(50) | NOT NULL, UNIQUE |
| mot_de_passe_hash | VARCHAR(255) | NOT NULL — empreinte `password_hash()`, jamais le mot de passe |

# 3. Relations

| Relation | Cardinalité | Règle |
|---|---|---|
| promotion — apprenant | 1 — 0..N | RG01 : un apprenant a une promotion et une seule ; une promotion peut être vide |
| intervenant — intervention | 1 — 0..N | RG05 : un intervenant peut n'avoir aucune intervention |
| matiere — intervention | 1 — 0..N | |
| promotion — intervention | 1 — 0..N | |
| intervenant — matiere | N — N (via `intervenant_matiere`) | RG03 : l'intervenant peut enseigner plusieurs matières, une matière peut avoir plusieurs intervenants |

# 4. Justification des décisions non imposées (RG10)

| # | Décision | Justification |
|---|---|---|
| 1 | Clé primaire technique `INT UNSIGNED AUTO_INCREMENT` partout | Imposé par le sujet ; évite d'utiliser un code ou un e-mail comme identité (RG02). |
| 2 | **Le volume horaire est sur `intervention`**, pas sur `matiere` | Le volume dépend de la promotion *et* de l'intervenant (RG06). Le volume d'une matière dans une promotion est la **somme** de ses interventions. |
| 3 | **Table `intervenant_matiere` (N-N) distincte de `intervention`** | RG03 déclare une compétence (« peut enseigner »), alors que `intervention` est une attribution (promotion + volume). Le classeur du Campus le confirme : sa colonne « matières » liste les matières de chaque intervenant sans promotion ni heures. *(Version initiale du schéma : ce lien était déduit des interventions ; corrigé après lecture du classeur.)* |
| 4 | `UNIQUE (intervenant, matière, promotion)` sur `intervention` ; créer une intervention déclare aussi le lien dans `intervenant_matiere` | Interdit le même triplet deux fois ; enseigner une matière suppose de pouvoir l'enseigner, donc les deux tables restent cohérentes. |
| 5 | `regime`, `statut` en **ENUM** (et non table de référence) | Listes courtes et figées par le cahier des charges ; la base refuse toute autre valeur. Ajouter une valeur demandera un `ALTER TABLE`. |
| 6 | `CHECK` sur `entreprise` (alternant) et `tarif_horaire` (vacataire / salarié) | La règle vit dans la base : aucune page, script ou saisie dans phpMyAdmin ne peut la contourner. |
| 7 | `apprenant.email` **non unique**, `intervenant.email` **unique** | RG02 (e-mail de parent partagé) et RG07. |
| 8 | `code` unique sur promotion et matière, `numero` unique sur salle | Ce sont les identifiants « métier » affichés ; deux salles « 101 » n'auraient aucun sens. |
| 9 | `salle.numero` en **VARCHAR** et non en entier | Un numéro comme « 012 » perdrait son zéro ; ce n'est pas une quantité. |
| 10 | `telephone` en **VARCHAR(20)**, format normalisé `06 12 34 56 78`, facultatif | Un téléphone n'est pas un nombre (zéro initial, +33). Facultatif : une donnée absente ne doit pas bloquer une inscription. |
| 11 | `tarif_horaire DECIMAL(6,2)`, `volume_horaire DECIMAL(5,2)` | RG08 : jamais de texte ni de flottant pour un montant. Le volume accepte les demi-heures (12,5 h). |
| 12 | Toutes les clés étrangères en `ON DELETE RESTRICT` | RG09 : la base refuse la suppression même si l'application était contournée ; l'application affiche en plus un message compréhensible. |
| 13 | `CHECK date_fin > date_debut` sur promotion | Une promotion ne peut pas finir avant d'avoir commencé. |
| 14 | `mot_de_passe_hash VARCHAR(255)` | `password_hash` peut produire des empreintes plus longues si l'algorithme par défaut évolue. |
| 15 | `utf8mb4_unicode_ci` | Accents, apostrophes et symboles (« Zoé », « O'Neil & Fils ») ; recherche insensible à la casse et aux accents. |
| 16 | Hormis la décision 17, aucune colonne ajoutée au-delà du minimum du sujet | Le périmètre de SkoolY 1 est volontairement petit ; les phases suivantes enrichiront le schéma. |
| 17 | Ajout de `salle.remarques` `VARCHAR(500) NULL` | Besoin du Campus : noter l'état du matériel ou une particularité d'accès. Facultatif (NULL si vide, jamais une chaîne vide) ; 500 caractères maximum, contrôlés côté serveur ; une base existante se met à jour avec `sql/migrations/001_salle_remarques.sql`. |
| 18 | `matiere.code` en `VARCHAR(20)` (et non 10) | Le classeur contient « INFO-BUREAU » (11 caractères). |
| 19 | `tarif_horaire >= 0` pour un vacataire (0 autorisé) | Le classeur contient un intervenant « Gratuit » (conférence) et des vacataires dont le tarif est inconnu : un tarif à 0 € signalé en remarque évite d'inventer une valeur et de contourner RG04. |
| 20 | Ajout de `intervenant.remarques` `VARCHAR(500) NULL` | Le classeur a une colonne « remarques » pour les intervenants (« parti en 2025 », « tarif revu en janvier »…). |
| 21 | `intervenant_matiere` en `ON DELETE CASCADE`, les autres clés en `RESTRICT` | Ce n'est qu'une association : supprimer une matière ou un intervenant sans intervention retire simplement le lien (l'application le signale avant). |

# 5. Décisions applicatives (hors base)

| Décision | Valeur retenue |
|---|---|
| Taille de page des listes | 15 lignes (10 pour les listes intégrées à une fiche) |
| Recherche | Dès 3 caractères, sur nom, prénom et e-mail (« contient ») ; en dessous de 3, un avertissement s'affiche et la recherche est ignorée |
| Filtres des apprenants | Promotion, régime, statut — combinables entre eux et avec la recherche |
| Saisie des dates | Texte `JJ/MM/AAAA` (le sélecteur natif empêcherait de démontrer le contrôle « 31 février ») ; stockage `AAAA-MM-JJ` |
| Âge minimal d'un apprenant | 14 ans ; année de naissance minimale 1940 |
| Suppression | Page de confirmation côté serveur (fonctionne sans JavaScript), requête `POST` avec jeton CSRF |
| Jeton CSRF | Sur tous les formulaires `POST` (au-delà du minimum du sujet, pour un coût très faible) |
| Messages d'erreur de connexion | Identiques que l'identifiant existe ou non |

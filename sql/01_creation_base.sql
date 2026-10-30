-- ============================================================================
-- SkoolY 1 - Script de creation de la base (REJOUABLE)
-- Supprime la base 'skooly' si elle existe, puis la recree entierement.
-- A executer avec un compte administrateur (ex. root).
-- ============================================================================
DROP DATABASE IF EXISTS skooly;
CREATE DATABASE skooly CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE skooly;

-- ---------------------------------------------------------------- promotion
CREATE TABLE promotion (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    code        VARCHAR(10)  NOT NULL,
    libelle     VARCHAR(100) NOT NULL,
    date_debut  DATE         NOT NULL,
    date_fin    DATE         NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_promotion_code (code),
    CONSTRAINT ck_promotion_dates CHECK (date_fin > date_debut)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------- apprenant
-- RG01 : une promotion et une seule (promotion_id NOT NULL)
-- RG02 : identifie par id, l'email n'est PAS unique
CREATE TABLE apprenant (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nom             VARCHAR(60)  NOT NULL,
    prenom          VARCHAR(60)  NOT NULL,
    date_naissance  DATE         NOT NULL,
    email           VARCHAR(150) NOT NULL,
    telephone       VARCHAR(20)  NULL,
    regime          ENUM('initial','alternant')     NOT NULL DEFAULT 'initial',
    entreprise      VARCHAR(100) NULL,
    statut          ENUM('inscrit','diplome','sorti') NOT NULL DEFAULT 'inscrit',
    promotion_id    INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    KEY idx_apprenant_promotion (promotion_id),
    KEY idx_apprenant_nom (nom, prenom),
    CONSTRAINT fk_apprenant_promotion FOREIGN KEY (promotion_id)
        REFERENCES promotion (id) ON DELETE RESTRICT,
    -- Un alternant a obligatoirement une entreprise d'accueil
    CONSTRAINT ck_apprenant_entreprise CHECK
        (regime <> 'alternant' OR (entreprise IS NOT NULL AND entreprise <> ''))
) ENGINE=InnoDB;

-- -------------------------------------------------------------- intervenant
-- RG04 : tarif obligatoire pour un vacataire (0 = gratuit/benevole), NULL pour un salarie
-- RG07 : email unique
CREATE TABLE intervenant (
    id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nom            VARCHAR(60)  NOT NULL,
    prenom         VARCHAR(60)  NOT NULL,
    email          VARCHAR(150) NOT NULL,
    telephone      VARCHAR(20)  NULL,
    statut         ENUM('salarie','vacataire') NOT NULL,
    tarif_horaire  DECIMAL(6,2) NULL,
    remarques      VARCHAR(500) NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_intervenant_email (email),
    KEY idx_intervenant_nom (nom, prenom),
    CONSTRAINT ck_intervenant_tarif CHECK (
        (statut = 'salarie'   AND tarif_horaire IS NULL) OR
        (statut = 'vacataire' AND tarif_horaire IS NOT NULL AND tarif_horaire >= 0))
) ENGINE=InnoDB;

-- ------------------------------------------------------------------ matiere
-- RG06 : pas de volume horaire ici (il depend de la promotion)
CREATE TABLE matiere (
    id       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    code     VARCHAR(20)  NOT NULL,
    libelle  VARCHAR(100) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_matiere_code (code)
) ENGINE=InnoDB;

-- -------------------------------------------------------------------- salle
CREATE TABLE salle (
    id               INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    numero           VARCHAR(10)       NOT NULL,
    libelle          VARCHAR(60)       NOT NULL,
    capacite         SMALLINT UNSIGNED NOT NULL,
    batiment         VARCHAR(40)       NOT NULL,
    videoprojecteur  BOOLEAN           NOT NULL DEFAULT 0,
    remarques        VARCHAR(500)      NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_salle_numero (numero),
    CONSTRAINT ck_salle_capacite CHECK (capacite > 0)
) ENGINE=InnoDB;

-- ------------------------------------------------------- intervenant_matiere
-- RG03 : "un intervenant peut enseigner plusieurs matieres, une matiere peut etre
-- enseignee par plusieurs intervenants" (competence declaree, SANS promotion ni
-- volume : c'est ce que contient le classeur). CASCADE : ce n'est qu'une association.
CREATE TABLE intervenant_matiere (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    intervenant_id  INT UNSIGNED NOT NULL,
    matiere_id      INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_intervenant_matiere (intervenant_id, matiere_id),
    KEY idx_intervenant_matiere_matiere (matiere_id),
    CONSTRAINT fk_im_intervenant FOREIGN KEY (intervenant_id)
        REFERENCES intervenant (id) ON DELETE CASCADE,
    CONSTRAINT fk_im_matiere FOREIGN KEY (matiere_id)
        REFERENCES matiere (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------- intervention
-- Une intervention = un intervenant, une matiere, une promotion + un volume.
-- RG06 : le volume horaire depend du triplet (intervenant, matiere, promotion)
CREATE TABLE intervention (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    intervenant_id  INT UNSIGNED NOT NULL,
    matiere_id      INT UNSIGNED NOT NULL,
    promotion_id    INT UNSIGNED NOT NULL,
    volume_horaire  DECIMAL(5,2) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_intervention_triplet (intervenant_id, matiere_id, promotion_id),
    KEY idx_intervention_matiere (matiere_id),
    KEY idx_intervention_promotion (promotion_id),
    CONSTRAINT fk_intervention_intervenant FOREIGN KEY (intervenant_id)
        REFERENCES intervenant (id) ON DELETE RESTRICT,
    CONSTRAINT fk_intervention_matiere FOREIGN KEY (matiere_id)
        REFERENCES matiere (id) ON DELETE RESTRICT,
    CONSTRAINT fk_intervention_promotion FOREIGN KEY (promotion_id)
        REFERENCES promotion (id) ON DELETE RESTRICT,
    CONSTRAINT ck_intervention_volume CHECK (volume_horaire > 0)
) ENGINE=InnoDB;

-- -------------------------------------------------------------- utilisateur
CREATE TABLE utilisateur (
    id                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
    identifiant        VARCHAR(50)  NOT NULL,
    mot_de_passe_hash  VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_utilisateur_identifiant (identifiant)
) ENGINE=InnoDB;

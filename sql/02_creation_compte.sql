-- ============================================================================
-- SkoolY 1 - Compte MySQL dedie a l'application (REJOUABLE)
-- A executer avec un compte administrateur, APRES 01_creation_base.sql.
--
-- AVANT D'EXECUTER : remplacez REMPLACER_PAR_UN_MOT_DE_PASSE par le mot de
-- passe de votre choix, puis reportez-le dans config/config.php.
-- Le compte n'a des droits QUE sur la base skooly, et seulement pour lire,
-- inserer, modifier et supprimer des lignes (aucun droit de structure).
-- ============================================================================
DROP USER IF EXISTS 'skooly_app'@'localhost';
CREATE USER 'skooly_app'@'localhost' IDENTIFIED BY 'REMPLACER_PAR_UN_MOT_DE_PASSE';
GRANT SELECT, INSERT, UPDATE, DELETE ON skooly.* TO 'skooly_app'@'localhost';
FLUSH PRIVILEGES;

-- ============================================================================
-- SkoolY 1 - OPTIONNEL - Interventions ILLUSTRATIVES (rejouable, apres 03)
-- ATTENTION : le classeur ne contient AUCUNE attribution intervenant/promotion.
-- Ces lignes sont des EXEMPLES pour la demonstration (page Promotion : interventions
-- et total d'heures). Regle : matieres vues au planning de la promotion, premier
-- intervenant declare pour la matiere, heures B1 (annee 1) ou B2 (annee 2).
-- Remplacez-les par les vraies attributions du Campus.
-- ============================================================================
USE skooly;
DELETE FROM intervention;
ALTER TABLE intervention AUTO_INCREMENT = 1;

INSERT INTO intervention (intervenant_id, matiere_id, promotion_id, volume_horaire) VALUES
  (2, 1, 1, 120),
  (4, 5, 1, 60),
  (14, 7, 1, 40),
  (10, 8, 1, 60),
  (12, 11, 1, 30),
  (8, 2, 2, 80),
  (3, 3, 2, 100),
  (2, 1, 2, 60),
  (4, 6, 2, 60),
  (10, 8, 2, 60),
  (3, 3, 4, 80),
  (8, 2, 4, 60),
  (4, 5, 4, 60),
  (10, 8, 4, 60),
  (8, 2, 5, 80),
  (3, 3, 5, 100),
  (4, 6, 5, 60),
  (10, 8, 5, 60),
  (5, 4, 6, 100),
  (4, 6, 6, 40),
  (7, 10, 6, 50),
  (5, 4, 7, 120),
  (4, 6, 7, 60),
  (10, 8, 7, 60);

-- Migration 001 : ajoute la colonne "remarques" a la table salle.
-- A executer UNE SEULE FOIS, avec un compte administrateur, sur une base creee
-- AVANT cette modification. (Une base creee avec 01_creation_base.sql a jour
-- possede deja la colonne : ne pas l'executer.)
USE skooly;
ALTER TABLE salle ADD COLUMN remarques VARCHAR(500) NULL AFTER videoprojecteur;

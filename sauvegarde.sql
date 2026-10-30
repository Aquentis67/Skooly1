-- MySQL dump 10.13  Distrib 8.4.7, for Win64 (x86_64)
--
-- Host: localhost    Database: skooly
-- ------------------------------------------------------
-- Server version	8.4.7

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `apprenant`
--

DROP TABLE IF EXISTS `apprenant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `apprenant` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_naissance` date NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `regime` enum('initial','alternant') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'initial',
  `entreprise` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut` enum('inscrit','diplome','sorti') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'inscrit',
  `promotion_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_apprenant_promotion` (`promotion_id`),
  KEY `idx_apprenant_nom` (`nom`,`prenom`),
  CONSTRAINT `fk_apprenant_promotion` FOREIGN KEY (`promotion_id`) REFERENCES `promotion` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `ck_apprenant_entreprise` CHECK (((`regime` <> _utf8mb4'alternant') or ((`entreprise` is not null) and (`entreprise` <> _utf8mb4''))))
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `apprenant`
--

LOCK TABLES `apprenant` WRITE;
/*!40000 ALTER TABLE `apprenant` DISABLE KEYS */;
INSERT INTO `apprenant` VALUES (1,'Hoffmann','Amandine','1999-06-25','a.hoffmann@exemple.fr','06 37 56 02 62','initial',NULL,'inscrit',1),(2,'Marchand','Salome','2008-02-20','s.marchand@exemple.fr','07 23 38 74 79','alternant','SAS Delaunay','inscrit',1),(3,'Bertrand','Manon','2003-10-10','m.bertrand@exemple.fr','07 28 88 44 19','alternant','Cabinet Perrot','inscrit',1),(4,'Sow','Anais','2006-08-21','a.sow@exemple.fr',NULL,'initial',NULL,'inscrit',1),(5,'Martinez','Camille','2002-11-01','c.martinez@exemple.fr','07 88 16 03 45','initial',NULL,'inscrit',1),(6,'Horvath','Pauline','2007-02-05','p.horvath@exemple.fr','07 51 03 85 89','alternant','Fiduciaire du Rhin','inscrit',1),(7,'Muller','Mathis','2003-08-09','m.muller@exemple.fr','07 60 59 84 46','alternant','Cabinet Wurtz','inscrit',1),(8,'Caron','Elodie','2006-07-20','e.caron@exemple.fr','07 71 83 68 72','initial',NULL,'inscrit',1),(9,'Marchand','Paul','2003-09-13','p.marchand@exemple.fr','06 71 97 90 16','alternant','Cabinet Haas','inscrit',1),(10,'Rousseau','Eva','2007-12-14','e.rousseau@exemple.fr','07 03 89 30 61','alternant','Cabinet Haas','inscrit',1),(11,'Rodrigues','Alexis','2005-06-26','a.rodrigues@exemple.fr','07 85 74 74 51','alternant','SA Lorentz','inscrit',1),(12,'Costa','Elodie','2003-10-05','e.costa@exemple.fr',NULL,'alternant','Cabinet Perrot','inscrit',1),(13,'Renaud','Mehdi','2006-09-14','m.renaud@exemple.fr','07 36 71 40 12','alternant','SA Lorentz','inscrit',1),(14,'Roux','Quentin','2000-01-06','q.roux@exemple.fr','06 01 16 79 76','alternant','Cabinet Perrot','inscrit',1),(15,'Kovacs','Julie','2005-09-26','j.kovacs@exemple.fr','07 52 36 61 34','initial',NULL,'inscrit',1),(16,'Faure','Chloe','2004-12-23','c.faure@exemple.fr','07 87 97 88 66','initial',NULL,'inscrit',2),(17,'Garnier','Alexis','1998-02-18','a.garnier@exemple.fr','07 77 70 80 59','alternant','SARL Kieffer','inscrit',2),(18,'Ferreira','Tom','2003-06-17','t.ferreira@exemple.fr','06 69 60 15 72','initial',NULL,'inscrit',2),(19,'Keita','Laura','2007-04-27','l.keita@exemple.fr','07 41 93 84 03','initial',NULL,'inscrit',2),(20,'Fall','Nathan','1998-03-26','n.fall@exemple.fr','07 28 36 47 93','initial',NULL,'inscrit',2),(21,'Belkacem','Clara','2007-11-01','c.belkacem@exemple.fr','06 72 94 42 78','initial',NULL,'inscrit',2),(22,'Masson','Louise','2004-09-09','l.masson@exemple.fr',NULL,'alternant','SAS Delaunay','inscrit',2),(23,'Molnar','Maeva','2006-05-06','m.molnar@exemple.fr','07 12 37 72 40','initial',NULL,'inscrit',2),(24,'Marchand','Clement','2007-08-10','c.marchand@exemple.fr','06 08 84 46 16','alternant','SARL Muntz','inscrit',2),(25,'Ziani','Zoe','2002-03-10','z.ziani@exemple.fr','07 85 35 64 35','initial',NULL,'inscrit',2),(26,'Slimani','Lina','2003-06-20','l.slimani@exemple.fr','07 67 90 90 69','alternant','Cabinet Wurtz','inscrit',2),(27,'Ferreira','Ana','2007-05-13','a.ferreira@exemple.fr','06 44 55 66 77','initial',NULL,'inscrit',2),(28,'Nguyen','Eva','2002-12-15','e.nguyen@exemple.fr','06 75 74 51 47','initial',NULL,'inscrit',2),(29,'Barbier','Mehdi','2008-09-27','m.barbier@exemple.fr','07 39 33 60 19','initial',NULL,'inscrit',2),(30,'Kovacs','Zoe','2002-01-21','z.kovacs@exemple.fr','06 83 79 10 41','alternant','SARL Muntz','inscrit',2),(31,'Rousseau','Awa','2002-07-07','a.rousseau@exemple.fr','06 96 09 13 06','alternant','Cabinet Haas','inscrit',3),(32,'Bertrand','Jade','2003-07-16','j.bertrand@exemple.fr','07 89 17 22 58','alternant','Cabinet Ostermann','inscrit',3),(33,'Dumont','Emma','2001-07-27','e.dumont@exemple.fr','07 67 22 03 90','alternant','SARL Muntz','inscrit',3),(34,'Kovacs','Clement','2001-09-02','c.kovacs@exemple.fr','06 34 06 00 93','initial',NULL,'inscrit',3),(35,'Schmitt','Mehdi','2004-02-17','m.schmitt@exemple.fr','07 09 94 83 45','alternant','SARL Belin','inscrit',3),(36,'Pires','Emma','2008-10-20','e.pires@exemple.fr','07 01 49 13 09','alternant','Cabinet Ostermann','inscrit',3),(37,'Garcia','Celia','2006-04-12','c.garcia@exemple.fr','06 70 63 80 58','alternant','SA Lorentz','inscrit',3),(38,'Martinez','Nour','2005-01-23','n.martinez@exemple.fr','06 42 04 48 17','alternant','Cabinet Rivat','inscrit',3),(39,'Martinez','Maeva','2007-07-22','m.martinez@exemple.fr','06 32 21 74 04','alternant','SARL Belin','inscrit',3),(40,'Lambert','Enzo','2002-08-23','e.lambert@exemple.fr','07 54 77 74 98','initial',NULL,'inscrit',3),(41,'Kovacs','Elodie','1998-05-22','e.kovacs@exemple.fr','07 42 69 94 29','alternant','Cabinet Rivat','inscrit',3),(42,'Durand','Ines','2005-04-19','i.durand@exemple.fr','06 46 78 82 52','alternant','SARL Muntz','inscrit',3),(43,'Traore','Sarah','2005-08-10','s.traore@exemple.fr','06 36 05 50 27','alternant','SARL Belin','inscrit',3),(44,'Sanchez','Maeva','2006-01-21','m.sanchez@exemple.fr','06 02 88 49 84','alternant','Cabinet Rivat','inscrit',3),(45,'Wagner','Mehdi','2004-05-11','m.wagner@exemple.fr','07 54 24 71 15','initial',NULL,'inscrit',3),(46,'Hartmann','Oceane','2006-07-03','o.hartmann@exemple.fr',NULL,'alternant','SA Lorentz','inscrit',4),(47,'Weiss','Elsa','2007-04-19','e.weiss@exemple.fr','07 88 99 00 11','alternant','Cabinet Rivat; agence de Colmar','inscrit',4),(48,'Farkas','Romain','2008-08-11','r.farkas@exemple.fr','06 29 25 66 53','alternant','SAS Delaunay','inscrit',4),(49,'Noel','Baptiste','2001-02-28','b.noel@exemple.fr','06 33 47 61 33','alternant','Cabinet Rivat','inscrit',4),(50,'Szabo','Mathis','2004-07-23','m.szabo@exemple.fr',NULL,'alternant','Cabinet Schaeffer','inscrit',4),(51,'Laurent','Jordan','2006-06-04','j.laurent@exemple.fr',NULL,'alternant','SA Lorentz','inscrit',4),(52,'Novak','Alice','2007-09-21','a.novak@exemple.fr','06 23 21 01 63','alternant','SARL Muntz','inscrit',4),(53,'Garnier','Nour','2005-12-15','n.garnier@exemple.fr','07 56 98 04 41','alternant','Fiduciaire Sud','inscrit',4),(54,'Pires','Fatou','2007-06-27','f.pires@exemple.fr','06 39 52 25 38','alternant','Cabinet Rivat','inscrit',4),(55,'Caron','Paul','1999-06-01','p.caron@exemple.fr','07 61 87 89 29','alternant','EURL Vogel','inscrit',4),(56,'Ferreira','Laura','2003-02-10','l.ferreira@exemple.fr','06 64 73 32 39','initial',NULL,'inscrit',4),(57,'Camara','Celia','2007-11-14','c.camara@exemple.fr','06 41 17 53 42','initial',NULL,'inscrit',4),(58,'Morel','Emma','2004-04-12','e.morel@exemple.fr','07 28 98 30 14','alternant','Fiduciaire Sud','inscrit',4),(59,'Moreau','Nathan','2002-06-05','n.moreau@exemple.fr','06 14 97 26 36','alternant','SARL Kieffer','inscrit',4),(60,'Muller','Salome','2006-10-03','s.muller@exemple.fr','07 51 35 49 55','alternant','Cabinet Perrot','inscrit',4),(61,'Laurent','Hugo','2004-10-28','h.laurent@exemple.fr','07 31 56 30 28','initial',NULL,'inscrit',5),(62,'Noel','Manon','2006-03-02','m.noel@exemple.fr','06 77 02 01 20','initial',NULL,'inscrit',5),(63,'Benali','Raphael','2004-09-10','r.benali@exemple.fr','07 75 25 36 24','initial',NULL,'inscrit',5),(64,'Horvath','Anais','2001-07-21','a.horvath@exemple.fr','07 92 36 82 28','initial',NULL,'inscrit',5),(65,'Novak','Chloe','2006-07-14','c.novak@exemple.fr','07 57 74 86 22','alternant','SARL Kieffer','inscrit',5),(66,'Robin','Hugo','1999-09-11','h.robin@exemple.fr','06 76 84 03 06','initial',NULL,'inscrit',5),(67,'Leroy','Zoe','1998-11-13','z.leroy@exemple.fr','07 71 64 84 79','alternant','Cabinet Rivat','inscrit',5),(68,'Marques','Samuel','2005-05-08','s.marques@exemple.fr','07 55 55 47 05','alternant','SAS Delaunay','inscrit',5),(69,'Horvath','Louis','1999-12-19','l.horvath@exemple.fr','06 49 49 88 20','alternant','Cabinet Rivat','inscrit',5),(70,'Hamdi','Elodie','2004-07-05','e.hamdi@exemple.fr','06 03 73 33 52','initial',NULL,'inscrit',5),(71,'Cherif','Mehdi','2008-12-12','m.cherif@exemple.fr','06 84 05 25 31','alternant','Cabinet Perrot','inscrit',5),(72,'Bauer','Dylan','2004-02-22','d.bauer@exemple.fr','07 74 59 84 28','alternant','Fiduciaire Sud','inscrit',5),(73,'Ferreira','Baptiste','2004-10-16','b.ferreira@exemple.fr','07 01 78 87 05','alternant','EURL Vogel','inscrit',5),(74,'Michel','Alexis','2007-04-05','a.michel@exemple.fr','06 29 06 49 98','alternant','Cabinet Wurtz','inscrit',5),(75,'Nagy','Elodie','1999-01-27','e.nagy@exemple.fr','06 22 56 34 57','alternant','EURL Vogel','inscrit',5),(76,'Schmitt','Julie','1998-03-14','j.schmitt@exemple.fr','06 41 22 78 90','alternant','Cabinet Ostermann','inscrit',6),(77,'Keller','Thomas','2001-07-22','t.keller@exemple.fr','07 45 12 33 88','initial',NULL,'inscrit',6),(78,'Diallo','Fatou','2000-05-09','f.diallo@exemple.fr','06 58 41 20 17','alternant','SARL Muller Paie','inscrit',6),(79,'Roth','Nicolas','1995-11-30','n.roth@exemple.fr','06 72 15 48 90','initial',NULL,'inscrit',6),(80,'Lambert','Chloe','2002-09-02','c.lambert@exemple.fr','07 61 30 22 84','alternant','Fiduciaire Rhin','inscrit',6),(81,'Meyer','Antoine','1990-12-18','a.meyer@exemple.fr',NULL,'initial',NULL,'inscrit',6),(82,'Barry','Aissatou','2003-01-15','a.barry@exemple.fr','06 90 12 34 56','alternant','EXPERTY','inscrit',6),(83,'Vogel','Sophie','1997-07-07','s.vogel@exemple.fr','07 19 84 62 05','initial',NULL,'inscrit',6),(84,'Klein','Maxime','2004-04-23','m.klein@exemple.fr','06 88 77 44 11','alternant','Cabinet Perrot','inscrit',6),(85,'Ferreira','Ines','1999-10-11','i.ferreira@exemple.fr','06 23 45 67 89','alternant','SAS Delaunay','inscrit',6),(86,'Muller','Laura','2001-02-03','l.muller@exemple.fr','06 14 25 36 47','alternant','Cabinet Ostermann','inscrit',7),(87,'Da Silva','Pedro','1999-11-30','p.dasilva@exemple.fr','07 68 45 23 10','initial',NULL,'inscrit',7),(88,'Weber','Emma','2003-06-08','e.weber@exemple.fr','06 77 21 09 54','alternant','SARL Muller Paie','inscrit',7),(89,'Bauer','Lucas','2000-09-17','l.bauer@exemple.fr','06 45 90 12 78','initial',NULL,'inscrit',7),(90,'Traore','Mariam','2002-12-21','m.traore@exemple.fr','07 05 66 41 28','alternant','Fiduciaire Rhin','inscrit',7),(91,'Fischer','Hugo','2004-03-19','h.fischer@exemple.fr',NULL,'alternant','EXPERTY','inscrit',7),(92,'Lopez','Sara','1998-04-29','s.lopez@exemple.fr','06 81 33 20 75','initial',NULL,'inscrit',7),(93,'Zimmermann','Noah','2002-01-05','n.zimmermann@exemple.fr','07 46 58 12 90','alternant','Cabinet Perrot','inscrit',7),(94,'Benali','Yasmine','2003-07-12','y.benali@exemple.fr','06 23 98 74 56','initial',NULL,'inscrit',7),(95,'Koch','Mathis','1997-10-26','m.koch@exemple.fr','06 90 45 12 87','alternant','SAS Delaunay','inscrit',7),(96,'Hertz','Camille','2000-05-14','c.hertz@exemple.fr','06 32 54 76 98','alternant','Cabinet Ostermann','inscrit',8),(97,'N\'Diaye','Moussa','2001-02-08','m.ndiaye@exemple.fr','07 54 12 36 98','initial',NULL,'inscrit',8),(98,'Ruiz','Ana','2002-08-19','a.ruiz@exemple.fr','06 12 87 54 30','alternant','Fiduciaire Rhin','inscrit',8),(99,'Schneider','Tom','1999-12-02','t.schneider@exemple.fr','06 78 90 12 34','initial',NULL,'inscrit',8),(100,'Petit','Lena','2003-03-30','l.petit@exemple.fr','07 89 12 45 67','alternant','EXPERTY','inscrit',8),(101,'Morel','Adam','2000-06-24','a.morel@exemple.fr',NULL,'alternant','SARL Muller Paie','inscrit',8),(102,'Haddad','Nour','2001-09-07','n.haddad@exemple.fr','06 55 12 98 74','initial',NULL,'inscrit',8),(103,'Lang','Jules','1998-11-15','j.lang@exemple.fr','07 33 21 65 48','alternant','Cabinet Wurtz','inscrit',8),(104,'Dubois','Clara','2002-01-23','c.dubois@exemple.fr','06 12 45 90 87','initial',NULL,'inscrit',8),(105,'Osman','Ilyes','2003-06-11','i.osman@exemple.fr','06 47 85 21 36','alternant','SAS Delaunay','inscrit',8);
/*!40000 ALTER TABLE `apprenant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `intervenant`
--

DROP TABLE IF EXISTS `intervenant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `intervenant` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut` enum('salarie','vacataire') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tarif_horaire` decimal(6,2) DEFAULT NULL,
  `remarques` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_intervenant_email` (`email`),
  KEY `idx_intervenant_nom` (`nom`,`prenom`),
  CONSTRAINT `ck_intervenant_tarif` CHECK ((((`statut` = _utf8mb4'salarie') and (`tarif_horaire` is null)) or ((`statut` = _utf8mb4'vacataire') and (`tarif_horaire` is not null) and (`tarif_horaire` >= 0))))
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `intervenant`
--

LOCK TABLES `intervenant` WRITE;
/*!40000 ALTER TABLE `intervenant` DISABLE KEYS */;
INSERT INTO `intervenant` VALUES (1,'Bertrand','Paul','p.bertrand@campus-experty.fr','03 88 45 12 30','salarie',NULL,'suivi d\'alternance uniquement.'),(2,'Nguyen','Lan','l.nguyen@campus-experty.fr','03 88 45 12 31','salarie',NULL,NULL),(3,'Ostermann','Claire','c.ostermann@exemple.fr','06 45 78 12 33','vacataire',55.00,'tarif revu en janvier.'),(4,'Wurtz','Jean-Marc','jm.wurtz@exemple.fr','06 12 98 45 77','vacataire',48.00,NULL),(5,'Haas','Sophie','s.haas@exemple.fr','06 78 45 12 09','vacataire',60.00,NULL),(6,'Kieffer','Michel','m.kieffer@exemple.fr','06 33 22 11 00','vacataire',45.00,NULL),(7,'Vogel','Anne','a.vogel@exemple.fr',NULL,'vacataire',0.00,'Tarif horaire à renseigner (absent ou illisible dans le classeur : « a revoir »).'),(8,'Schaeffer','Luc','l.schaeffer@exemple.fr','07 45 61 23 89','vacataire',50.00,NULL),(9,'Muntz','Carole','c.muntz@exemple.fr','06 78 12 45 30','vacataire',47.50,NULL),(10,'Perrot','Denis','d.perrot@exemple.fr','06 12 45 78 96','vacataire',55.00,NULL),(11,'Lorentz','Eva','e.lorentz@exemple.fr','06 98 74 12 56','vacataire',55.00,NULL),(12,'Diallo','Amadou','a.diallo@exemple.fr','07 11 22 33 44','vacataire',49.00,NULL),(13,'Klein','Sarah','s.klein@exemple.fr','06 55 66 77 88','vacataire',53.00,NULL),(14,'Weber','Thomas','t.weber@exemple.fr','06 23 45 67 89','vacataire',51.00,NULL),(15,'Fischer','Julie','j.fischer@exemple.fr','06 87 65 43 21','vacataire',46.00,NULL),(16,'Moreau','Alain','a.moreau@exemple.fr','06 74 85 96 32','vacataire',0.00,'parti en 2025. Tarif horaire à renseigner (absent ou illisible dans le classeur).'),(17,'Girard','Nadia','n.girard@exemple.fr','06 32 14 58 97','vacataire',54.00,NULL),(18,'Blanc','Pierre','p.blanc@exemple.fr','06 44 33 22 11','vacataire',0.00,'intervenant ponctuel, conference.'),(19,'Sow','Fatou','f.sow@exemple.fr','06 21 43 65 87','vacataire',52.00,NULL),(20,'Renaud','Marc','m.renaud@exemple.fr','06 90 80 70 60','vacataire',58.00,NULL),(21,'Caron','Lea','l.caron@exemple.fr','06 10 20 30 40','vacataire',0.00,'Tarif horaire à renseigner (absent ou illisible dans le classeur).');
/*!40000 ALTER TABLE `intervenant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `intervenant_matiere`
--

DROP TABLE IF EXISTS `intervenant_matiere`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `intervenant_matiere` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `intervenant_id` int unsigned NOT NULL,
  `matiere_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_intervenant_matiere` (`intervenant_id`,`matiere_id`),
  KEY `idx_intervenant_matiere_matiere` (`matiere_id`),
  CONSTRAINT `fk_im_intervenant` FOREIGN KEY (`intervenant_id`) REFERENCES `intervenant` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_im_matiere` FOREIGN KEY (`matiere_id`) REFERENCES `matiere` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `intervenant_matiere`
--

LOCK TABLES `intervenant_matiere` WRITE;
/*!40000 ALTER TABLE `intervenant_matiere` DISABLE KEYS */;
INSERT INTO `intervenant_matiere` VALUES (1,2,1),(29,3,1),(30,3,3),(4,4,5),(5,4,6),(6,5,4),(7,5,6),(8,6,1),(9,7,10),(11,8,2),(10,8,3),(12,9,2),(13,10,8),(14,11,8),(15,11,9),(16,12,11),(17,13,4),(18,14,7),(19,15,9),(20,16,5),(21,17,1),(22,17,2),(23,17,3),(24,19,4),(25,19,6),(27,20,2),(26,20,7),(28,21,10);
/*!40000 ALTER TABLE `intervenant_matiere` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `intervention`
--

DROP TABLE IF EXISTS `intervention`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `intervention` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `intervenant_id` int unsigned NOT NULL,
  `matiere_id` int unsigned NOT NULL,
  `promotion_id` int unsigned NOT NULL,
  `volume_horaire` decimal(5,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_intervention_triplet` (`intervenant_id`,`matiere_id`,`promotion_id`),
  KEY `idx_intervention_matiere` (`matiere_id`),
  KEY `idx_intervention_promotion` (`promotion_id`),
  CONSTRAINT `fk_intervention_intervenant` FOREIGN KEY (`intervenant_id`) REFERENCES `intervenant` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_intervention_matiere` FOREIGN KEY (`matiere_id`) REFERENCES `matiere` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_intervention_promotion` FOREIGN KEY (`promotion_id`) REFERENCES `promotion` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `ck_intervention_volume` CHECK ((`volume_horaire` > 0))
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `intervention`
--

LOCK TABLES `intervention` WRITE;
/*!40000 ALTER TABLE `intervention` DISABLE KEYS */;
INSERT INTO `intervention` VALUES (1,2,1,1,120.00),(2,4,5,1,60.00),(3,14,7,1,40.00),(4,10,8,1,60.00),(5,12,11,1,30.00),(6,8,2,2,80.00),(7,3,3,2,100.00),(8,2,1,2,60.00),(9,4,6,2,60.00),(10,10,8,2,60.00),(11,3,3,4,80.00),(12,8,2,4,60.00),(13,4,5,4,60.00),(14,10,8,4,60.00),(15,8,2,5,80.00),(16,3,3,5,100.00),(17,4,6,5,60.00),(18,10,8,5,60.00),(19,5,4,6,100.00),(20,4,6,6,40.00),(21,7,10,6,50.00),(22,5,4,7,120.00),(23,4,6,7,60.00),(24,10,8,7,60.00);
/*!40000 ALTER TABLE `intervention` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `matiere`
--

DROP TABLE IF EXISTS `matiere`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `matiere` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_matiere_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matiere`
--

LOCK TABLES `matiere` WRITE;
/*!40000 ALTER TABLE `matiere` DISABLE KEYS */;
INSERT INTO `matiere` VALUES (1,'COMPTA-GEN','Comptabilité générale'),(2,'COMPTA-ANA','Comptabilité analytique'),(3,'FISCA','Fiscalité'),(4,'PAIE','Gestion de la paie'),(5,'DROIT','Droit des affaires'),(6,'DROIT-SOC','Droit social'),(7,'MATH-FI','Mathématiques financières'),(8,'ANGLAIS','Anglais professionnel'),(9,'COM','Communication professionnelle'),(10,'BUREAU','Bureautique'),(11,'INFO-BUREAU','Outils informatiques');
/*!40000 ALTER TABLE `matiere` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotion`
--

DROP TABLE IF EXISTS `promotion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promotion` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_promotion_code` (`code`),
  CONSTRAINT `ck_promotion_dates` CHECK ((`date_fin` > `date_debut`))
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promotion`
--

LOCK TABLES `promotion` WRITE;
/*!40000 ALTER TABLE `promotion` DISABLE KEYS */;
INSERT INTO `promotion` VALUES (1,'DCG 1','Diplôme de comptabilité et de gestion - 1re année','2025-09-01','2026-06-30'),(2,'DCG 2','Diplôme de comptabilité et de gestion - 2e année','2025-09-01','2026-06-30'),(3,'DCG 3','Diplôme de comptabilité et de gestion - 3e année','2025-09-01','2026-06-30'),(4,'DSCG 1','Diplôme supérieur de comptabilité et de gestion - 1re année','2025-09-01','2026-06-30'),(5,'DSCG 2','Diplôme supérieur de comptabilité et de gestion - 2e année','2025-09-01','2026-06-30'),(6,'GP 1','Licence gestionnaire de paie - 1re année','2025-09-01','2026-06-30'),(7,'GP 2','Licence gestionnaire de paie - 2e année','2025-09-01','2026-06-30'),(8,'GP 3','Licence gestionnaire de paie - 3e année','2025-09-01','2026-06-30');
/*!40000 ALTER TABLE `promotion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salle`
--

DROP TABLE IF EXISTS `salle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `salle` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `numero` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacite` smallint unsigned NOT NULL,
  `batiment` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `videoprojecteur` tinyint(1) NOT NULL DEFAULT '0',
  `remarques` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_salle_numero` (`numero`),
  CONSTRAINT `ck_salle_capacite` CHECK ((`capacite` > 0))
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salle`
--

LOCK TABLES `salle` WRITE;
/*!40000 ALTER TABLE `salle` DISABLE KEYS */;
INSERT INTO `salle` VALUES (1,'101','Salle Ameche',25,'Bâtiment A',1,NULL),(2,'102','Jurassic Salle🦕',18,'Bâtiment A',0,NULL),(3,'103','Salle Petre',30,'Bâtiment A',1,'Vidéoprojecteur HS depuis Novembre'),(4,'201','Salle Lyre',20,'Bâtiment B',0,'En plein travaux 🚧'),(5,'202','Salle Thé',18,'Bâtiment B',1,'postes fixes'),(6,'∞','Amphi Nie',80,'Bâtiment B',1,'Pas assez de sièges pour bien exploiter l\'espace'),(7,'203','Amphi Lible',80,'Bâtiment B',1,NULL),(8,'104','Salle Ami',20,'Bâtiment A',1,NULL),(9,'204','Salle Aire',12,'Bâtiment B',0,NULL);
/*!40000 ALTER TABLE `salle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `utilisateur` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `identifiant` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mot_de_passe_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_utilisateur_identifiant` (`identifiant`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateur`
--

LOCK TABLES `utilisateur` WRITE;
/*!40000 ALTER TABLE `utilisateur` DISABLE KEYS */;
INSERT INTO `utilisateur` VALUES (1,'admin','$2y$10$is0CqZhho8/7ITAcHDXYVujGPwppPuBzc1ar2V/zuNwfjp5/Mv4ES');
/*!40000 ALTER TABLE `utilisateur` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-30 15:05:08

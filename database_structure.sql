/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.14-MariaDB, for debian-linux-gnu (aarch64)
--
-- Host: localhost    Database: grh_qrcode
-- ------------------------------------------------------
-- Server version	10.11.14-MariaDB-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `avis_employes`
--

DROP TABLE IF EXISTS `avis_employes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `avis_employes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_employe` varchar(50) DEFAULT NULL,
  `note` int(11) NOT NULL,
  `commentaire` text NOT NULL,
  `categorie` varchar(50) DEFAULT 'Ambiance / Climat Social',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `avis_employes`
--

LOCK TABLES `avis_employes` WRITE;
/*!40000 ALTER TABLE `avis_employes` DISABLE KEYS */;
INSERT INTO `avis_employes` VALUES
(1,'OMEGA-2026-001',3,'Peut mieux faire','Climat Social','2026-08-09 02:03:06');
/*!40000 ALTER TABLE `avis_employes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `avis_grh`
--

DROP TABLE IF EXISTS `avis_grh`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `avis_grh` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `contenu` text NOT NULL,
  `priorite` varchar(50) DEFAULT 'Normal',
  `date_publication` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `avis_grh`
--

LOCK TABLES `avis_grh` WRITE;
/*!40000 ALTER TABLE `avis_grh` DISABLE KEYS */;
INSERT INTO `avis_grh` VALUES
(1,'Gestion stock','Contacter fournisseurs','Urgent','2026-08-09 21:26:21');
/*!40000 ALTER TABLE `avis_grh` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departements`
--

DROP TABLE IF EXISTS `departements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `departements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departements`
--

LOCK TABLES `departements` WRITE;
/*!40000 ALTER TABLE `departements` DISABLE KEYS */;
INSERT INTO `departements` VALUES
(1,'Direction & Administration','Pilotage stratégique et gestion générale','2026-08-09 01:29:19'),
(2,'Ingénierie & Développement','Conception technique, architecture logicielle et systèmes','2026-08-09 01:29:19'),
(3,'Finance & Comptabilité','Gestion financière, paie et contrôle budgétaire','2026-08-09 01:29:19'),
(4,'Ressources Humaines & Support','Gestion du personnel, recrutement et logistique','2026-08-09 01:29:19');
/*!40000 ALTER TABLE `departements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents_rh`
--

DROP TABLE IF EXISTS `documents_rh`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents_rh` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employe_id` int(11) NOT NULL,
  `type_document` varchar(100) NOT NULL,
  `nom_fichier` varchar(255) NOT NULL,
  `date_ajout` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `employe_id` (`employe_id`),
  CONSTRAINT `documents_rh_ibfk_1` FOREIGN KEY (`employe_id`) REFERENCES `employes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents_rh`
--

LOCK TABLES `documents_rh` WRITE;
/*!40000 ALTER TABLE `documents_rh` DISABLE KEYS */;
/*!40000 ALTER TABLE `documents_rh` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employes`
--

DROP TABLE IF EXISTS `employes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_employe` varchar(50) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telephone` varchar(50) NOT NULL,
  `poste` varchar(100) NOT NULL,
  `departement_id` int(11) DEFAULT NULL,
  `salaire_base` decimal(12,2) DEFAULT 0.00,
  `date_embauche` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code_employe` (`code_employe`),
  KEY `departement_id` (`departement_id`),
  CONSTRAINT `employes_ibfk_1` FOREIGN KEY (`departement_id`) REFERENCES `departements` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employes`
--

LOCK TABLES `employes` WRITE;
/*!40000 ALTER TABLE `employes` DISABLE KEYS */;
INSERT INTO `employes` VALUES
(1,'OMEGA-2026-001','Siby','Mohamed','sibymohamed24@gmail.com','+221776542803','Consultant en Informatique / Directeur Technique',1,750000.00,'2026-01-02','2026-08-09 01:29:19'),
(2,'OMEGA-2026-002','Diallo','Fatou','fatou.diallo@omega-consulting.sn','+221775323725','Responsable Ressources Humaines',4,450000.00,'2026-02-10','2026-08-09 01:29:19'),
(3,'OMEGA-2026-003','Ndiaye','Moustapha','moustapha.ndiaye@omega-consulting.sn','+221778899001','Ingénieur Systèmes & Réseaux',2,500000.00,'2026-03-15','2026-08-09 01:29:19');
/*!40000 ALTER TABLE `employes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paie`
--

DROP TABLE IF EXISTS `paie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `paie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employe_id` int(11) NOT NULL,
  `periode` varchar(50) NOT NULL,
  `salaire_base` decimal(12,2) NOT NULL DEFAULT 0.00,
  `primes` decimal(12,2) NOT NULL DEFAULT 0.00,
  `retenues` decimal(12,2) NOT NULL DEFAULT 0.00,
  `montant_net` decimal(12,2) NOT NULL DEFAULT 0.00,
  `date_paiement` datetime NOT NULL,
  `statut` varchar(50) NOT NULL DEFAULT 'Payé',
  PRIMARY KEY (`id`),
  KEY `employe_id` (`employe_id`),
  CONSTRAINT `paie_ibfk_1` FOREIGN KEY (`employe_id`) REFERENCES `employes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paie`
--

LOCK TABLES `paie` WRITE;
/*!40000 ALTER TABLE `paie` DISABLE KEYS */;
INSERT INTO `paie` VALUES
(1,2,'August 2026',450000.00,12000.00,1200.00,460800.00,'2026-08-09 02:08:58','Payé');
/*!40000 ALTER TABLE `paie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paies`
--

DROP TABLE IF EXISTS `paies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `paies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employe_id` int(11) NOT NULL,
  `mois` varchar(20) NOT NULL,
  `montant` decimal(12,2) NOT NULL,
  `statut` varchar(50) DEFAULT 'Payé',
  `date_paiement` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `employe_id` (`employe_id`),
  CONSTRAINT `paies_ibfk_1` FOREIGN KEY (`employe_id`) REFERENCES `employes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paies`
--

LOCK TABLES `paies` WRITE;
/*!40000 ALTER TABLE `paies` DISABLE KEYS */;
/*!40000 ALTER TABLE `paies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pointages`
--

DROP TABLE IF EXISTS `pointages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pointages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code_employe` varchar(50) NOT NULL,
  `type_pointage` enum('ENTREE','SORTIE') NOT NULL,
  `date_pointage` date NOT NULL,
  `heure_pointage` time NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pointages`
--

LOCK TABLES `pointages` WRITE;
/*!40000 ALTER TABLE `pointages` DISABLE KEYS */;
INSERT INTO `pointages` VALUES
(1,'OMEGA-2026-003','ENTREE','2026-08-09','17:13:12','2026-08-09 17:13:12');
/*!40000 ALTER TABLE `pointages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `restau_commande_items`
--

DROP TABLE IF EXISTS `restau_commande_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `restau_commande_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `commande_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `prix_unitaire` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `commande_id` (`commande_id`),
  KEY `produit_id` (`produit_id`),
  CONSTRAINT `restau_commande_items_ibfk_1` FOREIGN KEY (`commande_id`) REFERENCES `restau_commandes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `restau_commande_items_ibfk_2` FOREIGN KEY (`produit_id`) REFERENCES `restau_produits` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restau_commande_items`
--

LOCK TABLES `restau_commande_items` WRITE;
/*!40000 ALTER TABLE `restau_commande_items` DISABLE KEYS */;
INSERT INTO `restau_commande_items` VALUES
(1,1,5,10,400.00),
(2,1,3,10,500.00),
(3,2,5,2,400.00),
(4,2,3,5,500.00),
(5,2,4,1,600.00);
/*!40000 ALTER TABLE `restau_commande_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `restau_commandes`
--

DROP TABLE IF EXISTS `restau_commandes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `restau_commandes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employe_id` int(11) DEFAULT NULL,
  `table_num` varchar(50) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `mode_paiement` varchar(50) NOT NULL DEFAULT 'Crédit Salaire',
  `statut` varchar(50) NOT NULL DEFAULT 'Validé',
  `date_commande` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `employe_id` (`employe_id`),
  CONSTRAINT `restau_commandes_ibfk_1` FOREIGN KEY (`employe_id`) REFERENCES `employes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restau_commandes`
--

LOCK TABLES `restau_commandes` WRITE;
/*!40000 ALTER TABLE `restau_commandes` DISABLE KEYS */;
INSERT INTO `restau_commandes` VALUES
(1,3,'Table 1',9000.00,'Crédit Salaire','Payé','2026-08-09 04:20:52'),
(2,2,'Table_VIP',3900.00,'Crédit Salaire','Payé','2026-08-09 04:25:22');
/*!40000 ALTER TABLE `restau_commandes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `restau_produits`
--

DROP TABLE IF EXISTS `restau_produits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `restau_produits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(150) NOT NULL,
  `categorie` varchar(100) NOT NULL,
  `prix` decimal(12,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `statut` varchar(50) DEFAULT 'Disponible',
  `date_creation` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restau_produits`
--

LOCK TABLES `restau_produits` WRITE;
/*!40000 ALTER TABLE `restau_produits` DISABLE KEYS */;
INSERT INTO `restau_produits` VALUES
(1,'Thiebou Dienn (Poisson)','Plat Chaud',2500.00,20,'Disponible','2026-08-09 04:17:24'),
(2,'Poulet Yassa','Plat Chaud',3000.00,15,'Disponible','2026-08-09 04:17:24'),
(3,'Jus de Bissap Naturel','Boisson',500.00,35,'Disponible','2026-08-09 04:17:24'),
(4,'Jus de Bouye (Pain de Singe)','Boisson',600.00,39,'Disponible','2026-08-09 04:17:24'),
(5,'Eau Minérale 1.5L','Boisson',400.00,88,'Disponible','2026-08-09 04:17:24'),
(6,'Salade de Fruits Frais','Dessert',1500.00,25,'Disponible','2026-08-09 04:17:24');
/*!40000 ALTER TABLE `restau_produits` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-10  0:38:56

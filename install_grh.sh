#!/bin/bash
# ==============================================================================
# Script d'installation et de migration : Omega Informatique Consulting - GRH
# Base de données : grh_qrcode
# ==============================================================================

DB_USER="root"
DB_PASS=""
DB_NAME="grh_qrcode"

echo "[*] Création de la base de données $DB_NAME..."
mysql -u "$DB_USER" ${DB_PASS:+-p$DB_PASS} -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo "[*] Génération du schéma de la base de données..."
mysql -u "$DB_USER" ${DB_PASS:+-p$DB_PASS} "$DB_NAME" << 'SQL_EOF'

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Table des départements / services
DROP TABLE IF EXISTS `departements`;
CREATE TABLE `departements` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Table des employés (remplace adherents)
DROP TABLE IF EXISTS `employes`;
CREATE TABLE `employes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code_employe` VARCHAR(50) NOT NULL UNIQUE,
  `nom` VARCHAR(100) NOT NULL,
  `prenom` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `telephone` VARCHAR(50) NOT NULL,
  `poste` VARCHAR(100) NOT NULL,
  `departement_id` INT,
  `salaire_base` DECIMAL(12,2) DEFAULT 0.00,
  `date_embauche` DATE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`departement_id`) REFERENCES `departements`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Table des pointages (Entrée / Sortie)
DROP TABLE IF EXISTS `pointages`;
CREATE TABLE `pointages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code_employe` VARCHAR(50) NOT NULL,
  `type_pointage` ENUM('ENTREE', 'SORTIE') NOT NULL,
  `date_pointage` DATE NOT NULL,
  `heure_pointage` TIME NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Table de gestion de la paie (remplace paiements)
DROP TABLE IF EXISTS `paies`;
CREATE TABLE `paies` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `employe_id` INT NOT NULL,
  `mois` VARCHAR(20) NOT NULL,
  `montant` DECIMAL(12,2) NOT NULL,
  `statut` VARCHAR(50) DEFAULT 'Payé',
  `date_paiement` DATE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`employe_id`) REFERENCES `employes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Table des avis / feedbacks internes (remplace avis_adherents)
DROP TABLE IF EXISTS `avis_employes`;
CREATE TABLE `avis_employes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code_employe` VARCHAR(50) DEFAULT NULL,
  `note` INT NOT NULL,
  `commentaire` TEXT NOT NULL,
  `categorie` VARCHAR(50) DEFAULT 'Ambiance / Climat Social',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Table des documents / CV / compétences (remplace produits/ressources)
DROP TABLE IF EXISTS `documents_rh`;
CREATE TABLE `documents_rh` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `employe_id` INT NOT NULL,
  `type_document` VARCHAR(100) NOT NULL,
  `nom_fichier` VARCHAR(255) NOT NULL,
  `date_ajout` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`employe_id`) REFERENCES `employes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertion de données initiales de test
INSERT INTO `departements` (`id`, `nom`, `description`) VALUES
(1, 'Direction & Administration', 'Pilotage stratégique et gestion générale'),
(2, 'Ingénierie & Développement', 'Conception technique, architecture logicielle et systèmes'),
(3, 'Finance & Comptabilité', 'Gestion financière, paie et contrôle budgétaire'),
(4, 'Ressources Humaines & Support', 'Gestion du personnel, recrutement et logistique');

INSERT INTO `employes` (`code_employe`, `nom`, `prenom`, `email`, `telephone`, `poste`, `departement_id`, `salaire_base`, `date_embauche`) VALUES
('OMEGA-2026-001', 'Siby', 'Mohamed', 'sibymohamed24@gmail.com', '+221776542803', 'Consultant en Informatique / Directeur Technique', 1, 750000.00, '2026-01-02'),
('OMEGA-2026-002', 'Diallo', 'Fatou', 'fatou.diallo@omega-consulting.sn', '+221775323725', 'Responsable Ressources Humaines', 4, 450000.00, '2026-02-10'),
('OMEGA-2026-003', 'Ndiaye', 'Moustapha', 'moustapha.ndiaye@omega-consulting.sn', '+221778899001', 'Ingénieur Systèmes & Réseaux', 2, 500000.00, '2026-03-15');

SET FOREIGN_KEY_CHECKS = 1;
SQL_EOF

echo "[*] Base de données grh_qrcode configurée avec succès !"

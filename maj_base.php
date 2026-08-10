<?php
// maj_base.php - Script de mise à jour et synchronisation de la base de données (OMEGA Suite)
ini_set('display_errors', 1);
error_reporting(E_ALL);

$db = null;
if (file_exists('config/database.php')) {
    require_once 'config/database.php';
    try { $db = (new Database())->getConnection(); } catch (Exception $e) {}
} elseif (file_exists('database.php')) {
    require_once 'database.php';
    try { $db = (new Database())->getConnection(); } catch (Exception $e) {}
}

if (!$db) {
    die("Erreur critique : Impossible de se connecter à la base de données. Vérifiez config/database.php");
}

$messages = [];

try {
    // 1. Table des équipements (Parc matériel & traçabilité)
    $db->exec("CREATE TABLE IF NOT EXISTS `equipements` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `code_equipement` VARCHAR(50) NOT NULL UNIQUE,
        `nom_equipement` VARCHAR(100) NOT NULL,
        `categorie` VARCHAR(50) DEFAULT 'Matériel',
        `date_acquisition` DATE DEFAULT NULL,
        `dernier_entretien` DATE DEFAULT NULL,
        `etat_usure` ENUM('Neuf', 'Bon', 'Moyen', 'À réparer') DEFAULT 'Bon',
        `reparateur_assigne` VARCHAR(100) DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    $messages[] = "Table `equipements` vérifiée/créée avec succès.";

    // 2. Table des avis clients (Satisfaction style BMW / Subway)
    $db->exec("CREATE TABLE IF NOT EXISTS `avis_clients` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `note` TINYINT NOT NULL CHECK (note BETWEEN 1 AND 5),
        `commentaire` TEXT DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    $messages[] = "Table `avis_clients` vérifiée/créée avec succès.";

    // 3. Table des pointages GRH (si absente)
    $db->exec("CREATE TABLE IF NOT EXISTS `pointages` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `employe_id` INT NOT NULL,
        `date_pointage` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `type` ENUM('ENTREE', 'SORTIE') DEFAULT 'ENTREE'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    $messages[] = "Table `pointages` vérifiée/créée avec succès.";

    // 4. Table des commandes Restau POS (si absente)
    $db->exec("CREATE TABLE IF NOT EXISTS `restau_commandes` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `details` TEXT NOT NULL,
        `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    $messages[] = "Table `restau_commandes` vérifiée/créée avec succès.";

} catch (Exception $e) {
    die("Erreur lors de la mise à jour SQL : " . $e.getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mise à jour Base de Données - OMEGA Suite</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/all.min.css">
</head>
<body class="bg-dark text-white">
    <div class="container my-5" style="max-width: 600px;">
        <div class="card bg-secondary border-dark shadow-lg p-4">
            <h3 class="text-success mb-3"><i class="fas fa-database me-2"></i> Base de Données Synchronisée</h3>
            <p class="text-white">Toutes les tables requises pour les nouveaux modules ont été initialisées avec succès.</p>
            <ul class="list-group list-group-flush mb-4">
                <?php foreach($messages as $msg): ?>
                    <li class="list-group-item bg-dark text-success border-secondary"><i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($msg) ?></li>
                <?php endforeach; ?>
            </ul>
            <div class="d-grid">
                <a href="index.php" class="btn btn-danger fw-bold"><i class="fas fa-home me-1"></i> Retour au Dashboard OMEGA</a>
            </div>
        </div>
    </div>
</body>
</html>

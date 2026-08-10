<?php
// reset_database.php - Script de réinitialisation des tables (Pointages, Factures, Paie, Commandes)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Connexion DB
$db = null;
if (file_exists('config/database.php')) {
    require_once 'config/database.php';
    try { $db = (new Database())->getConnection(); } catch (Exception $e) {}
} elseif (file_exists('database.php')) {
    require_once 'database.php';
    try { $db = (new Database())->getConnection(); } catch (Exception $e) {}
}

$message = '';
$type_msg = '';

if ($db && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmer_reset'])) {
    try {
        // Désactivation temporaire des contraintes de clés étrangères pour éviter les erreurs de suppression
        $db->exec("SET FOREIGN_KEY_CHECKS = 0;");

        // Liste des tables à vider (ajustez selon vos noms de tables exacts)
        $tables = [
            'pointages',
            'factures',
            'paiements',
            'paie',
            'bulletins_paie',
            'restau_commandes',
            'restau_commande_items'
        ];

        $reinitialisees = [];
        foreach ($tables as $table) {
            // Vérifier si la table existe avant de la vider
            $check = $db->query("SHOW TABLES LIKE '$table'")->rowCount();
            if ($check > 0) {
                $db->exec("TRUNCATE TABLE `$table`");
                $reinitialisees[] = $table;
            }
        }

        // Réactivation des contraintes
        $db->exec("SET FOREIGN_KEY_CHECKS = 1;");

        $message = "Réinitialisation réussie ! Tables vidées : " . implode(', ', $reinitialisees);
        $type_msg = "success";
    } catch (Exception $e) {
        if ($db) $db->exec("SET FOREIGN_KEY_CHECKS = 1;");
        $message = "Erreur lors de la réinitialisation : " . $e->getMessage();
        $type_msg = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>OMEGA GRH - Réinitialisation des Données</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-dark text-white">

<div class="container my-5" style="max-width: 600px;">
    <div class="card bg-secondary border-danger shadow-lg">
        <div class="card-header bg-danger text-white fw-bold">
            <i class="fas fa-exclamation-triangle me-2"></i> Zone Dangereuse : Réinitialisation des Données
        </div>
        <div class="card-body p-4">
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= $type_msg ?> fw-bold shadow">
                    <i class="fas fa-info-circle me-2"></i> <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <p class="text-warning">
                Attention : Ce script va vider complètement les tables de transactions (Pointages, Factures, Commandes Restau, Paie). 
                <strong>Les comptes employés et les produits du catalogue ne seront pas supprimés.</strong>
            </p>

            <form method="POST" onsubmit="return confirm('Êtes-vous absolument sûr de vouloir vider ces tables ? Cette action est irréversible.');">
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="confirmCheck" required>
                    <label class="form-check-label text-white" for="confirmCheck">Je confirme vouloir effacer l'historique des données opérationnelles.</label>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-outline-light"><i class="fas fa-arrow-left me-1"></i> Retour Dashboard</a>
                    <button type="submit" name="confirmer_reset" class="btn btn-danger fw-bold">
                        <i class="fas fa-trash-alt me-1"></i> Exécuter la Réinitialisation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>

<?php
// satisfaction.php - Enquêtes de Satisfaction & Avis Clients Temps Réel (BMW & Subway style)
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (file_exists('header.php')) {
    include 'header.php';
} else {
    echo '<link rel="stylesheet" href="assets/css/bootstrap.min.css"><link rel="stylesheet" href="assets/css/all.min.css">';
}

$db = null;
if (file_exists('config/database.php')) {
    require_once 'config/database.php';
    try { $db = (new Database())->getConnection(); } catch (Exception $e) {}
} elseif (file_exists('database.php')) {
    require_once 'database.php';
    try { $db = (new Database())->getConnection(); } catch (Exception $e) {}
}

// Création automatique de la table si absente
if ($db) {
    try {
        $db->exec("CREATE TABLE IF NOT EXISTS `avis_clients` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `note` INT NOT NULL,
            `commentaire` TEXT DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    } catch (Exception $e) {}
}

$message = '';
if ($db && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['noter'])) {
    $note = intval($_POST['note']);
    $commentaire = trim($_POST['commentaire']);
    if ($note >= 1 && $note <= 5) {
        $stmt = $db->prepare("INSERT INTO avis_clients (note, commentaire) VALUES (?, ?)");
        $stmt->execute([$note, $commentaire]);
        $message = "Merci pour votre évaluation ! Votre retour a bien été pris en compte.";
    }
}

$avis_list = [];
$moyenne = 0;
if ($db) {
    try {
        $avis_list = $db->query("SELECT * FROM avis_clients ORDER BY id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
        $res = $db->query("SELECT AVG(note) as avg_note, COUNT(*) as total FROM avis_clients")->fetch(PDO::FETCH_ASSOC);
        $moyenne = round($res['avg_note'] ?? 0, 1);
        $total_avis = $res['total'] ?? 0;
    } catch (Exception $e) {}
}
?>
<div class="container my-4 text-white" style="max-width: 700px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><span class="text-warning"><i class="fas fa-star"></i></span> Enquête de Satisfaction</h2>
        <a href="index.php" class="btn btn-outline-light btn-sm"><i class="fas fa-home"></i> Dashboard</a>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success shadow fw-bold"><i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="card bg-dark border-secondary shadow-lg mb-4 p-4 text-center">
        <h4 class="text-warning">Note Moyenne Globale : <?= $moyenne ?> / 5</h4>
        <p class="text-muted small">Basé sur <?= $avis_list ? count($avis_list) : 0 ?> retours clients récents.</p>
        
        <form method="POST" class="mt-3 text-start">
            <div class="mb-3">
                <label class="form-label text-info">Attribuez une note de 1 à 5 :</label>
                <select name="note" class="form-control bg-dark text-white border-secondary" required>
                    <option value="5">⭐⭐⭐⭐⭐ - Excellent</option>
                    <option value="4">⭐⭐⭐⭐ - Très Bien</option>
                    <option value="3">⭐⭐⭐ - Satisfaisant</option>
                    <option value="2">⭐⭐ - À améliorer</option>
                    <option value="1">⭐ - Insuffisant</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label text-info">Commentaire ou suggestion :</label>
                <textarea name="commentaire" class="form-control bg-dark text-white border-secondary" rows="2" placeholder="Votre avis nous intéresse..."></textarea>
            </div>
            <button type="submit" name="noter" class="btn btn-warning w-100 fw-bold text-dark"><i class="fas fa-paper-plane me-1"><b> Soumettre mon avis</b></i></button>
        </form>
    </div>
</div>
<?php if (file_exists('footer.php')) include 'footer.php'; ?>

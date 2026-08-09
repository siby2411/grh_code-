<?php
// avis_grh.php - Gestion des Avis & Notes de Service GRH
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

if ($db) {
    try {
        $db->exec("CREATE TABLE IF NOT EXISTS avis_grh (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titre VARCHAR(255) NOT NULL,
            contenu TEXT NOT NULL,
            priorite VARCHAR(50) DEFAULT 'Normal',
            date_publication DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    } catch (Exception $e) {}
}

$message = '';
$type_msg = '';

if ($db && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['publier_avis'])) {
    $titre = trim($_POST['titre']);
    $contenu = trim($_POST['contenu']);
    $priorite = trim($_POST['priorite']);

    if (!empty($titre) && !empty($contenu)) {
        try {
            $stmt = $db->prepare("INSERT INTO avis_grh (titre, contenu, priorite, date_publication) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$titre, $contenu, $priorite]);
            $message = "Avis ou Note de Service publié avec succès !";
            $type_msg = "success";
        } catch (Exception $e) {
            $message = "Erreur : " . $e->getMessage();
            $type_msg = "danger";
        }
    } else {
        $message = "Veuillez remplir tous les champs obligatoires.";
        $type_msg = "warning";
    }
}

$avis_liste = [];
if ($db) {
    try {
        $avis_liste = $db->query("SELECT * FROM avis_grh ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {}
}
?>

<div class="container my-4 text-white">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-bullhorn text-warning"></i> OMEGA GRH — Avis & Notes de Service</h2>
        <div>
            <a href="index.php" class="btn btn-outline-light btn-sm me-2"><i class="fas fa-home me-1"></i> Dashboard</a>
            <a href="liste_employes.php" class="btn btn-warning btn-sm text-dark fw-bold"><i class="fas fa-users me-1"></i> Annuaire</a>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $type_msg ?> fw-bold shadow"><i class="fas fa-info-circle me-2"></i> <?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card bg-dark border-secondary shadow-lg">
                <div class="card-header bg-warning text-dark fw-bold">
                    <i class="fas fa-pen me-1"></i> Publier un Avis / Note
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Titre de l'Avis</label>
                            <input type="text" name="titre" class="form-control bg-dark text-white border-secondary" required placeholder="Ex: Réunion générale vendredi">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Priorité</label>
                            <select name="priorite" class="form-control bg-dark text-white border-secondary">
                                <option value="Normal">Normal</option>
                                <option value="Important">Important</option>
                                <option value="Urgent">Urgent / Alerte</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contenu de la Note</label>
                            <textarea name="contenu" class="form-control bg-dark text-white border-secondary" rows="4" required placeholder="Détails de la note de service..."></textarea>
                        </div>
                        <button type="submit" name="publier_avis" class="btn btn-warning text-dark w-100 fw-bold py-2">
                            <i class="fas fa-paper-plane me-1"></i> Publier l'Avis
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-4">
            <div class="card bg-dark border-secondary shadow-lg">
                <div class="card-header bg-secondary text-white fw-bold">
                    <i class="fas fa-list me-1"></i> Tableau d'Affichage des Avis & Communications
                </div>
                <div class="card-body">
                    <?php if (empty($avis_liste)): ?>
                        <p class="text-muted text-center py-4">Aucun avis ou note de service publié pour le moment.</p>
                    <?php else: foreach ($avis_liste as $avis): ?>
                        <div class="card bg-dark border-secondary mb-3 shadow">
                            <div class="card-header d-flex justify-content-between align-items-center bg-secondary text-white py-2">
                                <span class="fw-bold"><?= htmlspecialchars($avis['titre']) ?></span>
                                <div>
                                    <?php if ($avis['priorite'] == 'Urgent'): ?>
                                        <span class="badge bg-danger">Urgent</span>
                                    <?php elseif ($avis['priorite'] == 'Important'): ?>
                                        <span class="badge bg-warning text-dark">Important</span>
                                    <?php else: ?>
                                        <span class="badge bg-info">Normal</span>
                                    <?php endif; ?>
                                    <small class="ms-2 text-light"><?= date('d/m/Y H:i', strtotime($avis['date_publication'])) ?></small>
                                </div>
                            </div>
                            <div class="card-body text-white">
                                <p class="card-text mb-0"><?= nl2br(htmlspecialchars($avis['contenu'])) ?></p>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (file_exists('footer.php')) include 'footer.php'; ?>

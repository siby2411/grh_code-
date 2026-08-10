<?php
// equipements.php - CRUD Complet Parc Matériel & Traçabilité QR Code (OMEGA Suite)
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

$message = '';
$type_msg = '';

if ($db) {
    // Création automatique de la table si absente
    try {
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
    } catch (Exception $e) {}

    // Traitement Ajout / Modification
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['enregistrer'])) {
            $id = intval($_POST['id'] ?? 0);
            $code = trim($_POST['code_equipement']);
            $nom = trim($_POST['nom_equipement']);
            $cat = trim($_POST['categorie']);
            $date_acq = !empty($_POST['date_acquisition']) ? $_POST['date_acquisition'] : null;
            $etat = $_POST['etat_usure'];
            $reparateur = trim($_POST['reparateur_assigne']);

            if (!empty($code) && !empty($nom)) {
                try {
                    if ($id > 0) {
                        // Mise à jour
                        $stmt = $db->prepare("UPDATE equipements SET code_equipement=?, nom_equipement=?, categorie=?, date_acquisition=?, etat_usure=?, reparateur_assigne=? WHERE id=?");
                        $stmt->execute([$code, $nom, $cat, $date_acq, $etat, $reparateur, $id]);
                        $message = "Équipement mis à jour avec succès !";
                    } else {
                        // Insertion
                        $stmt = $db->prepare("INSERT INTO equipements (code_equipement, nom_equipement, categorie, date_acquisition, etat_usure, reparateur_assigne) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$code, $nom, $cat, $date_acq, $etat, $reparateur]);
                        $message = "Nouvel équipement ajouté avec succès !";
                    }
                    $type_msg = "success";
                } catch (Exception $e) {
                    $message = "Erreur SQL : " . $e->getMessage();
                    $type_msg = "danger";
                }
            }
        } elseif (isset($_POST['supprimer'])) {
            $id = intval($_POST['id_suppr']);
            try {
                $stmt = $db->prepare("DELETE FROM equipements WHERE id = ?");
                $stmt->execute([$id]);
                $message = "Équipement supprimé avec succès.";
                $type_msg = "warning";
            } catch (Exception $e) {
                $message = "Erreur lors de la suppression : " . $e->getMessage();
                $type_msg = "danger";
            }
        }
    }
}

// Récupération de la liste
$equipements = [];
if ($db) {
    try {
        $equipements = $db->query("SELECT * FROM equipements ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {}
}

// Mode édition si ID présent dans l'URL
$edit_data = null;
if (isset($_GET['edit']) && $db) {
    $edit_id = intval($_GET['edit']);
    $stmt = $db->prepare("SELECT * FROM equipements WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_data = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="container my-4 text-white">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><span class="text-danger"><i class="fas fa-tools"></i></span> Gestion du Parc Matériel & QR Codes</h2>
        <a href="index.php" class="btn btn-outline-light btn-sm"><i class="fas fa-home"></i> Dashboard</a>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $type_msg ?> shadow fw-bold">
            <i class="fas fa-info-circle me-2"></i> <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Formulaire d'Ajout / Modification -->
    <div class="card bg-dark border-secondary shadow-lg mb-4">
        <div class="card-header bg-secondary text-white fw-bold">
            <i class="fas fa-<?= $edit_data ? 'edit' : 'plus-circle' ?> me-2"></i> <?= $edit_data ? 'Modifier l\'équipement #' . $edit_data['id'] : 'Enregistrer un nouvel équipement' ?>
        </div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <input type="hidden" name="id" value="<?= $edit_data['id'] ?? 0 ?>">
                
                <div class="col-md-3">
                    <label class="form-label text-muted">Code Équipement :</label>
                    <input type="text" name="code_equipement" class="form-control bg-dark text-white border-secondary" required value="<?= htmlspecialchars($edit_data['code_equipement'] ?? 'EQ-' . rand(100,999)) ?>" placeholder="Ex: EQ-PC-001">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted">Nom de l'équipement :</label>
                    <input type="text" name="nom_equipement" class="form-control bg-dark text-white border-secondary" required value="<?= htmlspecialchars($edit_data['nom_equipement'] ?? '') ?>" placeholder="Ex: PC Dell Latitude">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted">Catégorie :</label>
                    <input type="text" name="categorie" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($edit_data['categorie'] ?? 'Ordinateur') ?>" placeholder="Ex: Informatique">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted">Date d'acquisition :</label>
                    <input type="date" name="date_acquisition" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($edit_data['date_acquisition'] ?? date('Y-m-d')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted">État d'usure :</label>
                    <select name="etat_usure" class="form-control bg-dark text-white border-secondary">
                        <?php 
                        $etats = ['Neuf', 'Bon', 'Moyen', 'À réparer'];
                        $current_etat = $edit_data['etat_usure'] ?? 'Bon';
                        foreach($etats as $e) {
                            $sel = ($current_etat === $e) ? 'selected' : '';
                            echo "<option value=\"$e\" $sel>$e</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted">Réparateur assigné :</label>
                    <input type="text" name="reparateur_assigne" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($edit_data['reparateur_assigne'] ?? 'Mohamed Siby') ?>" placeholder="Ex: Mohamed Siby">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" name="enregistrer" class="btn btn-danger w-100 fw-bold">
                        <i class="fas fa-save me-1"></i> <?= $edit_data ? 'Mettre à jour' : 'Enregistrer' ?>
                    </button>
                    <?php if($edit_data): ?>
                        <a href="equipements.php" class="btn btn-outline-secondary ms-2">Annuler</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des Équipements -->
    <div class="card bg-dark border-secondary shadow-lg">
        <div class="card-header bg-secondary text-white fw-bold d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list me-2"></i> Parc Matériel Enregistré (<?= count($equipements) ?>)</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-striped align-middle mb-0">
                    <thead>
                        <tr class="text-secondary">
                            <th>Code</th>
                            <th>Nom & Catégorie</th>
                            <th>Acquisition</th>
                            <th>État</th>
                            <th>Réparateur</th>
                            <th class="text-center">QR Code & Fiche</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($equipements)): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">Aucun équipement enregistré pour le moment.</td></tr>
                        <?php else: foreach ($equipements as $eq): 
                            $badge_color = 'success';
                            if($eq['etat_usure'] === 'À réparer') $badge_color = 'danger';
                            elseif($eq['etat_usure'] === 'Moyen') $badge_color = 'warning text-dark';
                            elseif($eq['etat_usure'] === 'Neuf') $badge_color = 'info text-dark';

                            $fiche_url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/equipement_fiche.php?id=' . $eq['id'];
                            $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($fiche_url);
                        ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($eq['code_equipement']) ?></span></td>
                                <td>
                                    <strong><?= htmlspecialchars($eq['nom_equipement']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($eq['categorie']) ?></small>
                                </td>
                                <td><small><?= htmlspecialchars($eq['date_acquisition'] ?? 'N/A') ?></small></td>
                                <td><span class="badge bg-<?= $badge_color ?>"><?= htmlspecialchars($eq['etat_usure']) ?></span></td>
                                <td><?= htmlspecialchars($eq['reparateur_assigne'] ?? 'Non assigné') ?></td>
                                <td class="text-center">
                                    <a href="equipement_fiche.php?id=<?= $eq['id'] ?>" target="_blank" class="btn btn-sm btn-outline-info" title="Voir la fiche mobile">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#qrModal<?= $eq['id'] ?>" title="Afficher QR Code">
                                        <i class="fas fa-qrcode"></i>
                                    </button>

                                    <!-- Modal QR Code -->
                                    <div class="modal fade text-dark" id="qrModal<?= $eq['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered modal-sm">
                                            <div class="modal-content bg-dark text-white border-secondary">
                                                <div class="modal-header border-secondary">
                                                    <h5 class="modal-title fs-6"><?= htmlspecialchars($eq['nom_equipement']) ?></h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-center bg-white p-3">
                                                    <img src="<?= $qr_url ?>" alt="QR Code" class="img-fluid">
                                                    <p class="text-dark small mt-2 mb-0 fw-bold"><?= htmlspecialchars($eq['code_equipement']) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="equipements.php?edit=<?= $eq['id'] ?>" class="btn btn-sm btn-primary" title="Modifier l'état ou les infos">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Confirmer la suppression de cet équipement ?');">
                                        <input type="hidden" name="id_suppr" value="<?= $eq['id'] ?>">
                                        <button type="submit" name="supprimer" class="btn btn-sm btn-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if (file_exists('footer.php')) include 'footer.php'; ?>

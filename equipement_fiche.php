<?php
// equipement_fiche.php - Fiche dynamique consultée lors du scan QR d'un matériel
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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$equipement = null;
$tickets = [];
$message = '';
$type_msg = '';

if ($db && $id > 0) {
    try {
        // Récupérer l'équipement
        $stmt = $db->prepare("SELECT * FROM equipements WHERE id = ?");
        $stmt->execute([$id]);
        $equipement = $stmt->fetch(PDO::FETCH_ASSOC);

        // Récupérer l'historique des pannes / tickets de cet équipement
        $t_stmt = $db->prepare("SELECT * FROM maintenance_tickets WHERE equipement_id = ? ORDER BY id DESC");
        $t_stmt->execute([$id]);
        $tickets = $t_stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {}
}

// Traitement d'un signalement rapide depuis cette fiche mobile
if ($db && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signaler_panne_rapide'])) {
    $description = trim($_POST['description_panne']);
    $signale_par = trim($_POST['signale_par'] ?? 'Agent terrain');

    if (!empty($description) && $equipement) {
        try {
            $stmt = $db->prepare("INSERT INTO maintenance_tickets (equipement_id, signale_par, description_panne, statut) VALUES (?, ?, ?, 'Ouvert')");
            $stmt->execute([$id, $signale_par, $description]);
            
            $upd = $db->prepare("UPDATE equipements SET etat_usure = 'À réparer' WHERE id = ?");
            $upd->execute([$id]);

            $message = "Ticket enregistré avec succès ! L'équipe technique a été notifiée.";
            $type_msg = "success";

            // Recharger les infos
            $stmt = $db->prepare("SELECT * FROM equipements WHERE id = ?");
            $stmt->execute([$id]);
            $equipement = $stmt->fetch(PDO::FETCH_ASSOC);

            $t_stmt = $db->prepare("SELECT * FROM maintenance_tickets WHERE equipement_id = ? ORDER BY id DESC");
            $t_stmt->execute([$id]);
            $tickets = $t_stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $message = "Erreur : " . $e->getMessage();
            $type_msg = "danger";
        }
    }
}
?>

<div class="container my-4 text-white" style="max-width: 700px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-qrcode text-danger"></i> Fiche Matériel & Traçabilité</h2>
        <a href="equipements.php" class="btn btn-outline-light btn-sm"><i class="fas fa-arrow-left me-1"></i> Retour au Parc</a>
    </div>

    <?php if (!$equipement): ?>
        <div class="alert alert-danger shadow fw-bold text-center">
            <i class="fas fa-exclamation-triangle me-2"></i> Équipement introuvable ou inexistant.
        </div>
    <?php else: ?>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $type_msg ?> shadow fw-bold">
                <i class="fas fa-info-circle me-2"></i> <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Carte d'identité du matériel -->
        <div class="card bg-dark border-secondary shadow-lg mb-4">
            <div class="card-header bg-secondary fw-bold text-white d-flex justify-content-between">
                <span><?= htmlspecialchars($equipement['nom_equipement']) ?></span>
                <span class="badge bg-warning text-dark"><?= htmlspecialchars($equipement['code_equipement']) ?></span>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush bg-dark">
                    <li class="list-group-item bg-dark text-white d-flex justify-content-between border-secondary">
                        <span><i class="fas fa-tag me-2 text-secondary"></i> Catégorie :</span>
                        <strong><?= htmlspecialchars($equipement['categorie']) ?></strong>
                    </li>
                    <li class="list-group-item bg-dark text-white d-flex justify-content-between border-secondary">
                        <span><i class="fas fa-calendar-alt me-2 text-secondary"></i> Date d'acquisition :</span>
                        <span><?= htmlspecialchars($equipement['date_acquisition'] ?? 'Non renseignée') ?></span>
                    </li>
                    <li class="list-group-item bg-dark text-white d-flex justify-content-between border-secondary">
                        <span><i class="fas fa-tools me-2 text-secondary"></i> Dernier entretien :</span>
                        <span><?= htmlspecialchars($equipement['dernier_entretien'] ?? 'Aucun') ?></span>
                    </li>
                    <li class="list-group-item bg-dark text-white d-flex justify-content-between border-secondary">
                        <span><i class="fas fa-shield-alt me-2 text-secondary"></i> État d'usure actuel :</span>
                        <span class="badge bg-danger"><?= htmlspecialchars($equipement['etat_usure']) ?></span>
                    </li>
                    <li class="list-group-item bg-dark text-white d-flex justify-content-between border-secondary">
                        <span><i class="fas fa-user-cog me-2 text-secondary"></i> Réparateur assigné :</span>
                        <span><?= htmlspecialchars($equipement['reparateur_assigne'] ?? 'Non assigné') ?></span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Formulaire de signalement de panne direct sur mobile -->
        <div class="card bg-dark border-danger shadow-lg mb-4">
            <div class="card-header bg-danger text-white fw-bold">
                <i class="fas fa-exclamation-circle me-2"></i> Signaler un dysfonctionnement sur ce matériel
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label text-muted">Votre Nom / Prénom :</label>
                        <input type="text" name="signale_par" class="form-control bg-dark text-white border-secondary" required placeholder="Ex: Jean Marie Monteiro">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Description de la panne constatée :</label>
                        <textarea name="description_panne" class="form-control bg-dark text-white border-secondary" rows="3" required placeholder="Décrivez le problème rencontré..."></textarea>
                    </div>
                    <button type="submit" name="signaler_panne_rapide" class="btn btn-danger w-100 fw-bold">
                        <i class="fas fa-paper-plane me-1"></i> Envoyer le signalement technique
                    </button>
                </form>
            </div>
        </div>

        <!-- Historique des pannes -->
        <div class="card bg-dark border-secondary shadow-lg">
            <div class="card-header bg-secondary fw-bold text-white">
                <i class="fas fa-history me-2"></i> Historique des pannes & interventions (<?= count($tickets) ?>)
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-striped align-middle mb-0">
                        <thead>
                            <tr class="text-secondary">
                                <th>Date</th>
                                <th>Signalé par</th>
                                <th>Description</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($tickets)): ?>
                               <tr><td colspan="4" class="text-center text-muted py-3">Aucun historique de panne pour cet équipement.</td></tr>
                            <?php else: foreach ($tickets as $t): ?>
                                <tr>
                                    <td><small><?= htmlspecialchars($t['created_at']) ?></small></td>
                                    <td><?= htmlspecialchars($t['signale_par']) ?></td>
                                    <td><?= htmlspecialchars($t['description_panne']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $t['statut'] === 'Résolu' ? 'success' : 'warning text-dark' ?>">
                                            <?= htmlspecialchars($t['statut']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>

<?php if (file_exists('footer.php')) include 'footer.php'; ?>

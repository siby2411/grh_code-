<?php
// paiements.php - Gestion des paiements et Orange Money (Base: grh_qrcode, Table employes, Mobile: 221776542803)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Connexion PDO directe et autonome à la base grh_qrcode
$db = null;
try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=grh_qrcode;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Auto-création de la table paiements si elle n'existe pas encore dans grh_qrcode
    $db->exec("CREATE TABLE IF NOT EXISTS paiements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        employes_id INT NOT NULL,
        montant DECIMAL(10,2) NOT NULL,
        mode_paiement VARCHAR(50) NOT NULL,
        reference VARCHAR(100) DEFAULT NULL,
        observations TEXT DEFAULT NULL,
        statut VARCHAR(20) DEFAULT 'valide',
        date_paiement TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

} catch (Exception $e) {
    if (file_exists('config/database.php')) {
        require_once 'config/database.php';
        try { $db = (new Database())->getConnection(); } catch (Exception $ex) {}
    }
}

if (file_exists('header.php')) {
    include 'header.php';
} else {
    echo '<link rel="stylesheet" href="assets/css/bootstrap.min.css"><link rel="stylesheet" href="assets/css/all.min.css">';
}

$success = '';
// Traitement ajout paiement
if ($db && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    try {
        $query = "INSERT INTO paiements (employes_id, montant, mode_paiement, reference, observations) VALUES (:employes_id, :montant, :mode, :ref, :obs)";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':employes_id' => $_POST['employes_id'],
            ':montant' => $_POST['montant'],
            ':mode' => $_POST['mode_paiement'],
            ':ref' => $_POST['reference'] ?? 'OM-221776542803',
            ':obs' => $_POST['observations']
        ]);
        $success = "Paiement enregistré avec succès !";
    } catch (Exception $e) {
        $success = "Erreur lors de l'enregistrement : " . $e->getMessage();
    }
}

// Récupération des employés depuis la table 'employes'
$employes = [];
if ($db) {
    try {
        // Adaptation dynamique selon les colonnes probables de la table employes (nom, prenom, id)
        $employes = $db->query("SELECT id, nom, prenom FROM employes ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Fallback si la table utilise une structure différente
        try {
            $employes = $db->query("SELECT id FROM employes")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $ex) {}
    }
}

// Récupération de l'historique des paiements
$paiements = [];
if ($db) {
    try {
        $query = "SELECT p.*, e.prenom, e.nom 
                  FROM paiements p
                  JOIN employes e ON p.employes_id = e.id
                  ORDER BY p.date_paiement DESC LIMIT 50";
        $paiements = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {}
}

$numero_marchand = "221776542803";
?>

<div class="container my-4 text-white">
    <div class="card bg-dark border-secondary shadow-lg">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h3><i class="fas fa-money-bill-wave me-2"></i> Gestion des Paiements & Orange Money</h3>
            <span class="badge bg-black text-warning">Compte : <?= $numero_marchand ?></span>
        </div>
        <div class="card-body">
            <?php if(!empty($success)): ?>
                <div class="alert alert-success shadow fw-bold"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <div class="d-flex gap-2 mb-4">
                <button class="btn btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#addPaiementModal">
                    <i class="fas fa-plus me-1"></i> Nouveau Paiement
                </button>
                <button class="btn btn-outline-warning fw-bold" data-bs-toggle="modal" data-bs-target="#qrMobileModal">
                    <i class="fas fa-qrcode me-1"></i> Afficher QR Orange Money (<?= $numero_marchand ?>)
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-striped align-middle">
                    <thead>
                        <tr class="text-secondary">
                            <th>Date</th>
                            <th>Employé / Bénéficiaire</th>
                            <th>Montant</th>
                            <th>Mode</th>
                            <th>Référence</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($paiements)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-3">Aucun paiement enregistré pour le moment.</td></tr>
                        <?php else: foreach($paiements as $p): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($p['date_paiement'] ?? 'now')) ?></td>
                            <td><?= htmlspecialchars(($p['prenom'] ?? '') . ' ' . ($p['nom'] ?? '')) ?></td>
                            <td><strong><?= number_format($p['montant'] ?? 0, 0, ',', ' ') ?> FCFA</strong></td>
                            <td><span class="badge bg-info"><?= htmlspecialchars($p['mode_paiement'] ?? '') ?></span></td>
                            <td><?= htmlspecialchars($p['reference'] ?? '-') ?></td>
                            <td><span class="badge bg-success"><?= htmlspecialchars($p['statut'] ?? 'valide') ?></span></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajout Paiement -->
<div class="modal fade" id="addPaiementModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-hand-holding-usd me-2"></i> Enregistrer un Paiement</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label text-muted">Employé / Bénéficiaire *</label>
                        <select name="employes_id" class="form-control bg-dark text-white border-secondary" required>
                            <option value="">Sélectionner un employé</option>
                            <?php if(empty($employes)): ?>
                                <option value="" disabled>Aucun employé trouvé dans la table</option>
                            <?php else: foreach($employes as $e): ?>
                                <option value="<?= $e['id'] ?>"><?= htmlspecialchars(($e['prenom'] ?? '') . ' ' . ($e['nom'] ?? 'Employé #' . $e['id'])) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Montant (FCFA) *</label>
                        <input type="number" name="montant" class="form-control bg-dark text-white border-secondary" required placeholder="Ex: 10000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Mode de paiement *</label>
                        <select name="mode_paiement" class="form-control bg-dark text-white border-secondary" required>
                            <option value="especes">Espèces</option>
                            <option value="orange_money" selected>Orange Money (<?= $numero_marchand ?>)</option>
                            <option value="wave">Wave</option>
                            <option value="virement">Virement bancaire</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Référence de transaction (Optionnel)</label>
                        <input type="text" name="reference" class="form-control bg-dark text-white border-secondary" placeholder="N° transaction ou TXID...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Observations</label>
                        <textarea name="observations" class="form-control bg-dark text-white border-secondary" rows="2" placeholder="Notes complémentaires..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="submit" class="btn btn-success w-100 fw-bold">Enregistrer le paiement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal QR Code Orange Money -->
<div class="modal fade" id="qrMobileModal" tabindex="-1">
    <div class="modal-dialog text-center">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="fas fa-mobile-alt me-2"></i> Encaisser via Orange Money Sénégal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Scannez ce QR code officiel pour effectuer un transfert ou un paiement marchand :</p>
                <div class="bg-white p-3 d-inline-block rounded shadow my-2">
                    <img src="qr_gen.php?data=<?= urlencode($numero_marchand) ?>&label=ORANGE+MONEY+SENEGAL" alt="QR Orange Money" class="img-fluid">
                </div>
                <h4 class="text-success font-monospace mt-2"><?= $numero_marchand ?></h4>
                <p class="text-secondary small">Réseau Orange Money Sénégal</p>
            </div>
        </div>
    </div>
</div>

<?php if (file_exists('footer.php')) include 'footer.php'; ?>

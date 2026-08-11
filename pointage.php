<?php
require_once 'config/database.php';                         
include 'header.php';         
$db = (new Database())->getConnection();
$message = '';
$alert_type = 'success';
                              
if ($_SERVER['REQUEST_METHOD'] === 'POST') {                    
    $code_employe = trim($_POST['code_employe']);
    $type_pointage = $_POST['type_pointage']; // ENTREE ou SORTIE
    $date_pointage = date('Y-m-d');
    $heure_pointage = date('H:i:s');

    // Vérifier si l'employé existe
    $stmt = $db->prepare("SELECT * FROM employes WHERE code_employe = ?");
    $stmt->execute([$code_employe]);
    $emp = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($emp) {
        $stmt_insert = $db->prepare("INSERT INTO pointages (code_employe, type_pointage, date_pointage, heure_pointage) VALUES (?, ?, ?, ?)");
        $stmt_insert->execute([$code_employe, $type_pointage, $date_pointage, $heure_pointage]);
        $message = "Pointage de <strong>{$type_pointage}</strong> enregistré avec succès pour <strong>{$emp['prenom']} {$emp['nom']}</strong> à {$heure_pointage}.";
    } else {
        $message = "Code employé <strong>{$code_employe}</strong> introuvable dans la base GRH.";
        $alert_type = 'danger';
    }
}

// Récupération des pointages du jour
$pointages_jour = $db->query("SELECT p.*, e.nom, e.prenom, e.poste FROM pointages p JOIN employes e ON p.code_employe = e.code_employe WHERE p.date_pointage = CURDATE() ORDER BY p.id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-5">
    <div class="row justify-content-center mb-5">
        <div class="col-md-6">
            <div class="card card-omega text-white shadow-lg p-4 text-center">
                <h3 class="text-danger fw-bold mb-3"><i class="fas fa-fingerprint"></i> Borne de Pointage GRH</h3>
                <p class="text-light small mb-4">Scannez ou saisissez votre code employé pour enregistrer votre arrivée ou votre départ.</p>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?= $alert_type ?> text-center"><?= $message ?></div>
                <?php endif; ?>

                <form method="POST" class="needs-validation" novalidate>
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold">Code Employé :</label>
                        <input type="text" name="code_employe" class="form-control bg-dark text-white border-secondary form-control-lg text-center" placeholder="Ex: OMEGA-2026-001" required autofocus>
                    </div>
                    <div class="row mb-4">
                        <div class="col-6">
                            <button type="submit" name="type_pointage" value="ENTREE" class="btn btn-success w-100 py-3 fw-bold fs-5 shadow">
                                <i class="fas fa-sign-in-alt me-1"></i> ENTRÉE
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="submit" name="type_pointage" value="SORTIE" class="btn btn-danger w-100 py-3 fw-bold fs-5 shadow">
                                <i class="fas fa-sign-out-alt me-1"></i> SORTIE
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Historique des pointages du jour -->
    <div class="card card-omega text-white shadow-lg">
        <div class="card-header bg-dark border-bottom border-danger py-3">
            <h4 class="mb-0 text-danger"><i class="fas fa-list-check me-2"></i> Journal des Pointages du Jour (<?= date('d/m/Y') ?>)</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-striped table-bordered align-middle">
                    <thead>
                        <tr class="text-danger">
                            <th>Heure</th>
                            <th>Code</th>
                            <th>Employé</th>
                            <th>Poste</th>
                            <th class="text-center">Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pointages_jour)): ?>
                            <tr><td colspan="5" class="text-center text-muted">Aucun pointage enregistré pour aujourd'hui.</td></tr>
                        <?php else: ?>
                            <?php foreach($pointages_jour as $pt): ?>
                                <tr>
                                    <td><span class="badge bg-secondary"><?= $pt['heure_pointage'] ?></span></td>
                                    <td class="fw-bold text-warning"><?= $pt['code_employe'] ?></td>
                                    <td><?= htmlspecialchars($pt['prenom'] . ' ' . $pt['nom']) ?></td>
                                    <td><?= htmlspecialchars($pt['poste']) ?></td>
                                    <td class="text-center">
                                        <?php if($pt['type_pointage'] == 'ENTREE'): ?>
                                            <span class="badge bg-success px-3 py-2">ENTRÉE</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger px-3 py-2">SORTIE</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

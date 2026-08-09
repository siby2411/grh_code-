<?php
// carte_employe.php - Badge Professionnel avec QR Codes Distincts & Design Innovant
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
$employe = null;

if ($db && $id > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM employes WHERE id = ?");
        $stmt->execute([$id]);
        $employe = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {}
}
?>

<style>
    .badge-card {
        background: #1a1d20;
        border: 2px solid #dc3545;
        border-radius: 15px;
        color: #fff;
        box-shadow: 0 10px 25px rgba(0,0,0,0.5);
    }
    .header-badge {
        font-family: Arial, sans-serif;
        font-weight: bold;
        color: #dc3545;
        border-bottom: 2px solid #dc3545;
        padding-bottom: 8px;
        margin-bottom: 15px;
        letter-spacing: 0.5px;
    }
    .qr-container {
        background: #ffffff;
        padding: 12px;
        border: 2px solid #333;
        border-radius: 10px;
        display: inline-block;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
</style>

<div class="container my-5 text-white">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h2><i class="fas id-badge text-danger"></i> OMEGA GRH — Badge Pro & Design Sécurisé</h2>
        <div>
            <a href="liste_employes.php" class="btn btn-outline-light btn-sm me-2"><i class="fas fa-users me-1"></i> Retour Annuaire</a>
            <button onclick="window.print();" class="btn btn-success btn-sm"><i class="fas fa-print me-1"></i> Imprimer le Badge</button>
        </div>
    </div>

    <?php if (!$employe): ?>
        <div class="alert alert-danger shadow"><i class="fas fa-exclamation-triangle me-2"></i> Employé introuvable ou ID invalide.</div>
    <?php else: ?>
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card badge-card text-center p-4">
                    
                    <div class="header-badge">
                        OMEGA INFORMATIQUE CONSULTING<br>
                        <small style="font-size: 11px; color: #adb5bd;">GESTION GRH & RESTAURATION POS</small>
                    </div>

                    <div class="mb-3">
                        <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold shadow" style="width: 70px; height: 70px; font-size: 26px;">
                            <?= strtoupper(substr($employe['prenom'], 0, 1) . substr($employe['nom'], 0, 1)) ?>
                        </div>
                    </div>

                    <h4 class="fw-bold mb-1 text-white"><?= htmlspecialchars($employe['prenom'] . ' ' . $employe['nom']) ?></h4>
                    <p class="text-warning fw-bold mb-1"><?= htmlspecialchars($employe['poste'] ?? 'Collaborateur') ?></p>
                    <p class="badge bg-secondary mb-3">Matricule : <?= htmlspecialchars($employe['code_employe'] ?? 'EMP-'.$employe['id']) ?></p>

                    <div class="row g-3 justify-content-center mb-3">
                        <!-- Bloc QR Pointage -->
                        <div class="col-md-6 text-center">
                            <?php 
                            $url_pointage = urlencode("http://127.0.0.1:8000/pointage.php?employe_id=" . $employe['id']);
                            $qr_pointage_api = "https://api.qrserver.com/v1/create-qr-code/?size=130x130&data=" . $url_pointage;
                            
                            echo '
                            <div class="qr-container">
                                <div class="fw-bold mb-1 text-dark" style="font-size: 12px;">SCANNEZ POUR : <span class="text-primary">POINTAGE</span></div>
                                <img src="'.$qr_pointage_api.'" alt="QR Pointage" width="120" class="img-fluid">
                                <div class="mt-1 text-uppercase fw-bold text-dark" style="font-size: 10px;">Entrée / Sortie</div>
                            </div>';
                            ?>
                        </div>

                        <!-- Bloc QR Restau -->
                        <div class="col-md-6 text-center">
                            <?php 
                            $url_restau = urlencode("http://127.0.0.1:8000/restau_pos.php?employe_id=" . $employe['id']);
                            $qr_restau_api = "https://api.qrserver.com/v1/create-qr-code/?size=130x130&data=" . $url_restau;
                            
                            echo '
                            <div class="qr-container">
                                <div class="fw-bold mb-1 text-dark" style="font-size: 12px;">SCANNEZ POUR : <span class="text-danger">RESTAURANT</span></div>
                                <img src="'.$qr_restau_api.'" alt="QR Restau" width="120" class="img-fluid">
                                <div class="mt-1 text-uppercase fw-bold text-dark" style="font-size: 10px;">POS & Crédit Salaire</div>
                            </div>';
                            ?>
                        </div>
                    </div>

                    <div class="text-start small text-muted px-2 border-top border-secondary pt-2 mt-2">
                        <div><i class="fas fa-phone-alt me-1 text-danger"></i> Tél : <?= htmlspecialchars($employe['telephone'] ?? '-') ?></div>
                        <div><i class="fas fa-map-marker-alt me-1 text-danger"></i> Sacré-Cœur 3 VDN, Dakar, Sénégal</div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if (file_exists('footer.php')) include 'footer.php'; ?>

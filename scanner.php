<?php
// scanner.php
include 'header.php';

// Récupération du code depuis l'URL (ex: scanner.php?code=OMEGA-2026-001)
$code = isset($_GET['code']) ? htmlspecialchars($_GET['code']) : 'NON-DEFINI';

// Construction de l'URL pour le QR Code (API QRServer)
// Le data est l'URL de votre application de pointage ou d'avis
$url_data = urlencode("http://127.0.0.1:8000/pointage.php?code=" . $code);
$qr_api_url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . $url_data;
?>

<div class="container my-5 text-center">
    <div class="card card-omega shadow-lg border-danger mx-auto" style="max-width: 500px;">
        <div class="card-header bg-danger text-white fw-bold">
            <i class="fas fa-qrcode me-2"></i> Module de Pointage / Scan
        </div>
        <div class="card-body">
            <h4 class="text-warning mb-4">Code : <?= $code ?></h4>
            
            <!-- Affichage du QR Code généré par l'API -->
            <div class="bg-white p-3 d-inline-block rounded">
                <img src="<?= $qr_api_url ?>" alt="QR Code" class="img-fluid">
            </div>
            
            <p class="text-muted mt-3">
                Scannez ce code pour enregistrer le pointage ou accéder au dossier de l'employé.
            </p>
        </div>
        <div class="card-footer">
            <a href="liste_employes.php" class="btn btn-outline-light btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Retour à l'annuaire
            </a>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

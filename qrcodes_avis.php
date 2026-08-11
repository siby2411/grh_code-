<?php
include 'header.php';

$url_base = "http://127.0.0.1:8000/";
$url_pointage_global = $url_base . "pointage.php";
$url_avis_anonyme = $url_base . "donner_avis_anonyme.php";
$url_avis_select = $url_base . "donner_avis_select.php";

// Optimisation : taille 300x300 et correction d'erreur élevée ECC = H pour impression photocopieuse
$qr_pointage_api = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&ecc=H&data=" . urlencode($url_pointage_global);
$qr_anonyme_api = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&ecc=H&data=" . urlencode($url_avis_anonyme);
$qr_select_api = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&ecc=H&data=" . urlencode($url_avis_select);
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h2 class="text-danger fw-bold"><i class="fas fa-qrcode"></i> Centralisateur des QR Codes & Bornes GRH</h2>
        <p class="text-light">Imprimez ces codes pour l'accueil et les services d'Omega Informatique Consulting (Sacré-Cœur 3 VDN).</p>
    </div>

    <div class="row g-4 justify-content-center">
        <!-- 1. Borne Pointage -->
        <div class="col-md-4">
            <div class="card card-omega text-white text-center p-4 h-100">
                <div class="card-body">
                    <span class="badge bg-danger text-white mb-3 px-3 py-2">Pointage Entrée/Sortie</span>
                    <h4 class="h5 fw-bold text-white mb-3">Borne de Pointage</h4>
                    <div class="bg-white p-3 d-inline-block rounded shadow mb-3">
                        <img src="<?= $qr_pointage_api ?>" alt="QR Pointage" class="img-fluid" style="width: 180px; height: 180px;">
                    </div>
                    <div class="d-grid gap-2">
                        <a href="pointage.php" target="_blank" class="btn btn-omega btn-sm fw-bold">Ouvrir la borne</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Avis Anonyme -->
        <div class="col-md-4">
            <div class="card card-omega text-white text-center p-4 h-100">
                <div class="card-body">
                    <span class="badge bg-secondary text-warning mb-3 px-3 py-2">Climat Social</span>
                    <h4 class="h5 fw-bold text-white mb-3">Avis Anonyme</h4>
                    <div class="bg-white p-3 d-inline-block rounded shadow mb-3">
                        <img src="<?= $qr_anonyme_api ?>" alt="QR Anonyme" class="img-fluid" style="width: 180px; height: 180px;">
                    </div>
                    <div class="d-grid gap-2">
                        <a href="donner_avis_anonyme.php" target="_blank" class="btn btn-warning text-dark btn-sm fw-bold">Ouvrir le formulaire</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Avis avec Sélection Employé -->
        <div class="col-md-4">
            <div class="card card-omega text-white text-center p-4 h-100">
                <div class="card-body">
                    <span class="badge bg-success text-white mb-3 px-3 py-2">Évaluation Employé</span>
                    <h4 class="h5 fw-bold text-white mb-3">Sélection Code Employé</h4>
                    <div class="bg-white p-3 d-inline-block rounded shadow mb-3">
                        <img src="<?= $qr_select_api ?>" alt="QR Select" class="img-fluid" style="width: 180px; height: 180px;">
                    </div>
                    <div class="d-grid gap-2">
                        <a href="donner_avis_select.php" target="_blank" class="btn btn-success btn-sm fw-bold">Ouvrir le formulaire</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <a href="index.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Retour au Tableau de Bord</a>
    </div>
</div>

<?php include 'footer.php'; ?>

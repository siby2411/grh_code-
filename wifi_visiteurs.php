<?php
// wifi_visiteurs.php - Module IoT & Accès Wi-Fi Invité / Salles de Réunion (OMEGA Suite)
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (file_exists('header.php')) {
    include 'header.php';
} else {
    echo '<link rel="stylesheet" href="assets/css/bootstrap.min.css"><link rel="stylesheet" href="assets/css/all.min.css">';
}

// Configuration du réseau Wi-Fi invité
$ssid = "FAMILLE NDIAYE";
$password = "ozymandiasking";
$encryption = "WPA";

// Construction de la chaîne normalisée pour le QR Code Wi-Fi universel
$wifi_string = "WIFI:T:{$encryption};S:{$ssid};P:{$password};;";
$qr_wifi_api = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($wifi_string);
?>
<div class="container my-4 text-white" style="max-width: 600px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><span class="text-success"><i class="fas fa-wifi"></i></span> Wi-Fi Invité & Salles IoT</h2>
        <a href="index.php" class="btn btn-outline-light btn-sm"><i class="fas fa-home"></i> Accueil</a>
    </div>
    <div class="card bg-dark border-secondary shadow-lg text-center p-4">
        <h4 class="text-success mb-2">Accès Réseau Visiteur Sécurisé</h4>
        <p class="text-muted small">Scannez ce QR code avec un smartphone (Android / iOS) pour vous connecter instantanément et automatiquement au réseau sans fil de l'entreprise.</p>
        
        <div class="bg-white p-3 d-inline-block rounded shadow mb-3 mx-auto">
            <img src="<?= $qr_wifi_api ?>" alt="QR Code WiFi Invité" class="img-fluid">
        </div>
    </div>
</div>
<?php if (file_exists('footer.php')) include 'footer.php'; ?>

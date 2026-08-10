<?php
// salon_vcard.php - Générateur de vCard Salon embarquée (OMEGA Suite)
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (file_exists('header.php')) {
    include 'header.php';
} else {
    echo '<link rel="stylesheet" href="assets/css/bootstrap.min.css"><link rel="stylesheet" href="assets/css/all.min.css">';
}

$nom = $_GET['nom'] ?? 'Mohamed Siby';
$poste = $_GET['poste'] ?? 'Software Developer & IT Consultant';
$tel = $_GET['tel'] ?? '+221000000000';
$email = $_GET['email'] ?? 'contact@omegasuite.local';
$portfolio = $_GET['portfolio'] ?? 'https://github.com/';

// Construction d'une vCard texte brute standardisée directement dans le QR Code
$vcard_text = "BEGIN:VCARD\n" .
              "VERSION:3.0\n" .
              "FN:{$nom}\n" .
              "TITLE:{$poste}\n" .
              "TEL;TYPE=CELL:{$tel}\n" .
              "EMAIL:{$email}\n" .
              "URL:{$portfolio}\n" .
              "ORG:OMEGA SUITE Enterprise\n" .
              "END:VCARD";

$qr_api = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($vcard_text);
$vcf_url = "generer_vcf.php?nom=" . urlencode($nom) . "&poste=" . urlencode($poste) . "&tel=" . urlencode($tel) . "&email=" . urlencode($email) . "&portfolio=" . urlencode($portfolio);
?>
<div class="container my-4 text-white" style="max-width: 600px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><span class="text-danger"><i class="fas fa-id-badge"></i></span> OMEGA Salon & Visite Client</h2>
        <a href="index.php" class="btn btn-outline-light btn-sm"><i class="fas fa-home"></i> Accueil</a>
    </div>
    <div class="card bg-dark border-secondary shadow-lg text-center p-4">
        <h4 class="text-info mb-3"><?= htmlspecialchars($nom) ?></h4>
        <p class="text-muted"><?= htmlspecialchars($poste) ?></p>
        <div class="bg-white p-3 d-inline-block rounded shadow mb-3 mx-auto">
            <img src="<?= $qr_api ?>" alt="QR Code vCard Salon" class="img-fluid">
        </div>
        <p class="small text-secondary">Scannez ce QR code avec Google Lens ou l'appareil photo du smartphone : les coordonnées s'enregistrent directement dans le répertoire du client.</p>
        <a href="<?= $vcf_url ?>" class="btn btn-info fw-bold text-dark mt-2"><i class="fas fa-download me-1"></i> Télécharger le fichier .vcf direct</a>
    </div>
</div>
<?php if (file_exists('footer.php')) include 'footer.php'; ?>

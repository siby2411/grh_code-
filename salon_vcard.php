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
$tel = $_GET['tel'] ?? '+221 77 654 28 03';
$email = $_GET['email'] ?? 'sibymohamed24@gmail.com';
$portfolio = $_GET['portfolio'] ?? 'https://github.com/siby2411';

// Adresse et WhatsApp
$adresse = "SACRE CŒUR 3 VDN, Dakar, Sénégal";
$message_wa = "Bonjour, j'ai vu votre profil professionnel via le salon et je souhaite échanger avec vous.";
$whatsapp = "https://wa.me/221776542803?text=" . urlencode($message_wa);

// Construction de la vCard (texte brut standardisé pour le QR Code)
$vcard_text = "BEGIN:VCARD\n" .
              "VERSION:3.0\n" .
              "FN:{$nom}\n" .
              "TITLE:{$poste}\n" .
              "TEL;TYPE=CELL:{$tel}\n" .
              "EMAIL:{$email}\n" .
              "ADR;TYPE=WORK:;;{$adresse}\n" .
              "URL;TYPE=WhatsApp:{$whatsapp}\n" .
              "URL;TYPE=Portfolio:{$portfolio}\n" .
              "ORG:OMEGA SUITE Enterprise\n" .
              "END:VCARD";

$qr_api = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($vcard_text);
$vcf_url = "generer_vcf.php?nom=" . urlencode($nom) . "&poste=" . urlencode($poste) . "&tel=" . urlencode($tel) . "&email=" . urlencode($email) . "&portfolio=" . urlencode($portfolio) . "&adresse=" . urlencode($adresse);
?>
<div class="container my-4 text-white" style="max-width: 600px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><span class="text-danger"><i class="fas fa-id-badge"></i></span> OMEGA Salon & Visite</h2>
        <a href="index.php" class="btn btn-outline-light btn-sm"><i class="fas fa-home"></i> Accueil</a>
    </div>
    <div class="card bg-dark border-secondary shadow-lg text-center p-4">
        <h4 class="text-info mb-1"><?= htmlspecialchars($nom) ?></h4>
        <p class="text-muted mb-1"><?= htmlspecialchars($poste) ?></p>
        
        <!-- Affichage visuel de l'adresse sur l'écran -->
        <p class="small text-light mb-3">
            <i class="fas fa-map-marker-alt text-danger me-1"></i> <?= htmlspecialchars($adresse) ?>
        </p>
        
        <div class="bg-white p-3 d-inline-block rounded shadow mb-3 mx-auto">
            <img src="<?= $qr_api ?>" alt="QR Code vCard Salon" class="img-fluid">
        </div>
        
        <div class="d-grid gap-2">
            <a href="<?= htmlspecialchars($whatsapp) ?>" target="_blank" class="btn btn-success fw-bold">
                <i class="fab fa-whatsapp me-1"></i> Contact WhatsApp
            </a>
            <a href="<?= htmlspecialchars($vcf_url) ?>" class="btn btn-info fw-bold text-dark">
                <i class="fas fa-download me-1"></i> Télécharger fichier .vcf
            </a>
        </div>
        
        <p class="small text-secondary mt-3">Scannez ce QR code pour enregistrer toutes les informations (y compris l'adresse et le contact) directement dans votre répertoire.</p>
    </div>
</div>
<?php if (file_exists('footer.php')) include 'footer.php'; ?>

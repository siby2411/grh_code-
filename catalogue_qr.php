<?php
// catalogue_qr.php - Générateur de plusieurs fiches produits avec QR codes intégrés
ini_set('display_errors', 0);
require_once 'libs/phpqrcode.php';

// Dossier temporaire pour stocker les images des QR codes
$dir = 'temp_qrs/';
if (!file_exists($dir)) { mkdir($dir, 0777, true); }

// Liste de vos produits/équipements à encoder en dur
$produits = [
    [
        "titre" => "PC Portable Core i7",
        "specs" => "Core i7 | 8Go RAM | SSD 256Go",
        "prix" => "340 000 FCFA",
        "contact" => "Mohamed Siby (Consultant)\nTél: 77 654 28 03\nEmail: sibymohamed24@gmail.com"
    ],
    [
        "titre" => "PC Portable Core i5",
        "specs" => "Core i5 | 16Go RAM | SSD 512Go",
        "prix" => "280 000 FCFA",
        "contact" => "Mohamed Siby (Consultant)\nTél: 77 654 28 03\nEmail: sibymohamed24@gmail.com"
    ],
    [
        "titre" => "Écran Dell 24 pouces",
        "specs" => "Full HD IPS | HDMI / DisplayPort",
        "prix" => "95 000 FCFA",
        "contact" => "Mohamed Siby (Consultant)\nTél: 77 654 28 03\nEmail: sibymohamed24@gmail.com"
    ]
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catalogue QR Codes - OMEGA SUITE</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f2f2f2; color: #333; padding: 20px; }
        .no-print { text-align: center; margin-bottom: 30px; }
        .btn-print { background: #0d6efd; color: white; padding: 12px 25px; border: none; border-radius: 6px; font-size: 1rem; cursor: pointer; font-weight: bold; }
        
        /* Grille des étiquettes */
        .grid-catalogue { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; max-width: 1200px; margin: 0 auto; }
        
        /* Carte / Étiquette individuelle */
        .label-card { background: #fff; border: 2px dashed #ccc; border-radius: 10px; padding: 20px; text-align: center; page-break-inside: avoid; break-inside: avoid; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .label-card h3 { color: #dc3545; margin-bottom: 8px; font-size: 1.2rem; }
        .label-card p { font-size: 0.9rem; color: #555; margin-bottom: 5px; }
        .prix { font-weight: bold; color: #198754; font-size: 1.1rem; margin: 10px 0; }
        .qr-img { background: #fff; padding: 8px; border: 1px solid #ddd; border-radius: 6px; margin: 10px 0; display: inline-block; }
        .contact-box { font-size: 0.8rem; color: #771212; border-top: 1px solid #eee; margin-top: 10px; padding-top: 8px; white-space: pre-line; }

        /* Configuration spécifique pour l'impression papier */
        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none; }
            .grid-catalogue { display: block; }
            .label-card { border: 2px solid #000; page-break-after: always; margin-bottom: 20px; box-shadow: none; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button class="btn-print" onclick="window.print()">🖨️ Imprimer les étiquettes / fiches</button>
</div>

<div class="grid-catalogue">
    <?php foreach ($produits as $index => $prod): 
        // Constitution du texte "en dur" pour chaque QR code
        $payload = "FICHE PRODUIT\n" .
                   "Article: " . $prod['titre'] . "\n" .
                   "Specs: " . $prod['specs'] . "\n" .
                   "Prix: " . $prod['prix'] . "\n\n" .
                   "CONTACT:\n" . $prod['contact'];

        // Nom du fichier QR temporaire unique
        $qr_file = $dir . 'qr_' . $index . '.png';
        QRcode::png($payload, $qr_file, QR_ECLEVEL_M, 4, 4);
    ?>
        <div class="label-card">
            <h3><?= htmlspecialchars($prod['titre']) ?></h3>
            <p><?= htmlspecialchars($prod['specs']) ?></p>
            <div class="prix"><?= htmlspecialchars($prod['prix']) ?></div>
            
            <div class="qr-img">
                <img src="<?= $qr_file ?>" width="140" height="140" alt="QR Code">
            </div>
            
            <div class="contact-box"><?= nl2br(htmlspecialchars($prod['contact'])) ?></div>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>

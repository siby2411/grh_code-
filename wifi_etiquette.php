<?php
// wifi_etiquette.php - Générateur d'étiquette Wi-Fi officielle (OMEGA Suite)
ini_set('display_errors', 1);
error_reporting(E_ALL);

$ssid = "FAMILLE NDIAYE";
$password = "ozymandiasking";
$encryption = "WPA";

// La chaîne reste identique pour permettre la connexion automatique par scan
$wifi_string = "WIFI:T:{$encryption};S:{$ssid};P:{$password};;";
$qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=" . urlencode($wifi_string);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Étiquette Wi-Fi - <?= htmlspecialchars($ssid) ?></title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/all.min.css">
    <style>
        body { background: #fff; color: #000; font-family: Arial, sans-serif; }
        .wifi-badge-card {
            width: 350px;
            border: 3px solid #198754;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin: 40px auto;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        @media print {
            .no-print { display: none; }
            .wifi-badge-card { border: 2px solid #000; margin: 0 auto; box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="container text-center my-4">
        <div class="no-print mb-4">
            <button onclick="window.print();" class="btn btn-success fw-bold px-4 py-2"><i class="fas fa-print me-1"></i> Imprimer l'étiquette Wi-Fi</button>
            <a href="wifi_visiteurs.php" class="btn btn-outline-secondary px-3 py-2">Retour</a>
        </div>

        <div class="wifi-badge-card">
            <div class="text-success mb-2"><i class="fas fa-wifi fa-2x"></i></div>
            <h4 class="fw-bold mb-1 text-uppercase text-dark">ACCÈS WI-FI INVITÉ</h4>
            <p class="text-muted small mb-3">Scannez pour vous connecter automatiquement</p>
            
            <div class="bg-white p-2 d-inline-block border rounded shadow-sm mb-3">
                <img src="<?= $qr_url ?>" alt="QR Code WiFi" class="img-fluid" style="width: 200px; height: 200px;">
            </div>

            <div class="bg-light p-3 rounded text-center border small text-dark">
                <div class="mb-0"><strong>Réseau :</strong> <span class="text-danger fw-bold"><?= htmlspecialchars($ssid) ?></span></div>
                <!-- Mot de passe retiré ici -->
            </div>
            
            <div class="mt-3 text-muted" style="font-size: 11px;">OMEGA Suite — Espace Connecté Sécurisé</div>
        </div>
    </div>
</body>
</html>

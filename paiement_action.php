<?php
// paiement_action.php - Passerelle de Paiement Orange Money / Wave
$montant = isset($_GET['montant']) ? htmlspecialchars($_GET['montant']) : '1500';
$marchand = "+221776542803";
$ussd_code = "*145*2*1*" . $marchand . "*" . $montant . "#";
$ussd_url = "tel:" . urlencode($ussd_code);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Marchand - OMEGA SUITE</title>
    <style>
        body { background: #121212; color: #fff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; text-align: center; }
        .card { background: #1e1e1e; border: 1px solid #333; border-radius: 12px; padding: 30px; max-width: 400px; margin: 40px auto; box-shadow: 0 8px 25px rgba(0,0,0,0.5); }
        .btn-orange { background: #ff6600; color: white; padding: 15px 25px; border-radius: 8px; text-decoration: none; font-weight: bold; display: block; font-size: 1.1rem; margin-top: 20px; }
        .btn-orange:hover { background: #e65c00; }
        .badge { background: rgba(255,102,0,0.15); color: #ff8533; border: 1px solid #ff6600; padding: 5px 15px; border-radius: 20px; font-size: 0.85rem; display: inline-block; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="card">
        <span class="badge">OMEGA PAYMENTS</span>
        <h2 style="margin-bottom: 15px;">Paiement Orange Money</h2>
        <p style="color: #adb5bd; margin-bottom: 20px;">Marchand : <strong><?= $marchand ?></strong></p>
        <h1 style="color: #ff6600; margin-bottom: 25px;"><?= number_format($montant, 0, ',', ' ') ?> F CFA</h1>
        
        <p style="font-size: 0.9rem; color: #ccc; margin-bottom: 20px;">Cliquez sur le bouton ci-dessous pour lancer la transaction USSD sécurisée sur votre mobile :</p>
        
        <a href="<?= $ussd_url ?>" class="btn-orange">Valider le Paiement (USSD)</a>
    </div>
</body>
</html>

<?php
// index.php - OMEGA SUITE (Version Pro Intégrale - GRH, POS, Paiements, IoT & vCards)
ini_set('display_errors', 0);
require_once 'libs/phpqrcode.php';

// Connexion BDD locale
$db = null;
$nb_employes = 0;
$ca_restau = 0;
$pointages = 0;
$satisfaction = 0;
$nb_equipements = 0;

try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=grh_qrcode;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nb_employes = $db->query("SELECT COUNT(*) FROM employes")->fetchColumn() ?: 0;
    
    $chk_eq = $db->query("SHOW TABLES LIKE 'equipements'");
    if ($chk_eq->rowCount() > 0) $nb_equipements = $db->query("SELECT COUNT(*) FROM equipements")->fetchColumn() ?: 0;

    $chk_restau = $db->query("SHOW TABLES LIKE 'restau_commandes'");
    if ($chk_restau->rowCount() > 0) $ca_restau = $db->query("SELECT SUM(total) FROM restau_commandes")->fetchColumn() ?: 0;

    $chk_pt = $db->query("SHOW TABLES LIKE 'pointages'");
    if ($chk_pt->rowCount() > 0) $pointages = $db->query("SELECT COUNT(*) FROM pointages WHERE DATE(date_pointage) = CURDATE()")->fetchColumn() ?: 0;

    $chk_sat = $db->query("SHOW TABLES LIKE 'avis_clients'");
    if ($chk_sat->rowCount() > 0) $satisfaction = $db->query("SELECT ROUND(AVG(note), 1) FROM avis_clients")->fetchColumn() ?: 0;
} catch (Exception $e) {}

// Fonction de génération QR locale
function get_qr($data) {
    return 'qr_gen.php?data=' . urlencode($data);
}

// Payloads des QR codes (IoT / Paiement USSD / vCards / Wi-Fi)
$ussd_orange = "tel:*145*2*1*776542803*1500#";
$iot_device = "OMEGA_IOT_GATEWAY_SN_2026:DEVICE_CONNECTED:PORT_9010";
$vcard_med = "BEGIN:VCARD\nVERSION:3.0\nN:Diallo;Fatou;;;\nFN:Fatou Diallo (URGENCE)\nTEL;TYPE=CELL:+221770000000\nNOTE:SANG:O-;ALLERGIE:Aspirine;Diabétique Type 1\nEND:VCARD";
$vcard_pro = "BEGIN:VCARD\nVERSION:3.0\nN:Siby;Mohamed;;;\nFN:Mohamed Siby (Consultant)\nORG:OMEGA INFORMATIQUE CONSULTING\nTEL;TYPE=CELL:+221776542803\nEMAIL:sibymohamed24@gmail.com\nNOTE:Sacré-Cœur 3 VDN, Dakar\nEND:VCARD";
$wifi_visiteur = "WIFI:S:OMEGA_VISITEUR;T:WPA;P:omega2026visiteur;;";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OMEGA SUITE - GRH, POS, Équipements & QR Code</title>
    <!-- Style 100% Intégré pour un rendu garanti sans faille -->
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background-color: #121212; color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .text-center { text-align: center; }
        .mb-4 { margin-bottom: 1.5rem; }
        .mb-5 { margin-bottom: 3rem; }
        
        /* En-tête */
        h1 { color: #dc3545; font-size: 2.5rem; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 10px; }
        .badge-pro { background: rgba(220, 53, 69, 0.15); border: 1px solid #dc3545; color: #ff6b6b; padding: 6px 18px; border-radius: 20px; font-size: 0.85rem; display: inline-block; font-weight: bold; margin-bottom: 15px; }
        
        /* Boutons */
        .btn { display: inline-block; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: 0.2s; cursor: pointer; border: none; text-align: center; }
        .btn-outline-light { background: transparent; color: #f8f9fa; border: 1px solid #6c757d; }
        .btn-outline-light:hover { background: #f8f9fa; color: #121212; }
        .btn-warning { background: #ffc107; color: #000; }
        .btn-primary { background: #0d6efd; color: #fff; }
        .btn-success { background: #198754; color: #fff; }
        .btn-info { background: #0dcaf0; color: #000; }
        .btn-danger { background: #dc3545; color: #fff; }
        
        /* Grille des KPI */
        .grid-kpi { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.12); border-radius: 12px; padding: 20px; text-align: center; transition: transform 0.3s, border-color 0.3s; }
        .card:hover { transform: translateY(-3px); border-color: rgba(220, 53, 69, 0.5); box-shadow: 0 6px 20px rgba(0,0,0,0.4); }
        .card small { color: #adb5bd; font-size: 0.9rem; display: block; margin-bottom: 10px; }
        .card h3 { font-size: 1.8rem; margin-bottom: 15px; color: #fff; }
        
        /* Grille des modules principaux */
        .grid-modules { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .module-card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.12); border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; }
        .module-header { padding: 15px 20px; font-weight: bold; font-size: 1.1rem; color: white; }
        .bg-danger-custom { background: #dc3545; }
        .bg-primary-custom { background: #0d6efd; }
        .bg-success-custom { background: #198754; }
        .module-body { padding: 20px; display: flex; flex-direction: column; flex-grow: 1; }
        .module-body p { color: #adb5bd; font-size: 0.9rem; margin-bottom: 20px; flex-grow: 1; }
        .module-body .btn { width: 100%; margin-bottom: 8px; }

        /* Grille des QR Codes */
        .section-title { color: #0dcaf0; border-bottom: 1px solid #333; padding-bottom: 8px; margin-bottom: 20px; font-size: 1.4rem; }
        .grid-qr { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .qr-card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.12); border-radius: 12px; padding: 20px; text-align: center; display: flex; flex-direction: column; justify-content: space-between; }
        .qr-box { background: #ffffff; padding: 10px; border-radius: 8px; display: inline-block; margin: 10px auto; }
        .qr-card h5 { margin-bottom: 10px; font-size: 1rem; }
        .qr-card p { color: #adb5bd; font-size: 0.85rem; margin-bottom: 15px; }

        /* Footer */
        .footer { background: #000; border: 1px solid #333; padding: 20px; border-radius: 10px; text-align: center; margin-top: 40px; }
        .footer p { color: #adb5bd; font-size: 0.9rem; margin-bottom: 5px; }
        .footer strong { color: #fff; }
    </style>
</head>
<body>
<div class="container">
    <!-- En-tête Pro -->
    <div class="text-center mb-5">
        <span class="badge-pro">OMEGA INFORMATIQUE CONSULTING — DAKAR</span>
        <h1>OMEGA SUITE</h1>
        <p style="color: #6c757d; margin-bottom: 15px;">Module GRH, Pointage Intelligent & Traçabilité QR Code</p>
        <a href="http://localhost:9007" class="btn btn-outline-light">← Retour au Menu Global (Port 9007)</a>
    </div>

    <!-- Indicateurs Clés (KPI) -->
    <div class="grid-kpi">
        <div class="card" style="border-top: 4px solid #ffc107;">
            <small>Équipements</small>
            <h3><?= $nb_equipements ?></h3>
            <a href="equipements.php" class="btn btn-warning" style="width:100%;">Gérer</a>
        </div>
        <div class="card" style="border-top: 4px solid #0d6efd;">
            <small>Effectif Salariés</small>
            <h3><?= $nb_employes ?></h3>
            <a href="liste_employes.php" class="btn btn-primary" style="width:100%;">GRH</a>
        </div>
        <div class="card" style="border-top: 4px solid #198754;">
            <small>CA Restau</small>
            <h3><?= number_format($ca_restau, 0, ',', ' ') ?> F</h3>
            <a href="restau_pos.php" class="btn btn-success" style="width:100%;">POS</a>
        </div>
        <div class="card" style="border-top: 4px solid #0dcaf0;">
            <small>Pointages du Jour</small>
            <h3><?= $pointages ?></h3>
            <a href="pointage.php" class="btn btn-info" style="width:100%;">Voir</a>
        </div>
        <div class="card" style="border-top: 4px solid #ffc107;">
            <small>Satisfaction Clients</small>
            <h3><?= $satisfaction ?> / 5</h3>
            <a href="satisfaction.php" class="btn btn-warning" style="width:100%;">Avis</a>
        </div>
    </div>

    <!-- Accès Rapides aux Modules Métiers -->
    <div class="grid-modules">
        <div class="module-card">
            <div class="module-header bg-danger-custom">OMEGA RESTAU — POS</div>
            <div class="module-body">
                <p>Commandes, imputation sur crédit salaire et suivi des stocks en temps réel.</p>
                <a href="restau_pos.php" class="btn btn-danger">Point de Vente (POS)</a>
                <a href="restau_etats.php" class="btn btn-outline-light">États Financiers & Traçabilité</a>
            </div>
        </div>

        <div class="module-card">
            <div class="module-header bg-primary-custom">OMEGA GRH — Pointage</div>
            <div class="module-body">
                <p>Badgeage instantané par QR Code et sécurisation des présences du personnel.</p>
                <a href="pointage.php" class="btn btn-primary">Pointage Entrée & Sortie</a>
                <a href="liste_employes.php" class="btn btn-outline-light">Annuaire & Badges QR</a>
            </div>
        </div>

        <div class="module-card">
            <div class="module-header bg-success-custom">OMEGA PAYMENTS — Orange Money</div>
            <div class="module-body">
                <p>Gestion des encaissements et affichage QR Marchand centralisé (+221776542803).</p>
                <a href="paiements.php" class="btn btn-success">Gestion des Paiements</a>
            </div>
        </div>
    </div>

    <!-- Section Générateurs QR Codes, Paiements & vCards -->
    <h3 class="section-title">Générateurs Rapides (Orange Money, IoT, vCards & Wi-Fi)</h3>
    <div class="grid-qr">
        <div class="qr-card" style="border-color: #198754;">
            <h5 style="color: #198754;">QR Orange Money (USSD)</h5>
            <div class="qr-box">
                <img src="<?= get_qr($ussd_orange) ?>" alt="QR Orange Money" style="width: 120px; height: 120px; display: block;">
            </div>
            <p>Paiement direct marchand :<br><b>+221 77 654 28 03</b></p>
            <a href="paiements.php" class="btn btn-outline-success" style="width: 100%;">Module Paiements</a>
        </div>

        <div class="qr-card" style="border-color: #ffc107;">
            <h5 style="color: #ffc107;">Passerelle IoT & Terminaux</h5>
            <div class="qr-box">
                <img src="<?= get_qr($iot_device) ?>" alt="QR IoT Gateway" style="width: 120px; height: 120px; display: block;">
            </div>
            <p>Synchronisation matérielle & capteurs de badgeage</p>
            <a href="equipements.php" class="btn btn-outline-warning" style="width: 100%;">Gérer Parc IoT</a>
        </div>

        <div class="qr-card" style="border-color: #0dcaf0;">
            <h5 style="color: #0dcaf0;">vCard Consultant (Pro)</h5>
            <div class="qr-box">
                <img src="<?= get_qr($vcard_pro) ?>" alt="vCard Pro" style="width: 120px; height: 120px; display: block;">
            </div>
            <p>Carte de visite numérique Mohamed Siby</p>
            <a href="vcard.php" class="btn btn-outline-info" style="width: 100%;">Gérer vCard Pro</a>
        </div>

        <div class="qr-card">
            <h5 style="color: #fff;">vCard Médicale / Salon</h5>
            <div class="qr-box">
                <img src="<?= get_qr($vcard_med) ?>" alt="vCard Medical" style="width: 120px; height: 120px; display: block;">
            </div>
            <p>Fiche d'urgence & informations médicales</p>
            <a href="salon_vcard.php" class="btn btn-outline-light" style="width: 100%;">Module Salon & vCard</a>
        </div>

        <div class="qr-card">
            <h5 style="color: #fff;">Accès Wi-Fi Sécurisé</h5>
            <div class="qr-box">
                <img src="<?= get_qr($wifi_visiteur) ?>" alt="WiFi QR" style="width: 120px; height: 120px; display: block;">
            </div>
            <p>Connexion instantanée réseau Visiteurs</p>
            <a href="wifi_visiteurs.php" class="btn btn-outline-light" style="width: 100%;">Gérer Wi-Fi & Étiquettes</a>
        </div>
    </div>

    <!-- Footer Professionnel -->
    <div class="footer">
        <p><strong>OMEGA INFORMATIQUE CONSULTING</strong> — Sacré-Cœur 3 VDN, Dakar, Sénégal</p>
        <p style="font-size: 0.85rem; color: #6c757d; margin-top: 5px;">Consultant en informatique : <strong>Mr Mohamed Siby</strong> | Mobile : <strong>+221 77 654 28 03</strong> | Email : <strong>sibymohamed24@gmail.com</strong></p>
    </div>
</div>
</body>
</html>

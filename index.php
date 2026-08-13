<?php
// index.php - OMEGA SUITE (Version Pro Intégrale - GRH, POS, Paiements, Matériel, Quittance & Communication)
ini_set('display_errors', 0); 
require_once 'libs/phpqrcode.php';

$dir = 'temp_qrs/';
if (!file_exists($dir)) { mkdir($dir, 0777, true); }

// Connexion BDD locale
$db = null; $nb_employes = 0; $ca_restau = 0; $pointages = 0; $satisfaction = 0; $nb_equipements = 0;
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

// Fonction de génération QR locale standard
function get_qr($data) {
    return 'qr_gen.php?data=' . urlencode($data);
}

// --- PAYLOADS ET CONFIGURATIONS DE COMMUNICATION ---
$tel_marchand = "+221776542803";
$ussd_orange = "http://127.0.0.1:8000/paiement_action.php?montant=1500";
$ussd_direct = "tel:*145*2*1*%2B221776542803*1500%23";
$sms_direct = "sms:" . $tel_marchand . "?body=PAIEMENT_OMEGA_1500_FCFA";
$wifi_visiteur = "WIFI:S:OMEGA_VISITEUR;T:WPA;P:omega2026visiteur;;";
$iot_device = "OMEGA_IOT_GATEWAY_SN_2026:DEVICE_CONNECTED:PORT_9010";
$vcard_pro = "BEGIN:VCARD\nVERSION:3.0\nN:Siby;Mohamed;;;\nFN:Mohamed Siby (Consultant)\nORG:OMEGA INFORMATIQUE CONSULTING\nTEL;TYPE=CELL:+221776542803\nEMAIL:sibymohamed24@gmail.com\nNOTE:Sacré-Cœur 3 VDN, Dakar\nEND:VCARD";

// --- PAYLOAD ORANGE MONEY DIRECT ---
$om_payload = "ORANGE_MONEY_MERCHANT|TEL:" . $tel_marchand . "|AMOUNT:1500|REF:OMEGA-PAY";
$qr_om_file = $dir . 'orange_money_1500.png';
QRcode::png($om_payload, $qr_om_file, QR_ECLEVEL_M, 3, 2);

// --- 1. PAYLOAD MATÉRIEL EN DUR (Généré via phpqrcode) ---
$materiel_payload = "PC Core i7|8Go RAM|SSD 256Go|340 000 FCFA|Contact:776542803";
$qr_materiel_file = $dir . 'mat_core_i7.png';
QRcode::png($materiel_payload, $qr_materiel_file, QR_ECLEVEL_M, 3, 2);

// --- 2. TRAITEMENT QUITTANCE / FACTURE SÉCURISÉE ANTI-FRAUDE ---
$q_client = $_POST['q_client'] ?? 'Client Partenaire';
$q_tel = $_POST['q_tel'] ?? '+221 77 000 00 00';
$q_designation = $_POST['q_designation'] ?? 'Prestation Informatique & Matériel';
$q_montant = $_POST['q_montant'] ?? '340000';
$q_ref = 'OMEGA-' . date('Ymd') . '-' . rand(1000, 9999);
$q_sig = substr(hash('sha256', $q_ref . $q_client . $q_montant . 'OMEGA_KEY'), 0, 10);
$q_payload_court = "OMEGA_RECUS|" . $q_ref . "|" . $q_client . "|" . $q_montant . "FCFA|Sig:" . $q_sig;

$qr_quittance_file = $dir . 'quittance_' . $q_ref . '.png';
QRcode::png($q_payload_court, $qr_quittance_file, QR_ECLEVEL_L, 3, 2);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OMEGA SUITE - GRH, POS, Équipements, Communication & Quittances Sécurisées</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background-color: #121212; color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .text-center { text-align: center; }
        .mb-4 { margin-bottom: 1.5rem; }
        .mb-5 { margin-bottom: 3rem; }

        h1 { color: #dc3545; font-size: 2.5rem; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 10px; }
        .badge-pro { background: rgba(220, 53, 69, 0.15); border: 1px solid #dc3545; color: #ff6b6b; padding: 6px 18px; border-radius: 20px; font-size: 0.85rem; display: inline-block; font-weight: bold; margin-bottom: 15px; }

        .btn { display: inline-block; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: 0.2s; cursor: pointer; border: none; text-align: center; }
        .btn-outline-light { background: transparent; color: #f8f9fa; border: 1px solid #6c757d; }
        .btn-outline-light:hover { background: #f8f9fa; color: #121212; }
        .btn-warning { background: #ffc107; color: #000; }
        .btn-primary { background: #0d6efd; color: #fff; }
        .btn-success { background: #198754; color: #fff; }
        .btn-info { background: #0dcaf0; color: #000; }
        .btn-danger { background: #dc3545; color: #fff; }
        .btn-orange { background: #ff6600; color: #fff; }

        .grid-kpi { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.12); border-radius: 12px; padding: 20px; text-align: center; transition: transform 0.3s, border-color 0.3s; }
        .card:hover { transform: translateY(-3px); border-color: rgba(220, 53, 69, 0.5); box-shadow: 0 6px 20px rgba(0,0,0,0.4); }
        .card small { color: #adb5bd; font-size: 0.9rem; display: block; margin-bottom: 10px; }
        .card h3 { font-size: 1.8rem; margin-bottom: 15px; color: #fff; }

        .grid-modules { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .module-card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.12); border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; }
        .module-header { padding: 15px 20px; font-weight: bold; font-size: 1.1rem; color: white; }
        .bg-danger-custom { background: #dc3545; }
        .bg-primary-custom { background: #0d6efd; }
        .bg-success-custom { background: #198754; }
        .bg-warning-custom { background: #ffc107; color: #000; }
        .module-body { padding: 20px; display: flex; flex-direction: column; flex-grow: 1; }
        .module-body p { color: #adb5bd; font-size: 0.9rem; margin-bottom: 20px; flex-grow: 1; }
        .module-body .btn { width: 100%; margin-bottom: 8px; }

        .section-title { color: #0dcaf0; border-bottom: 1px solid #333; padding-bottom: 8px; margin-bottom: 20px; font-size: 1.4rem; }
        .grid-qr { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .qr-card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.12); border-radius: 12px; padding: 20px; text-align: center; display: flex; flex-direction: column; justify-content: space-between; }
        .qr-box { background: #ffffff; padding: 10px; border-radius: 8px; display: inline-block; margin: 10px auto; }
        .qr-card h5 { margin-bottom: 10px; font-size: 1rem; }
        .qr-card p { color: #adb5bd; font-size: 0.85rem; margin-bottom: 15px; }

        .form-control { width: 100%; padding: 6px; background: #1e1e1e; border: 1px solid #444; color: #fff; border-radius: 6px; margin-bottom: 8px; font-size: 0.85rem; }
        .form-group label { display: block; text-align: left; margin-bottom: 3px; font-size: 0.75rem; color: #adb5bd; }

        .footer { background: #000; border: 1px solid #333; padding: 20px; border-radius: 10px; text-align: center; margin-top: 40px; }
        .footer p { color: #adb5bd; font-size: 0.9rem; margin-bottom: 5px; }
        .footer strong { color: #fff; }

        /* --- MODE FACTURE MASQUÉ À L'ÉCRAN NORMAL --- */
        #printable-facture { display: none; }

        /* --- STYLES D'IMPRESSION TYPE FACTURE PROFESSIONNELLE --- */
        @media print {
            body * { visibility: hidden; }
            #printable-facture, #printable-facture * { visibility: visible; }
            #printable-facture {
                display: block !important;
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                background: #ffffff !important;
                color: #000000 !important;
                padding: 30px;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            .invoice-header { display: flex; justify-content: space-between; border-bottom: 2px solid #dc3545; padding-bottom: 15px; margin-bottom: 20px; }
            .invoice-company h2 { color: #dc3545; font-size: 1.5rem; text-transform: uppercase; margin-bottom: 5px; }
            .invoice-company p { font-size: 0.85rem; color: #555; line-height: 1.4; }
            .invoice-meta { text-align: right; }
            .invoice-meta h3 { font-size: 1.2rem; color: #333; margin-bottom: 5px; }
            .invoice-meta p { font-size: 0.85rem; color: #555; }
            .invoice-client { background: #f8f9fa; border: 1px solid #ddd; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; }
            .invoice-client h4 { color: #333; margin-bottom: 5px; font-size: 0.95rem; border-bottom: 1px solid #ccc; padding-bottom: 3px; }
            .invoice-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            .invoice-table th, .invoice-table td { border: 1px solid #ddd; padding: 10px; font-size: 0.85rem; text-align: left; }
            .invoice-table th { background: #dc3545; color: #fff; }
            .invoice-total { text-align: right; font-size: 1.1rem; font-weight: bold; margin-bottom: 30px; color: #000; }
            .invoice-footer { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #ddd; padding-top: 15px; }
            .invoice-qr img { width: 100px; height: 100px; }
            .invoice-sign { text-align: right; font-size: 0.85rem; color: #555; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="text-center mb-5">
        <span class="badge-pro">OMEGA INFORMATIQUE CONSULTING — DAKAR</span>
        <h1>OMEGA SUITE — TABLEAU DE BORD</h1>
        <p style="color: #6c757d; margin-bottom: 15px;">Modules de Communication, Sécurité, IoT & Gestion Intégrale</p>
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

    <!-- Modules Métiers -->
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

        <div class="module-card">
            <div class="module-header bg-warning-custom">OMEGA ÉQUIPEMENTS — Parc Matériel</div>
            <div class="module-body">
                <p>Inventaire, fiches de suivi et étiquetage QR code par actif matériel.</p>
                <a href="equipements.php" class="btn btn-warning" style="color: #000;">Gestion du Parc</a>
            </div>
        </div>
    </div>

    <!-- Section Modules de Communication, IoT & Sécurité -->
    <h3 class="section-title">Modules de Communication, IoT & Sécurité</h3>
    <div class="grid-qr">
        <!-- 1. QR Paiement Orange Money Direct -->
        <div class="qr-card" style="border-color: #ff6600;">
            <h5 style="color: #ff6600;">Orange Money (1500F)</h5>
            <div class="qr-box">
                <img src="<?= $qr_om_file ?>" alt="QR Orange Money" style="width: 120px; height: 120px; display: block;">
            </div>
            <p>Marchand : <b>+221 77 654 28 03</b><br>Encaissement instantané</p>
            <a href="<?= $ussd_direct ?>" class="btn btn-orange" style="width: 100%;">Lancer USSD</a>
        </div>

        <!-- 2. Accès Wi-Fi -->
        <div class="qr-card" style="border-color: #0dcaf0;">
            <h5 style="color: #0dcaf0;">Accès Wi-Fi</h5>
            <div class="qr-box">
                <img src="<?= get_qr($wifi_visiteur) ?>" alt="WiFi QR" style="width: 120px; height: 120px; display: block;">
            </div>
            <p>Réseau: <b>OMEGA_VISITEUR</b><br>Connexion instantanée</p>
            <span style="font-size: 0.75rem; color: #0dcaf0;">Sécurisé WPA</span>
        </div>

        <!-- 3. Messagerie SMS -->
        <div class="qr-card" style="border-color: #0d6efd;">
            <h5 style="color: #0d6efd;">Messagerie SMS</h5>
            <div class="qr-box">
                <img src="<?= get_qr($sms_direct) ?>" alt="SMS QR" style="width: 120px; height: 120px; display: block;">
            </div>
            <p>SMS pré-rempli vers :<br><b>+221 77 654 28 03</b></p>
            <a href="<?= $sms_direct ?>" class="btn btn-primary" style="width: 100%;">Envoyer SMS</a>
        </div>

        <!-- 4. Passerelle IoT -->
        <div class="qr-card" style="border-color: #ffc107;">
            <h5 style="color: #ffc107;">Passerelle IoT</h5>
            <div class="qr-box">
                <img src="<?= get_qr($iot_device) ?>" alt="IoT QR" style="width: 120px; height: 120px; display: block;">
            </div>
            <p>Synchronisation matérielle & capteurs de badgeage</p>
            <a href="equipements.php" class="btn btn-warning" style="width: 100%; color: #000;">Gérer IoT</a>
        </div>

        <!-- 5. vCard Pro Consultant -->
        <div class="qr-card" style="border-color: #198754;">
            <h5 style="color: #198754;">vCard Consultant</h5>
            <div class="qr-box">
                <img src="<?= get_qr($vcard_pro) ?>" alt="vCard Pro" style="width: 120px; height: 120px; display: block;">
            </div>
            <p>Carte de visite numérique Mohamed Siby</p>
            <a href="vcard.php" class="btn btn-success" style="width: 100%;">Voir vCard</a>
        </div>

        <!-- 6. Fiche Matériel en Dur -->
        <div class="qr-card" style="border-color: #ff6600;">
            <h5 style="color: #ff6600;">Fiche Matériel</h5>
            <div class="qr-box">
                <img src="<?= $qr_materiel_file ?>" alt="Matériel QR" style="width: 120px; height: 120px; display: block;">
            </div>
            <p><b>PC Core i7 (340 000 F)</b><br>Autonome (Sans serveur)</p>
            <span style="font-size: 0.75rem; color: #28a745;">✓ Étiquette Imprimable</span>
        </div>

        <!-- 7. Quittance / Facture Sécurisée avec Formulaire complet -->
        <div class="qr-card" style="border-color: #dc3545; text-align: left;">
            <h5 style="color: #dc3545; text-align: center;">Quittance / Facture</h5>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Client</label>
                    <input type="text" name="q_client" class="form-control" value="<?= htmlspecialchars($q_client) ?>">
                </div>
                <div class="form-group">
                    <label>Téléphone Client</label>
                    <input type="text" name="q_tel" class="form-control" value="<?= htmlspecialchars($q_tel) ?>">
                </div>
                <div class="form-group">
                    <label>Désignation</label>
                    <input type="text" name="q_designation" class="form-control" value="<?= htmlspecialchars($q_designation) ?>">
                </div>
                <div class="form-group">
                    <label>Montant (FCFA)</label>
                    <input type="text" name="q_montant" class="form-control" value="<?= htmlspecialchars($q_montant) ?>">
                </div>
                <button type="submit" class="btn btn-danger" style="width: 100%; font-size: 0.75rem; padding: 5px;">Générer Hash</button>
            </form>
            <div class="qr-box" style="margin: 8px auto; display: block; text-align: center;">
                <img src="<?= $qr_quittance_file ?>" alt="QR Quittance" style="width: 85px; height: 85px;">
            </div>
            <button onclick="window.print();" class="btn btn-outline-light" style="width: 100%; font-size: 0.75rem; padding: 5px; margin-top: 5px;">🖨️ Imprimer Facture</button>
        </div>
    </div>

    <!-- Modèle de Facture Professionnelle (Visible uniquement à l'impression) -->
    <div id="printable-facture">
        <div class="invoice-header">
            <div class="invoice-company">
                <h2>OMEGA INFORMATIQUE CONSULTING</h2>
                <p>Sacré-Cœur 3 VDN, Dakar, Sénégal<br>Tél : +221 77 654 28 03 | Email : sibymohamed24@gmail.com</p>
            </div>
            <div class="invoice-meta">
                <h3>FACTURE / QUITTANCE</h3>
                <p><b>Réf :</b> <?= htmlspecialchars($q_ref) ?></p>
                <p><b>Date :</b> <?= date('d/m/Y H:i') ?></p>
            </div>
        </div>

        <div class="invoice-client">
            <h4>INFORMATIONS CLIENT</h4>
            <p><b>Nom / Raison Sociale :</b> <?= htmlspecialchars($q_client) ?></p>
            <p><b>Téléphone :</b> <?= htmlspecialchars($q_tel) ?></p>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Désignation de la prestation / matériel</th>
                    <th style="text-align: right; width: 150px;">Montant (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= htmlspecialchars($q_designation) ?></td>
                    <td style="text-align: right;"><?= number_format((float)$q_montant, 0, ',', ' ') ?> FCFA</td>
                </tr>
            </tbody>
        </table>

        <div class="invoice-total">
            Total à payer : <?= number_format((float)$q_montant, 0, ',', ' ') ?> FCFA
        </div>

        <div class="invoice-footer">
            <div class="invoice-qr">
                <img src="<?= $qr_quittance_file ?>" alt="QR Sécurité Anti-Fraude">
                <div style="font-size: 0.6rem; font-family: monospace; margin-top: 3px;">Sig: <?= htmlspecialchars($q_sig) ?></div>
            </div>
            <div class="invoice-sign">
                <p><b>Le Consultant :</b> Mr Mohamed Siby</p>
                <br><br>
                <p>Cachet & Signature</p>
            </div>
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

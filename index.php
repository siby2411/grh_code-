<?php
// index.php - OMEGA SUITE (Version Exhaustive & 100% Hors-ligne)
ini_set('display_errors', 0);
require_once 'libs/phpqrcode.php';

// Connexion BDD locale (root sans mot de passe sur localhost)
$db = null;
$nb_employes = 0;
$ca_restau = 0;
$pointages = 0;               $satisfaction = 0;

try {
    $db = new PDO('mysql:host=127.0.0.1;dbname=grh_qrcode;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nb_employes = $db->query("SELECT COUNT(*) FROM employes")->fetchColumn() ?: 0;
    
    $chk_restau = $db->query("SHOW TABLES LIKE 'restau_commandes'");
    if ($chk_restau->rowCount() > 0) {
        $ca_restau = $db->query("SELECT SUM(total) FROM restau_commandes")->fetchColumn() ?: 0;
    }

    $chk_pt = $db->query("SHOW TABLES LIKE 'pointages'");
    if ($chk_pt->rowCount() > 0) {
        $pointages = $db->query("SELECT COUNT(*) FROM pointages WHERE DATE(date_pointage) = CURDATE()")->fetchColumn() ?: 0;
    }

    $chk_sat = $db->query("SHOW TABLES LIKE 'avis_clients'");
    if ($chk_sat->rowCount() > 0) {
        $satisfaction = $db->query("SELECT ROUND(AVG(note), 1) FROM avis_clients")->fetchColumn() ?: 0;
    }
} catch (Exception $e) {
    // Silencieux si la BDD est hors-ligne
}

// Fonction de génération QR locale
function get_qr($data) {
    return 'qr_gen.php?data=' . urlencode($data);
}

// Payloads des QR codes (Inclus USSD, vCards et les configurations WiFi)
$ussd = "tel:*145*2*1*776542803*1500#";
$vcard_med = "BEGIN:VCARD\nVERSION:3.0\nN:Diallo;Fatou;;;\nFN:Fatou Diallo (URGENCE)\nTEL;TYPE=CELL:+221770000000\nNOTE:SANG:O-;ALLERGIE:Aspirine;Diabétique Type 1\nEND:VCARD";
$vcard_pro = "BEGIN:VCARD\nVERSION:3.0\nN:Siby;Mohamed;;;\nFN:Mohamed Siby (Consultant)\nORG:OMEGA INFORMATIQUE CONSULTING\nTEL;TYPE=CELL:+221776542803\nEMAIL:sibymohamed24@gmail.com\nNOTE:Sacré-Cœur 3 VDN, Dakar\nEND:VCARD";

// Configs WiFi (Format standard WIFI:S:SSID;T:WPA;P:Password;;)
$wifi_visiteur = "WIFI:S:OMEGA_VISITEUR;T:WPA;P:omega2026visiteur;;";
$wifi_interne = "WIFI:S:OMEGA_INTERNE;T:WPA;P:omega2026interne;;";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>OMEGA SUITE - Dashboard Complet & Hors-ligne</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/all.min.css">
    <style>body { background-color: #121212; color: #fff; }</style>
</head>
<body>
<div class="container my-4">
    <!-- En-tête Pro -->
    <div class="text-center mb-5">
        <h1 class="text-danger fw-bold"><i class="fas fa-network-wired me-2"></i> OMEGA INFORMATIQUE CONSULTING</h1>
        <h4 class="text-secondary">Gestion du Personnel GRH, Pointage Intelligent & Traçabilité IoT</h4>
    </div>

    <!-- Indicateurs Clés (KPI) -->
    <div class="row text-center mb-5">
        <div class="col-md-3 mb-3">
            <div class="card bg-dark text-primary border-primary shadow p-3 h-100">
                <span class="small text-muted"><i class="fas fa-users"></i> Effectif Salariés</span>
                <h3 class="fw-bold mt-2"><?= $nb_employes ?></h3>
                <a href="liste_employes.php" class="btn btn-sm btn-outline-primary mt-2">Gérer GRH</a>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-dark text-success border-success shadow p-3 h-100">
                <span class="small text-muted"><i class="fas fa-cash-register"></i> CA Restau</span>
                <h3 class="fw-bold mt-2"><?= number_format($ca_restau, 0, ',', ' ') ?> F</h3>
                <a href="restau_pos.php" class="btn btn-sm btn-outline-success mt-2">Accès POS</a>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-dark text-info border-info shadow p-3 h-100">
                <span class="small text-muted"><i class="fas fa-fingerprint"></i> Pointages du Jour</span>
                <h3 class="fw-bold mt-2"><?= $pointages ?></h3>
                <a href="pointage.php" class="btn btn-sm btn-outline-info mt-2">Pointage Entrée/Sortie</a>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-dark text-warning border-warning shadow p-3 h-100">
                <span class="small text-muted"><i class="fas fa-star"></i> Satisfaction Clients</span>
                <h3 class="fw-bold mt-2"><?= $satisfaction ?> / 5</h3>
                <a href="satisfaction.php" class="btn btn-sm btn-outline-warning mt-2">Voir les Avis</a>
            </div>
        </div>
    </div>

    <!-- Accès Rapides aux Modules Métiers -->
    <div class="row mb-5">
        <div class="col-lg-4 mb-4">
            <div class="card bg-dark border-secondary shadow-lg h-100">
                <div class="card-header bg-danger text-white fw-bold">
                    <i class="fas fa-utensils me-2"></i> OMEGA RESTAU — POS
                </div>
                <div class="card-body">
                    <p class="text-muted small">Commandes, imputation sur crédit salaire et suivi des stocks.</p>
                    <div class="d-grid gap-2">
                        <a href="restau_pos.php" class="btn btn-danger btn-sm"><i class="fas fa-cash-register me-1"></i> Point de Vente (POS)</a>
                        <a href="restau_etats.php" class="btn btn-outline-light btn-sm"><i class="fas fa-chart-line me-1"></i> États Financiers & Traçabilité</a>
                        <a href="restau_crud.php" class="btn btn-outline-light btn-sm"><i class="fas fa-boxes me-1"></i> Gestion Stock & Plats</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card bg-dark border-secondary shadow-lg h-100">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-fingerprint me-2"></i> OMEGA GRH — Pointage
                </div>
                <div class="card-body">
                    <p class="text-muted small">Badgeage instantané par QR Code et gestion des présences.</p>
                    <div class="d-grid gap-2">
                        <a href="pointage.php" class="btn btn-primary btn-sm"><i class="fas fa-sign-in-alt me-1"></i> <b>Pointage Entrée & Sortie</b></a>
                        <a href="liste_employes.php" class="btn btn-outline-light btn-sm"><i class="fas fa-id-card me-1"></i> Annuaire & Badges QR</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card bg-dark border-secondary shadow-lg h-100">
                <div class="card-header bg-success text-white fw-bold">
                    <i class="fas fa-money-bill-wave me-2"></i> OMEGA PAYMENTS — Orange Money
                </div>
                <div class="card-body">
                    <p class="text-muted small">Gestion des encaissements, suivi des règlements et affichage QR Marchand.</p>
                    <div class="d-grid gap-2">
                        <a href="paiements.php" class="btn btn-success btn-sm fw-bold"><i class="fas fa-hand-holding-usd me-1"></i> Gestion des Paiements</a>
                        <a href="paiements.php" class="btn btn-outline-light btn-sm"><i class="fas fa-qrcode me-1"></i> Scanner QR Orange Money (+221776542803)</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Modules QR Codes Autonomes & Hors-ligne -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="p-4 bg-dark border border-secondary rounded shadow-lg">
                <h3 class="text-success mb-3"><i class="fas fa-qrcode me-2"></i> Modules Spéciaux (Génération Locale Hors-ligne)</h3>
                <p class="text-muted small">Générés mathématiquement par votre serveur local sans aucune connexion Internet requise.</p>

                <div class="row mt-4 text-center">
                    <!-- Bloc USSD -->
                    <div class="col-md-4 mb-3">
                        <div class="card bg-black border-warning p-3 h-100">
                            <h6 class="text-warning fw-bold">Paiement USSD (Wave / OM)</h6>
                            <div class="bg-white p-2 d-inline-block rounded shadow mx-auto my-2">
                                <img src="<?= get_qr($ussd) ?>" alt="QR USSD" class="img-fluid" style="width: 140px; height: 140px;">
                            </div>
                            <span class="font-monospace text-success small">tel:*145*2*1*...#</span>
                        </div>
                    </div>

                    <!-- Bloc vCard Consultant -->
                    <div class="col-md-4 mb-3">
                        <div class="card bg-black border-info p-3 h-100">
                            <h6 class="text-info fw-bold">vCard Consultant (Salon)</h6>
                            <div class="bg-white p-2 d-inline-block rounded shadow mx-auto my-2">
                                <img src="<?= get_qr($vcard_pro) ?>" alt="QR vCard" class="img-fluid" style="width: 140px; height: 140px;">
                            </div>
                            <span class="font-monospace text-light small">Mohamed Siby - OMEGA</span>
                        </div>
                    </div>

                    <!-- Bloc Urgence Médicale -->
                    <div class="col-md-4 mb-3">
                        <div class="card bg-black border-danger p-3 h-100">
                            <h6 class="text-danger fw-bold">Urgence Médicale (ICE)</h6>
                            <div class="bg-white p-2 d-inline-block rounded shadow mx-auto my-2">
                                <img src="<?= get_qr($vcard_med) ?>" alt="QR Medical" class="img-fluid" style="width: 140px; height: 140px;">
                            </div>
                            <span class="font-monospace text-info small">Fatou Diallo (Groupe O-)</span>
                        </div>
                    </div>
                </div>

                <!-- Ligne WiFi additionnelle -->
                <div class="row mt-3 text-center">
                    <!-- WiFi Visiteur -->
                    <div class="col-md-6 mb-3">
                        <div class="card bg-black border-secondary p-3 h-100">
                            <h6 class="text-light fw-bold"><i class="fas fa-wifi me-1"></i> WiFi Visiteur</h6>
                            <div class="bg-white p-2 d-inline-block rounded shadow mx-auto my-2">
                                <img src="<?= get_qr($wifi_visiteur) ?>" alt="QR WiFi Visiteur" class="img-fluid" style="width: 130px; height: 130px;">
                            </div>
                            <span class="font-monospace text-muted small">SSID: OMEGA_VISITEUR</span>
                        </div>
                    </div>

                    <!-- WiFi Interne -->
                    <div class="col-md-6 mb-3">
                        <div class="card bg-black border-secondary p-3 h-100">
                            <h6 class="text-light fw-bold"><i class="fas fa-wifi me-1"></i> WiFi Interne (Staff)</h6>
                            <div class="bg-white p-2 d-inline-block rounded shadow mx-auto my-2">
                                <img src="<?= get_qr($wifi_interne) ?>" alt="QR WiFi Interne" class="img-fluid" style="width: 130px; height: 130px;">
                            </div>
                            <span class="font-monospace text-muted small">SSID: OMEGA_INTERNE</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Footer Professionnel -->
    <div class="card bg-black border-secondary p-4 mt-5">
        <p class="text-center mb-1">
            <strong>OMEGA INFORMATIQUE CONSULTING</strong> — Sacré-Cœur 3 VDN, Dakar, Sénégal
        </p>
        <p class="text-center text-muted small mb-0">
            Consultant en informatique : <strong>Mr Mohamed Siby</strong> | Mobile : <strong>+221 77 654 28 03</strong> | Email : <strong>sibymohamed24@gmail.com</strong>
        </p>
        <hr class="bg-secondary my-3">
        <p class="text-center text-muted small mb-0">© 2026 Omega Suite GRH. Tous droits réservés.</p>
    </div>
</div>
</body>
</html>

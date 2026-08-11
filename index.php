<?php
// index.php - Dashboard Principal OMEGA Suite GRH, RESTAU & QR Codes Avancés & Natifs
ini_set('display_errors', 1); 
error_reporting(E_ALL);

if (file_exists('header.php')) {
    include 'header.php';
} else {
    echo '<link rel="stylesheet" href="assets/css/bootstrap.min.css"><link rel="stylesheet" href="assets/css/all.min.css">';
}

$db = null;
if (file_exists('config/database.php')) {
    require_once 'config/database.php';
    try { $db = (new Database())->getConnection(); } catch (Exception $e) {}
} elseif (file_exists('database.php')) {
    require_once 'database.php';
    try { $db = (new Database())->getConnection(); } catch (Exception $e) {}
}

$nb_employes = 0;
$nb_commandes = 0;
$ca_restau = 0;
$pointages_jour = 0;
$moyenne_satisfaction = 0;
$total_satisfaction = 0;

if ($db) {
    try {
        $nb_employes = $db->query("SELECT COUNT(*) as nb FROM employes")->fetch(PDO::FETCH_ASSOC)['nb'] ?? 0;
        $nb_commandes = $db->query("SELECT COUNT(*) as nb FROM restau_commandes")->fetch(PDO::FETCH_ASSOC)['nb'] ?? 0;
        $ca_restau = $db->query("SELECT SUM(total) as total FROM restau_commandes")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        $check_pt = $db->query("SHOW TABLES LIKE 'pointages'");
        if ($check_pt->rowCount() > 0) {
            $pointages_jour = $db->query("SELECT COUNT(*) as nb FROM pointages WHERE DATE(date_pointage) = CURDATE()")->fetch(PDO::FETCH_ASSOC)['nb'] ?? 0;
        }

        $check_sat = $db->query("SHOW TABLES LIKE 'avis_clients'");
        if ($check_sat->rowCount() > 0) {
            $sat_res = $db->query("SELECT AVG(note) as avg_note, COUNT(*) as total FROM avis_clients")->fetch(PDO::FETCH_ASSOC);
            $moyenne_satisfaction = round($sat_res['avg_note'] ?? 0, 1);
            $total_satisfaction = $sat_res['total'] ?? 0;
        }
    } catch (Exception $e) {}
}

// ==========================================
// DÉFINITION DES PAYLOADS QR CODES AVANCÉS
// ==========================================

// 1. Paiement USSD (Wave / Orange Money)
$ussd_data = "tel:*145*2*1*776542803*1500#";
$ussd_qr = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&ecc=H&data=" . urlencode($ussd_data);

// 2. Fiche Médicale d'Urgence vCard (Fatou Diallo)
$vcard_med_data = "BEGIN:VCARD\n" .
                  "VERSION:3.0\n" .
                  "N:Diallo;Fatou;;;\n" .
                  "FN:Fatou Diallo (URGENCE MEDICALE)\n" .
                  "TEL;TYPE=CELL:+221770000000\n" .
                  "NOTE:SANG:O-;ALLERGIE:Aspirine,Pénicilline;DIabétique de type 1;TRAITEMENT:Insuline\n" .
                  "END:VCARD";
$vcard_med_qr = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&ecc=H&data=" . urlencode($vcard_med_data);

// 3. vCard Salon / Consultant (Mohamed Siby)
$vcard_salon_data = "BEGIN:VCARD\n" .
                    "VERSION:3.0\n" .
                    "N:Siby;Mohamed;;;\n" .
                    "FN:Mohamed Siby (Consultant Informatique)\n" .
                    "ORG:OMEGA INFORMATIQUE CONSULTING\n" .
                    "TEL;TYPE=CELL:+221776542803\n" .
                    "EMAIL:sibymohamed24@gmail.com\n" .
                    "NOTE:Sacré-Cœur 3 VDN, Dakar, Sénégal\n" .
                    "END:VCARD";
$vcard_salon_qr = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&ecc=H&data=" . urlencode($vcard_salon_data);
?>

<div class="container my-4 text-white">
    <div class="text-center mb-5">
        <h1 class="fw-bold text-danger"><i class="fas fa-network-wired me-2"></i> OMEGA SUITE — Dashboard Intégré</h1>
        <p class="text-muted">Système de Gestion des Ressources Humaines, Pointage Intelligent, Restauration POS & Traçabilité IoT & Native</p>
    </div>

    <!-- Indicateurs Clés (KPI) -->
    <div class="row text-white mb-5">
        <div class="col-md-3 mb-3">
            <div class="card bg-dark border-primary shadow p-3 text-center h-100">
                <span class="text-muted small"><i class="fas fa-users me-1"></i> Effectif Salariés</span>
                <h3 class="text-primary fw-bold mt-2"><?= $nb_employes ?></h3>
                <a href="liste_employes.php" class="btn btn-sm btn-outline-primary mt-2">Gérer GRH</a>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-dark border-success shadow p-3 text-center h-100">
                <span class="text-muted small"><i class="fas fa-cash-register me-1"></i> CA Restau</span>
                <h3 class="text-success fw-bold mt-2"><?= number_format($ca_restau, 0, ',', ' ') ?> F</h3>
                <a href="restau_pos.php" class="btn btn-sm btn-outline-success mt-2">Accès POS</a>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-dark border-info shadow p-3 text-center h-100">
                <span class="text-muted small"><i class="fas fa-fingerprint me-1"></i> Pointages du Jour</span>
                <h3 class="text-info fw-bold mt-2"><?= $pointages_jour ?></h3>
                <a href="pointage.php" class="btn btn-sm btn-outline-info mt-2">Pointage Entrée / Sortie</a>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-dark border-warning shadow p-3 text-center h-100">
                <span class="text-muted small"><i class="fas fa-star me-1"></i> Satisfaction (<?= $total_satisfaction ?>)</span>
                <h3 class="text-warning fw-bold mt-2"><?= $moyenne_satisfaction ?> / 5</h3>
                <a href="satisfaction.php" class="btn btn-sm btn-outline-warning mt-2">Voir les Avis</a>
            </div>
        </div>
    </div>

    <!-- SECTION DOCUMENTÉE : QR CODES NATIFS HORS-LIGNE (USSD, vCard Médicale & vCard Salon) -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="p-4 bg-dark border border-secondary rounded shadow-lg">
                <h3 class="text-success mb-3"><i class="fas fa-qrcode me-2"></i> Modules Spéciaux : Paiement USSD & vCards Natives</h3>
                <p class="text-muted small">Ces modules exploitent les protocoles natifs des smartphones sans nécessiter de serveur web distant au moment du scan.</p>
                
                <div class="row mt-4">
                    <!-- Bloc USSD -->
                    <div class="col-md-4 mb-4">
                        <div class="card bg-black border-warning p-3 text-center h-100">
                            <h5 class="text-warning fw-bold">1. Paiement Mobile Direct (USSD)</h5>
                            <p class="text-muted small">Transfert marchand instantané (Wave / Orange Money).</p>
                            
                            <div class="bg-white p-2 d-inline-block rounded shadow mx-auto my-2">
                                <img src="<?= $ussd_qr ?>" alt="QR Code USSD" class="img-fluid" style="width: 160px; height: 160px;">
                            </div>
                            
                            <div class="text-start mt-2">
                                <span class="badge bg-warning text-dark">Protocole :</span>
                                <div class="font-monospace text-success mt-1 p-2 bg-dark rounded" style="font-size: 0.7rem; word-break: break-all;">
                                    <?= htmlspecialchars($ussd_data) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bloc vCard Salon / Consultant -->
                    <div class="col-md-4 mb-4">
                        <div class="card bg-black border-info p-3 text-center h-100">
                            <h5 class="text-info fw-bold">2. vCard Salon (Consultant)</h5>
                            <p class="text-muted small">Partage de carte de visite pro (Mohamed Siby).</p>
                            
                            <div class="bg-white p-2 d-inline-block rounded shadow mx-auto my-2">
                                <img src="<?= $vcard_salon_qr ?>" alt="QR Code vCard Salon" class="img-fluid" style="width: 160px; height: 160px;">
                            </div>
                            
                            <div class="text-start mt-2">
                                <span class="badge bg-info text-dark">Protocole :</span>
                                <div class="font-monospace text-light mt-1 p-2 bg-dark rounded" style="font-size: 0.65rem; white-space: pre-wrap; max-height: 80px; overflow-y: auto;">
<?= htmlspecialchars($vcard_salon_data) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bloc vCard Médicale -->
                    <div class="col-md-4 mb-4">
                        <div class="card bg-black border-danger p-3 text-center h-100">
                            <h5 class="text-danger fw-bold">3. vCard Urgence Médicale</h5>
                            <p class="text-muted small">Profil de secours (Fatou Diallo, Groupe O-).</p>
                            
                            <div class="bg-white p-2 d-inline-block rounded shadow mx-auto my-2">
                                <img src="<?= $vcard_med_qr ?>" alt="QR Code vCard Médicale" class="img-fluid" style="width: 160px; height: 160px;">
                            </div>
                            
                            <div class="text-start mt-2">
                                <span class="badge bg-danger text-white">Protocole :</span>
                                <div class="font-monospace text-info mt-1 p-2 bg-dark rounded" style="font-size: 0.65rem; white-space: pre-wrap; max-height: 80px; overflow-y: auto;">
<?= htmlspecialchars($vcard_med_data) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accès Rapides aux Modules de Base -->
    <div class="row mb-4">
        <div class="col-lg-4 mb-4">
            <div class="card bg-dark border-secondary shadow-lg h-100">
                <div class="card-header bg-danger text-white fw-bold">
                    <i class="fas fa-utensils me-2"></i> OMEGA RESTAU — POS
                </div>
                <div class="card-body">
                    <p class="text-muted small">Commandes par QR Code, imputation sur crédit salaire et suivi des stocks.</p>
                    <div class="d-grid gap-2">
                        <a href="restau_pos.php" class="btn btn-danger btn-sm"><i class="fas fa-cash-register me-1"></i> Point de Vente (POS)</a>
                        <a href="restau_etats.php" class="btn btn-outline-light btn-sm"><i class="fas fa-chart-line me-1"></i> États Financiers & Traçabilité</a>
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
                    <p class="text-muted small">Badgeage instantané par QR Code et enregistrement des pointages d'entrées et de sorties.</p>
                    <div class="d-grid gap-2">
                        <a href="pointage.php" class="btn btn-primary btn-sm"><i class="fas fa-sign-in-alt me-1"></i> <b>Pointage Entrée & Sortie</b></a>
                        <a href="liste_employes.php" class="btn btn-outline-light btn-sm"><i class="fas fa-id-card me-1"></i> Annuaire & Badges QR</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card bg-dark border-secondary shadow-lg h-100">
                <div class="card-header bg-warning text-dark fw-bold">
                    <i class="fas fa-bullhorn me-2"></i> OMEGA GRH — Avis & Paie
                </div>
                <div class="card-body">
                    <p class="text-muted small">Diffusion des notes de service, avis RH et édition des bulletins de paie.</p>
                    <div class="d-grid gap-2">
                        <a href="avis_grh.php" class="btn btn-warning btn-sm text-dark fw-bold"><i class="fas fa-bullhorn me-1"></i> Avis & Communications GRH</a>
                        <a href="liste_employes.php" class="btn btn-outline-light btn-sm"><i class="fas fa-file-invoice-dollar me-1"></i> Bulletins de Paie</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (file_exists('footer.php')) include 'footer.php'; ?>

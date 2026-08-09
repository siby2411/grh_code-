<?php
// index.php - Dashboard Principal OMEGA Suite GRH & RESTAU
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
$avis_grh = [];

if ($db) {
    try {
        $nb_employes = $db->query("SELECT COUNT(*) as nb FROM employes")->fetch(PDO::FETCH_ASSOC)['nb'] ?? 0;
        $nb_commandes = $db->query("SELECT COUNT(*) as nb FROM restau_commandes")->fetch(PDO::FETCH_ASSOC)['nb'] ?? 0;
        $ca_restau = $db->query("SELECT SUM(total) as total FROM restau_commandes")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // Vérification table pointage si elle existe
        $check_pt = $db->query("SHOW TABLES LIKE 'pointages'");
        if ($check_pt->rowCount() > 0) {
            $pointages_jour = $db->query("SELECT COUNT(*) as nb FROM pointages WHERE DATE(date_pointage) = CURDATE()")->fetch(PDO::FETCH_ASSOC)['nb'] ?? 0;
        }

        // Vérification table avis_grh si elle existe
        $check_avis = $db->query("SHOW TABLES LIKE 'avis_grh'");
        if ($check_avis->rowCount() > 0) {
            $avis_grh = $db->query("SELECT * FROM avis_grh ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {}
}
?>

<div class="container my-4 text-white">
    <div class="text-center mb-5">
        <h1 class="fw-bold text-danger"><i class="fas fa-network-wired me-2"></i> OMEGA SUITE — Dashboard Intégré</h1>
        <p class="text-muted">Système de Gestion des Ressources Humaines, Pointage Intelligent & Restauration POS</p>
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
                <span class="text-muted small"><i class="fas fa-cash-register me-1"></i> Chiffre d'Affaires Restau</span>
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
                <span class="text-muted small"><i class="fas fa-bullhorn me-1"></i> Avis & Notes GRH</span>
                <h3 class="text-warning fw-bold mt-2"><?= count($avis_grh) ?></h3>
                <a href="avis_grh.php" class="btn btn-sm btn-outline-warning mt-2">Consulter Notes</a>
            </div>
        </div>
    </div>

    <!-- Accès Rapides aux Modules -->
    <div class="row">
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
                    <p class="text-muted small">Diffusion des notes de service, avis RH et édition des bulletins de paie avec déduction restau.</p>
                    <div class="d-grid gap-2">
                        <a href="avis_grh.php" class="btn btn-warning btn-sm text-dark fw-bold"><i class="fas fa-bullhorn me-1"></i> Avis & Communications GRH</a>
                        <a href="liste_employes.php" class="btn btn-outline-light btn-sm"><i class="fas fa-file-invoice-dollar me-1"></i> Bulletins de Paie & Déductions</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (file_exists('footer.php')) include 'footer.php'; ?>

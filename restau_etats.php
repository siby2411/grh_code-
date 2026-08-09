<?php
// restau_etats.php - États Financiers & Suivi Global / Par Employé
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

$employe_filtre = isset($_GET['employe_id']) ? intval($_GET['employe_id']) : 0;
$employes = [];
$total_ca = 0;
$total_commandes = 0;
$ventes_par_paiement = [];
$top_produits = [];
$historique_commandes = [];
$cumul_employe_details = [];

if ($db) {
    try {
        $employes = $db->query("SELECT * FROM employes ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);

        // Filtre global ou par employé
        if ($employe_filtre > 0) {
            $stmt_ca = $db->prepare("SELECT SUM(total) as total, COUNT(*) as nb FROM restau_commandes WHERE employe_id = ?");
            $stmt_ca->execute([$employe_filtre]);
            $res_ca = $stmt_ca->fetch(PDO::FETCH_ASSOC);
            $total_ca = $res_ca['total'] ?? 0;
            $total_commandes = $res_ca['nb'] ?? 0;

            $stmt_hist = $db->prepare("SELECT c.*, e.nom, e.prenom, e.code_employe FROM restau_commandes c LEFT JOIN employes e ON c.employe_id = e.id WHERE c.employe_id = ? ORDER BY c.id DESC");
            $stmt_hist->execute([$employe_filtre]);
            $historique_commandes = $stmt_hist->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $total_ca = $db->query("SELECT SUM(total) as total FROM restau_commandes")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            $total_commandes = $db->query("SELECT COUNT(*) as nb FROM restau_commandes")->fetch(PDO::FETCH_ASSOC)['nb'] ?? 0;
            $ventes_par_paiement = $db->query("SELECT mode_paiement, SUM(total) as somme FROM restau_commandes GROUP BY mode_paiement")->fetchAll(PDO::FETCH_ASSOC);
            $top_produits = $db->query("SELECT p.nom, SUM(i.quantite) as total_qte, SUM(i.quantite * i.prix_unitaire) as ca_prod FROM restau_commande_items i JOIN restau_produits p ON i.produit_id = p.id GROUP BY p.id ORDER BY total_qte DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            $historique_commandes = $db->query("SELECT c.*, e.nom, e.prenom, e.code_employe FROM restau_commandes c LEFT JOIN employes e ON c.employe_id = e.id ORDER BY c.id DESC LIMIT 25")->fetchAll(PDO::FETCH_ASSOC);
        }

        // Cumul par employé pour l'état analytique dédié
        $cumul_employe_details = $db->query("
            SELECT e.id, e.code_employe, e.prenom, e.nom, e.poste, 
                   COUNT(c.id) as nb_commandes, SUM(c.total) as total_achats 
            FROM employes e 
            LEFT JOIN restau_commandes c ON e.id = c.employe_id 
            GROUP BY e.id 
            ORDER BY total_achats DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $e) {}
}
?>

<div class="container my-4 text-white">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-chart-line text-danger"></i> OMEGA RESTAU — États Financiers & Traçabilité (Globale & Par Employé)</h2>
        <div>
            <a href="index.php" class="btn btn-outline-light btn-sm me-2"><i class="fas fa-home me-1"></i> Dashboard</a>
            <a href="restau_pos.php" class="btn btn-danger btn-sm me-2"><i class="fas fa-cash-register me-1"></i> Retour au POS</a>
            <button onclick="window.print();" class="btn btn-success btn-sm"><i class="fas fa-print me-1"></i> Imprimer</button>
        </div>
    </div>

    <!-- Filtre par Employé -->
    <div class="card bg-dark border-secondary shadow mb-4">
        <div class="card-body">
            <form method="GET" class="row align-items-center">
                <div class="col-md-8">
                    <label class="form-label fw-bold text-warning"><i class="fas fa-filter me-1"></i> Filtrer les états par Salarié / Employé :</label>
                    <select name="employe_id" class="form-control bg-dark text-white border-secondary">
                        <option value="0">-- Vue Globale (Tous les Employés & Externes) --</option>
                        <?php foreach ($employes as $emp): ?>
                            <option value="<?= $emp['id'] ?>" <?= ($employe_filtre == $emp['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($emp['prenom'] . ' ' . $emp['nom']) ?> (<?= $emp['code_employe'] ?? 'EMP-'.$emp['id'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mt-3 mt-md-0 text-end">
                    <button type="submit" class="btn btn-danger w-100 fw-bold py-2"><i class="fas fa-search me-1"></i> Appliquer le Filtre</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Indicateurs Clés (KPI) -->
    <div class="row text-white mb-4">
        <div class="col-md-6 mb-3">
            <div class="card bg-dark border-success shadow p-3 text-center">
                <span class="text-muted small"><?= $employe_filtre > 0 ? 'Total Dépenses / Crédit Salaire Employé' : "Chiffre d'Affaires Global Restau" ?></span>
                <h3 class="text-success fw-bold mt-1"><?= number_format($total_ca, 0, ',', ' ') ?> F CFA</h3>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card bg-dark border-info shadow p-3 text-center">
                <span class="text-muted small"><?= $employe_filtre > 0 ? 'Nombre de Commandes de l’Employé' : 'Nombre Total de Commandes Enregistrées' ?></span>
                <h3 class="text-info fw-bold mt-1"><?= $total_commandes ?></h3>
            </div>
        </div>
    </div>

    <?php if ($employe_filtre == 0): ?>
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card bg-dark border-secondary shadow-lg h-100">
                <div class="card-header bg-secondary fw-bold">Répartition par Mode de Paiement</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush bg-transparent">
                        <?php if (empty($ventes_par_paiement)): ?>
                            <li class="list-group-item bg-dark text-muted">Aucune donnée disponible.</li>
                        <?php else: foreach ($ventes_par_paiement as $v): ?>
                            <li class="list-group-item bg-dark text-white d-flex justify-content-between align-items-center border-secondary">
                                <span><?= htmlspecialchars($v['mode_paiement']) ?></span>
                                <span class="badge bg-success fs-6"><?= number_format($v['somme'], 0, ',', ' ') ?> F CFA</span>
                            </li>
                        <?php endforeach; endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card bg-dark border-secondary shadow-lg h-100">
                <div class="card-header bg-secondary fw-bold">Top 5 des Plats & Boissons les Plus Demandés</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush bg-transparent">
                        <?php if (empty($top_produits)): ?>
                            <li class="list-group-item bg-dark text-muted">Aucune donnée disponible.</li>
                        <?php else: foreach ($top_produits as $tp): ?>
                            <li class="list-group-item bg-dark text-white d-flex justify-content-between align-items-center border-secondary">
                                <span><?= htmlspecialchars($tp['nom']) ?> <small class="text-muted">(<?= $tp['total_qte'] ?> vendus)</small></span>
                                <span class="badge bg-warning text-dark fs-6"><?= number_format($tp['ca_prod'], 0, ',', ' ') ?> F CFA</span>
                            </li>
                        <?php endforeach; endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Synthèse & Cumul par Employé (Imputation Crédit Salaire) -->
    <div class="card bg-dark border-secondary shadow-lg mb-4">
        <div class="card-header bg-danger fw-bold"><i class="fas id-badge me-1"></i> État Synthétique des Commandes & Crédits Salaires par Employé</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-striped align-middle mb-0">
                    <thead>
                        <tr class="text-secondary">
                            <th>Code</th>
                            <th>Nom & Prénom</th>
                            <th>Poste</th>
                            <th class="text-center">Commandes</th>
                            <th class="text-end">Total Imputable (F CFA)</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cumul_employe_details)): ?>
                            <tr><td colspan="6" class="text-center text-muted">Aucun employé trouvé.</td></tr>
                        <?php else: foreach ($cumul_employe_details as $ce): ?>
                            <tr>
                                <td class="text-warning fw-bold"><?= htmlspecialchars($ce['code_employe'] ?? 'EMP-'.$ce['id']) ?></td>
                                <td><?= htmlspecialchars($ce['prenom'] . ' ' . $ce['nom']) ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($ce['poste'] ?? 'Employé') ?></span></td>
                                <td class="text-center"><span class="badge bg-info"><?= $ce['nb_commandes'] ?></span></td>
                                <td class="text-end text-success fw-bold"><?= number_format($ce['total_achats'] ?? 0, 0, ',', ' ') ?> F</td>
                                <td class="text-center">
                                    <a href="restau_etats.php?employe_id=<?= $ce['id'] ?>" class="btn btn-sm btn-outline-warning"><i class="fas fa-eye"></i> Voir Détails</a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Historique des Commandes (Global ou Filtré) -->
    <div class="card bg-dark border-secondary shadow-lg">
        <div class="card-header bg-secondary fw-bold">
            <?= $employe_filtre > 0 ? 'Historique des Commandes de l’Employé Sélectionné' : 'Journal Global des Commandes & Traçabilité QR' ?>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-striped align-middle mb-0">
                    <thead>
                        <tr class="text-secondary">
                            <th>ID</th>
                            <th>Date / Heure</th>
                            <th>Table</th>
                            <th>Employé / Traçabilité QR</th>
                            <th>Mode Paiement</th>
                            <th class="text-end">Montant Total</th>
                            <th class="text-center">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($historique_commandes)): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">Aucune commande enregistrée pour ce filtre.</td></tr>
                        <?php else: foreach ($historique_commandes as $cmd): ?>
                            <tr>
                                <td class="text-warning fw-bold">#<?= $cmd['id'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($cmd['date_commande'])) ?></td>
                                <td><?= htmlspecialchars($cmd['table_num']) ?></td>
                                <td>
                                    <?php if (!empty($cmd['nom'])): ?>
                                        <span class="badge bg-info text-dark fw-bold">
                                            <i class="fas fa-user-check me-1"></i> <?= htmlspecialchars($cmd['prenom'] . ' ' . $cmd['nom']) ?> 
                                            <small class="text-muted">(<?= htmlspecialchars($cmd['code_employe'] ?? 'EMP-'.$cmd['employe_id']) ?>)</small>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">Client Externe / Comptant</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($cmd['mode_paiement']) ?></span></td>
                                <td class="text-end text-success fw-bold"><?= number_format($cmd['total'], 0, ',', ' ') ?> F</td>
                                <td class="text-center"><span class="badge bg-success"><?= htmlspecialchars($cmd['statut']) ?></span></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if (file_exists('footer.php')) include 'footer.php'; ?>

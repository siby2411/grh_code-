<?php
// bulletin_paie.php - Génération du Bulletin de Paie & Prise en compte Crédit Restau
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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$employe = null;
$total_restau_credit = 0;

if ($db && $id > 0) {
    try {
        $stmt = $db->prepare("SELECT * FROM employes WHERE id = ?");
        $stmt->execute([$id]);
        $employe = $stmt->fetch(PDO::FETCH_ASSOC);

        // Calcul des consommations restaurant non payées ou cumulées du mois en cours (Crédit Salaire)
        $stmt_restau = $db->prepare("SELECT SUM(total) as total_restau FROM restau_commandes WHERE employe_id = ? AND mode_paiement = 'Crédit Salaire'");
        $stmt_restau->execute([$id]);
        $res_restau = $stmt_restau->fetch(PDO::FETCH_ASSOC);
        $total_restau_credit = $res_restau['total_restau'] ?? 0;

    } catch (Exception $e) {}
}

$salaire_base = $employe['salaire_base'] ?? 0;
$net_a_payer = $salaire_base - $total_restau_credit;
?>

<div class="container my-5 text-white">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-file-invoice-dollar text-danger"></i> OMEGA GRH — Bulletin de Paie & Déduction Restau</h2>
        <div>
            <a href="liste_employes.php" class="btn btn-outline-light btn-sm me-2"><i class="fas fa-users me-1"></i> Annuaire</a>
            <button onclick="window.print();" class="btn btn-success btn-sm"><i class="fas fa-print me-1"></i> Imprimer le Bulletin</button>
        </div>
    </div>

    <?php if (!$employe): ?>
        <div class="alert alert-danger shadow"><i class="fas fa-exclamation-triangle me-2"></i> Employé introuvable ou ID invalide.</div>
    <?php else: ?>
        <div class="card bg-dark border-secondary shadow-lg p-4">
            <div class="text-center border-bottom border-secondary pb-3 mb-4">
                <h3 class="fw-bold text-danger">OMEGA INFORMATIQUE CONSULTING</h3>
                <p class="text-muted mb-1">Sacré-Cœur 3 VDN, Dakar, Sénégal | Tél : +221 77 654 28 03</p>
                <h5 class="text-warning mt-3">BULLETIN DE PAIE — <?= strtoupper(date('F Y')) ?></h5>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Matricule :</strong> <?= htmlspecialchars($employe['code_employe'] ?? 'EMP-'.$employe['id']) ?></p>
                    <p><strong>Nom & Prénom :</strong> <?= htmlspecialchars($employe['prenom'] . ' ' . $employe['nom']) ?></p>
                    <p><strong>Poste :</strong> <?= htmlspecialchars($employe['poste'] ?? 'Employé') ?></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p><strong>Date d'édition :</strong> <?= date('d/m/Y') ?></p>
                    <p><strong>Mode de Règlement :</strong> Virement / Espèces</p>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-dark table-bordered align-middle">
                    <thead>
                        <tr class="text-secondary bg-secondary text-white">
                            <th>Rubrique / Désignation</th>
                            <th class="text-end">Base / Quantité</th>
                            <th class="text-end">Gains (F CFA)</th>
                            <th class="text-end">Retenues (F CFA)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Salaire de Base</td>
                            <td class="text-end">1 Mois</td>
                            <td class="text-end text-success fw-bold"><?= number_format($salaire_base, 0, ',', ' ') ?></td>
                            <td class="text-end">-</td>
                        </tr>
                        <?php if ($total_restau_credit > 0): ?>
                        <tr>
                            <td>Déduction Crédit Restauration (OMEGA RESTAU)</td>
                            <td class="text-end">Consommations POS</td>
                            <td class="text-end">-</td>
                            <td class="text-end text-danger fw-bold"><?= number_format($total_restau_credit, 0, ',', ' ') ?></td>
                        </tr>
                        <?php else: ?>
                        <tr>
                            <td>Déduction Crédit Restauration (OMEGA RESTAU)</td>
                            <td class="text-end">Aucun crédit</td>
                            <td class="text-end">-</td>
                            <td class="text-end text-muted">0</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-secondary text-white">
                            <th colspan="2" class="text-end uppercase">TOTAUX :</th>
                            <th class="text-end text-success"><?= number_format($salaire_base, 0, ',', ' ') ?> F</th>
                            <th class="text-end text-danger"><?= number_format($total_restau_credit, 0, ',', ' ') ?> F</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="card bg-secondary text-white p-3 mb-4 text-end">
                <h4 class="mb-0">NET À PAYER : <span class="text-warning fw-bold"><?= number_format($net_a_payer, 0, ',', ' ') ?> F CFA</span></h4>
            </div>

            <div class="row mt-5 text-muted small">
                <div class="col-6 text-center">
                    <p>Signature de l'Employeur</p>
                    <div style="height: 60px;"></div>
                </div>
                <div class="col-6 text-center">
                    <p>Signature de l'Employé (Mention Lu et Approuvé)</p>
                    <div style="height: 60px;"></div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if (file_exists('footer.php')) include 'footer.php'; ?>

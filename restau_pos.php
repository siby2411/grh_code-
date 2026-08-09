<?php
// restau_pos.php - Module POS & Restauration Interne
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

$message = '';
$type_msg = '';

if ($db && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valider_commande'])) {
    $employe_id = !empty($_POST['employe_id']) ? intval($_POST['employe_id']) : null;
    $table_num = trim($_POST['table_num']);
    $mode_paiement = trim($_POST['mode_paiement']);
    $produits_commandes = $_POST['quantite'] ?? [];

    try {
        $db->beginTransaction();
        $total_panier = 0;
        $items_to_insert = [];

        foreach ($produits_commandes as $prod_id => $qte) {
            $qte = intval($qte);
            if ($qte > 0) {
                $stmt = $db->prepare("SELECT * FROM restau_produits WHERE id = ?");
                $stmt->execute([$prod_id]);
                $prod = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($prod && $prod['stock'] >= $qte) {
                    $total_panier += ($prod['prix'] * $qte);
                    $items_to_insert[] = ['id' => $prod['id'], 'qte' => $qte, 'prix' => $prod['prix']];
                } else {
                    throw new Exception("Produit indisponible ou stock insuffisant : " . ($prod['nom'] ?? 'Inconnu'));
                }
            }
        }

        if ($total_panier <= 0) throw new Exception("Veuillez sélectionner au moins un produit.");

        // Enregistrement de la commande avec l'état EN_ATTENTE
        $stmt = $db->prepare("INSERT INTO restau_commandes (employe_id, table_num, total, mode_paiement, date_commande, etat) VALUES (?, ?, ?, ?, NOW(), 'EN_ATTENTE')");
        $stmt->execute([$employe_id, $table_num, $total_panier, $mode_paiement]);
        $commande_id = $db->lastInsertId();

        foreach ($items_to_insert as $item) {
            $db->prepare("INSERT INTO restau_commande_items (commande_id, produit_id, quantite, prix_unitaire) VALUES (?, ?, ?, ?)")
               ->execute([$commande_id, $item['id'], $item['qte'], $item['prix']]);
            
            $db->prepare("UPDATE restau_produits SET stock = stock - ? WHERE id = ?")
               ->execute([$item['qte'], $item['id']]);
        }

        $db->commit();
        $message = "Commande enregistrée avec succès (État: EN_ATTENTE). Total : " . number_format($total_panier, 0, ',', ' ') . " F CFA.";
        $type_msg = "success";
    } catch (Exception $e) {
        if ($db->inTransaction()) $db->rollBack();
        $message = "Erreur : " . $e->getMessage();
        $type_msg = "danger";
    }
}

$produits = [];
$employes = [];
$ventes_jour = 0;

if ($db) {
    try {
        $produits = $db->query("SELECT * FROM restau_produits WHERE statut = 'Disponible' ORDER BY categorie, nom")->fetchAll(PDO::FETCH_ASSOC);
        $employes = $db->query("SELECT * FROM employes ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {}
}
?>

<div class="container my-4 text-white">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-utensils text-danger"></i> OMEGA RESTAU — POS & Restauration Interne</h2>
        <div>
            <a href="index.php" class="btn btn-outline-light btn-sm me-2"><i class="fas fa-home me-1"></i> Dashboard</a>
            <a href="restau_etats.php" class="btn btn-success btn-sm"><i class="fas fa-chart-line me-1"></i> États Financiers</a>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $type_msg ?> fw-bold shadow"><i class="fas fa-info-circle me-2"></i> <?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card bg-dark border-secondary shadow-lg mb-4">
                <div class="card-header bg-danger text-white fw-bold">
                    <i class="fas fa-cash-register me-1"></i> Point de Vente (POS) — Commande par QR Code / Employé
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Employé (Mode Crédit Salaire)</label>
                                <select name="employe_id" class="form-control bg-dark text-white border-secondary">
                                    <option value="">-- Client Externe / Comptant --</option>
                                    <?php foreach ($employes as $emp): ?>
                                        <option value="<?= $emp['id'] ?>" <?= (isset($_GET['employe_id']) && $_GET['employe_id'] == $emp['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($emp['prenom'] . ' ' . $emp['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Table / Poste</label>
                                <input type="text" name="table_num" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($_GET['table'] ?? 'Table 1') ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Paiement</label>
                                <select name="mode_paiement" class="form-control bg-dark text-white border-secondary">
                                    <option value="Crédit Salaire">Crédit Salaire</option>
                                    <option value="Espèces">Espèces</option>
                                    <option value="Wave / Orange Money">Wave / OM</option>
                                </select>
                            </div>
                        </div>

                        <h5 class="text-warning mt-4 mb-3">Sélection des Plats & Boissons</h5>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-dark table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Catégorie</th>
                                        <th>Prix Unitaire</th>
                                        <th>Stock</th>
                                        <th style="width: 120px;">Quantité</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($produits)): ?>
                                        <tr><td colspan="5" class="text-center text-muted">Aucun produit disponible. Veuillez en ajouter via le CRUD.</td></tr>
                                    <?php else: foreach ($produits as $p): ?>
                                        <tr>
                                            <td class="fw-bold"><?= htmlspecialchars($p['nom']) ?></td>
                                            <td><span class="badge bg-secondary"><?= htmlspecialchars($p['categorie']) ?></span></td>
                                            <td class="text-success"><?= number_format($p['prix'], 0, ',', ' ') ?> F</td>
                                            <td><span class="badge bg-<?= $p['stock'] > 5 ? 'info' : 'danger' ?>"><?= $p['stock'] ?> en stock</span></td>
                                            <td>
                                                <input type="number" name="quantite[<?= $p['id'] ?>]" class="form-control form-control-sm bg-dark text-white border-secondary text-center" value="0" min="0" max="<?= $p['stock'] ?>">
                                            </td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" name="valider_commande" class="btn btn-danger fw-bold px-5 py-2">
                                <i class="fas fa-check-circle me-1"></i> Valider la Commande & Imputer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-dark border-secondary shadow-lg mb-4">
                <div class="card-header bg-secondary text-white fw-bold">
                    <i class="fas fa-qrcode me-1"></i> QR Codes Restau & Actions
                </div>
                <div class="card-body text-center">
                    <p class="text-muted small">Scannez ce QR Code pour commander directement depuis la table :</p>
                    <?php
                    $emp_qr = isset($_GET['employe_id']) ? intval($_GET['employe_id']) : 1;
                    $table_qr = isset($_GET['table']) ? htmlspecialchars($_GET['table']) : 'Table_VIP';
                    $url_table = urlencode("http://127.0.0.1:8000/restau_pos.php?employe_id=" . $emp_qr . "&table=" . $table_qr);
                    $qr_api = "https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=" . $url_table;
                    ?>
                    <img src="<?= $qr_api ?>" alt="QR Table Restau" class="bg-white p-2 rounded mb-3">
                    <div>
                        <a href="restau_crud.php" class="btn btn-sm btn-info w-100 mb-2">
                            <i class="fas fa-boxes me-1"></i> Gestion Stock & Produits (CRUD)
                        </a>
                        <a href="restau_etats.php" class="btn btn-sm btn-success w-100">
                            <i class="fas fa-chart-line me-1"></i> États Financiers & Rapports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (file_exists('footer.php')) include 'footer.php'; ?>

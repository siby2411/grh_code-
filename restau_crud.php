<?php
// restau_crud.php - Gestion des Stocks & Produits Restaurant (CRUD)
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

if ($db && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_produit'])) {
    $id = intval($_POST['id'] ?? 0);
    $nom = trim($_POST['nom']);
    $categorie = trim($_POST['categorie']);
    $prix = floatval($_POST['prix']);
    $stock = intval($_POST['stock']);
    $statut = trim($_POST['statut']);

    if (!empty($nom) && $prix > 0) {
        if ($id > 0) {
            $stmt = $db->prepare("UPDATE restau_produits SET nom = ?, categorie = ?, prix = ?, stock = ?, statut = ? WHERE id = ?");
            $stmt->execute([$nom, $categorie, $prix, $stock, $statut, $id]);
            $message = "Produit mis à jour avec succès !";
        } else {
            $stmt = $db->prepare("INSERT INTO restau_produits (nom, categorie, prix, stock, statut) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $categorie, $prix, $stock, $statut]);
            $message = "Produit ajouté avec succès au catalogue Restau !";
        }
        $type_msg = "success";
    } else {
        $message = "Veuillez renseigner un nom valide et un prix supérieur à 0.";
        $type_msg = "warning";
    }
}

if ($db && isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $db->prepare("DELETE FROM restau_produits WHERE id = ?")->execute([$del_id]);
    header("Location: restau_crud.php");
    exit();
}

$edit_prod = null;
if ($db && isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $db->prepare("SELECT * FROM restau_produits WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_prod = $stmt->fetch(PDO::FETCH_ASSOC);
}

$produits = [];
if ($db) {
    try {
        $produits = $db->query("SELECT * FROM restau_produits ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {}
}
?>

<div class="container my-4 text-white">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-boxes text-danger"></i> OMEGA RESTAU — Gestion des Stocks & Produits (CRUD)</h2>
        <div>
            <a href="index.php" class="btn btn-outline-light btn-sm me-2"><i class="fas fa-home me-1"></i> Dashboard</a>
            <a href="restau_pos.php" class="btn btn-danger btn-sm"><i class="fas fa-cash-register me-1"></i> Aller au POS</a>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $type_msg ?> fw-bold shadow"><i class="fas fa-info-circle me-2"></i> <?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-4">
            <div class="card bg-dark border-secondary shadow-lg mb-4">
                <div class="card-header bg-danger fw-bold text-white">
                    <?= $edit_prod ? 'Modifier le Produit' : 'Ajouter un Plat / Boisson' ?>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $edit_prod['id'] ?? 0 ?>">
                        <div class="mb-3">
                            <label class="form-label">Nom du Produit</label>
                            <input type="text" name="nom" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($edit_prod['nom'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catégorie</label>
                            <select name="categorie" class="form-control bg-dark text-white border-secondary">
                                <option value="Plat Chaud" <?= ($edit_prod['categorie']??'')=='Plat Chaud'?'selected':'' ?>>Plat Chaud</option>
                                <option value="Boisson" <?= ($edit_prod['categorie']??'')=='Boisson'?'selected':'' ?>>Boisson</option>
                                <option value="Dessert" <?= ($edit_prod['categorie']??'')=='Dessert'?'selected':'' ?>>Dessert</option>
                                <option value="Snack" <?= ($edit_prod['categorie']??'')=='Snack'?'selected':'' ?>>Snack</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prix (F CFA)</label>
                            <input type="number" step="any" name="prix" class="form-control bg-dark text-white border-secondary" value="<?= $edit_prod['prix'] ?? 0 ?>" required min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stock Initial / Actuel</label>
                            <input type="number" name="stock" class="form-control bg-dark text-white border-secondary" value="<?= $edit_prod['stock'] ?? 10 ?>" required min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Statut</label>
                            <select name="statut" class="form-control bg-dark text-white border-secondary">
                                <option value="Disponible" <?= ($edit_prod['statut']??'')=='Disponible'?'selected':'' ?>>Disponible</option>
                                <option value="Rupture" <?= ($edit_prod['statut']??'')=='Rupture'?'selected':'' ?>>Rupture</option>
                            </select>
                        </div>
                        <button type="submit" name="save_produit" class="btn btn-danger w-100 fw-bold">
                            <i class="fas fa-save me-1"></i> Enregistrer
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card bg-dark border-secondary shadow-lg">
                <div class="card-header bg-secondary fw-bold text-white">Catalogue Actuel</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-striped align-middle mb-0">
                            <thead>
                                <tr class="text-secondary">
                                    <th>Nom</th>
                                    <th>Catégorie</th>
                                    <th>Prix</th>
                                    <th>Stock</th>
                                    <th>Statut</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($produits)): ?>
                                    <tr><td colspan="6" class="text-center text-muted">Aucun produit enregistré. Utilisez le formulaire pour en ajouter.</td></tr>
                                <?php else: foreach ($produits as $p): ?>
                                    <tr>
                                        <td class="fw-bold"><?= htmlspecialchars($p['nom']) ?></td>
                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($p['categorie']) ?></span></td>
                                        <td class="text-success"><?= number_format($p['prix'], 0, ',', ' ') ?> F</td>
                                        <td><span class="badge bg-<?= $p['stock'] > 5 ? 'info' : 'danger' ?>"><?= $p['stock'] ?></span></td>
                                        <td><span class="badge bg-<?= $p['statut']=='Disponible'?'success':'warning' ?>"><?= $p['statut'] ?></span></td>
                                        <td class="text-center">
                                            <a href="restau_crud.php?edit=<?= $p['id'] ?>" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                                            <a href="restau_crud.php?delete=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce produit ?');"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (file_exists('footer.php')) include 'footer.php'; ?>

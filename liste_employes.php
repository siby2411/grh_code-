<?php 
// liste_employes.php - Gestion du Personnel & Annuaire GRH 
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

$employes = [];
if ($db) {
    try {
        $employes = $db->query("SELECT * FROM employes ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {}
}
?>

<div class="container my-4 text-white">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-users-cog text-danger"></i> OMEGA GRH — Annuaire & Badges QR</h2>
        <div>
            <a href="index.php" class="btn btn-outline-light btn-sm me-2"><i class="fas fa-home me-1"></i> Dashboard</a>
            <a href="ajouter_employe.php" class="btn btn-danger btn-sm"><i class="fas fa-user-plus me-1"></i> Nouvel Employé</a>
        </div>
    </div>

    <div class="card bg-dark border-secondary shadow-lg">
        <div class="card-header bg-secondary fw-bold text-white">Liste des Salariés & Accès Restau / Pointage / vCard</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-striped align-middle mb-0">
                    <thead>
                        <tr class="text-secondary">
                            <th>Code</th>
                            <th>Nom & Prénom</th>
                            <th>Poste</th>
                            <th>Téléphone</th>
                            <th class="text-end">Salaire</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($employes)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">Aucun employé enregistré.</td></tr>
                        <?php else: foreach ($employes as $emp): 
                            $prenom = $emp['prenom'] ?? '';
                            $nom = $emp['nom'] ?? '';
                            $poste = $emp['poste'] ?? 'Employé';
                            $telephone = $emp['telephone'] ?? '-';
                        ?>
                            <tr>
                                <td class="text-warning fw-bold"><?= htmlspecialchars($emp['code_employe'] ?? 'EMP-'.$emp['id']) ?></td>
                                <td><?= htmlspecialchars($prenom . ' ' . $nom) ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($poste) ?></span></td>
                                <td><?= htmlspecialchars($telephone) ?></td>
                                <td class="text-end text-success fw-bold"><?= number_format($emp['salaire_base'] ?? 0, 0, ',', ' ') ?> F</td>
                                <td class="text-center">
                                    <!-- Bouton POS Restau -->
                                    <a href="restau_pos.php?employe_id=<?= $emp['id'] ?>" class="btn btn-sm btn-danger mb-1" title="POS Restau"><i class="fas fa-utensils"></i></a>
                              
                                    <!-- Bouton Badge QR -->
                                    <a href="carte_employe.php?id=<?= $emp['id'] ?>" class="btn btn-sm btn-info mb-1" title="Badge Pro QR"><i class="fas fa-id-card"></i></a>
                              
                                    <!-- Bouton Paie -->
                                    <a href="bulletin_paie.php?id=<?= $emp['id'] ?>" class="btn btn-sm btn-success mb-1" title="Bulletin Paie"><i class="fas fa-file-invoice-dollar"></i></a>

                                    <?php 
                                        // Configuration de l'URL vCard pour l'employé courant
                                        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                                        $host = $_SERVER['HTTP_HOST'];
                                        $vcard_url = $protocol . $host . dirname($_SERVER['PHP_SELF']) . '/vcard.php?id=' . $emp['id'];
                                        $qr_api = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($vcard_url);
                                    ?>
                                    
                                    <!-- Bouton vCard Numérique -->
                                    <button type="button" class="btn btn-sm btn-outline-info mb-1" data-bs-toggle="modal" data-bs-target="#vcardModal<?= $emp['id'] ?>" title="Carte de visite numérique vCard">
                                        <i class="fas fa-address-book"></i>
                                    </button>

                                    <!-- Modal d'affichage du QR Code vCard -->
                                    <div class="modal fade text-start" id="vcardModal<?= $emp['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content bg-dark text-white border-secondary">
                                                <div class="modal-header border-secondary">
                                                    <h5 class="modal-title"><i class="fas fa-address-card me-2"></i> Carte de visite : <?= htmlspecialchars($prenom . ' ' . $nom) ?></h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center p-4">
                                                    <p class="text-muted">Scannez ce QR code avec un smartphone pour enregistrer le contact :</p>
                                                    <div class="bg-white p-3 d-inline-block rounded shadow">
                                                        <img src="<?= $qr_api ?>" alt="QR Code vCard" class="img-fluid">
                                                    </div>
                                                    <div class="mt-3">
                                                        <span class="badge bg-secondary"><?= htmlspecialchars($poste) ?></span>
                                                        <p class="mt-2 mb-0 small text-info"><?= htmlspecialchars($telephone) ?></p>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-secondary">
                                                    <a href="<?= $vcard_url ?>" class="btn btn-success btn-sm" target="_blank">
                                                        <i class="fas fa-download me-1"></i> Télécharger le fichier .vcf
                                                    </a>
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fermer</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if (file_exists('footer.php')) include 'footer.php'; ?>

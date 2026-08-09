<?php
require_once 'config/database.php';
include 'header.php';

$db = (new Database())->getConnection();
$stmt = $db->query("SELECT e.*, d.nom as nom_departement FROM employes e LEFT JOIN departements d ON e.departement_id = d.id ORDER BY e.id DESC");
$employes = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-users text-danger"></i> Annuaire & Gestion des Employés</h2>
        <a href="ajouter_employe.php" class="btn btn-omega"><i class="fas fa-user-plus me-1"></i> Nouvel Employé</a>
    </div>

    <div class="card card-omega text-white shadow-lg">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-striped table-bordered align-middle">
                    <thead class="bg-danger text-white">
                        <tr>
                            <th>Code Employé</th>
                            <th>Employé</th>
                            <th>Poste / Département</th>
                            <th>Contact</th>
                            <th class="text-center">QR Code Pointage</th>
                            <th class="text-center">Carte Pro</th>
                            <th class="text-center">WhatsApp & Téléchargement</th>
                            <th class="text-center">Actions (CRUD)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($employes)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">Aucun employé enregistré dans la base GRH.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($employes as $emp): ?>
                                <?php 
                                    $code = htmlspecialchars($emp['code_employe']);
                                    $prenom_nom = htmlspecialchars($emp['prenom'] . ' ' . $emp['nom']);
                                    $telephone = preg_replace('/[^0-9+]/', '', $emp['telephone']);
                                    
                                    $url_pointage = "http://127.0.0.1:8000/scanner.php?code=" . urlencode($code);
                                    $qr_api = "https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=" . urlencode($url_pointage);
                                    $lien_telechargement = "telecharger_qrcode.php?code=" . urlencode($code);
                                    
                                    $msg_whatsapp = urlencode("Bonjour *{$prenom_nom}*,\n\nVoici votre lien personnel et QR Code de pointage / avis pour *Omega Informatique Consulting* :\n{$url_pointage}\n\nCordialement,\nMr Mohamed Siby.");
                                    $lien_whatsapp = "https://wa.me/{$telephone}?text={$msg_whatsapp}";
                                ?>
                                <tr>
                                    <td class="fw-bold text-warning"><?php echo $code; ?></td>
                                    <td><?php echo $prenom_nom; ?></td>
                                    <td>
                                        <div class="fw-bold text-light"><?php echo htmlspecialchars($emp['poste']); ?></div>
                                        <div class="small text-muted"><?php echo htmlspecialchars($emp['nom_departement'] ?? 'Sans département'); ?></div>
                                    </td>
                                    <td>
                                        <div><i class="fas fa-envelope text-secondary"></i> <?php echo htmlspecialchars($emp['email']); ?></div>
                                        <div><i class="fas fa-phone text-secondary"></i> <?php echo htmlspecialchars($emp['telephone']); ?></div>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo $url_pointage; ?>" target="_blank" title="Tester le lien de pointage">
                                            <img src="<?php echo $qr_api; ?>" alt="QR Code" class="img-thumbnail bg-white p-1" style="width: 55px; height: 55px;">
                                        </a>
                                        <div class="mt-1">
                                            <a href="<?php echo $lien_telechargement; ?>" class="text-info small text-decoration-none fw-bold" title="Télécharger l'image PNG du QR Code">
                                                <i class="fas fa-download"></i> Télécharger
                                            </a>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="carte_membre.php?id=<?php echo $emp['id']; ?>" target="_blank" class="btn btn-sm btn-outline-danger fw-bold">
                                            <i class="fas fa-id-card"></i> Carte Pro
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo $lien_whatsapp; ?>" target="_blank" class="btn btn-sm btn-success fw-bold w-100 mb-1" title="Envoyer sur WhatsApp">
                                            <i class="fab fa-whatsapp"></i> WhatsApp
                                        </a>
                                        <a href="<?php echo $url_pointage; ?>" target="_blank" class="btn btn-sm btn-outline-light w-100" title="Ouvrir la page du QR code">
                                            <i class="fas fa-external-link-alt"></i> Ouvrir lien
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="editer_employe.php?id=<?php echo $emp['id']; ?>" class="btn btn-sm btn-warning text-dark px-2" title="Modifier l'employé"><i class="fas fa-edit"></i></a>
                                            <a href="supprimer_employe.php?id=<?php echo $emp['id']; ?>" class="btn btn-sm btn-danger px-2" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet employé ?');" title="Supprimer l'employé"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

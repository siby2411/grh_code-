<?php
require_once 'config/database.php';
include 'header.php';

$db = (new Database())->getConnection();
$avis = $db->query("SELECT a.*, e.nom, e.prenom, e.poste FROM avis_employes a LEFT JOIN employes e ON a.code_employe = e.code_employe ORDER BY a.id DESC")->fetchAll(PDO::FETCH_ASSOC);
$moyenne = $db->query("SELECT AVG(note) FROM avis_employes")->fetchColumn() ?: 0;
?>

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-comments text-danger"></i> Suivi du Climat Social & Avis</h2>
        <span class="badge bg-dark border border-warning fs-6 p-2">Moyenne Globale : <?= number_format($moyenne, 1) ?> / 5</span>
    </div>

    <div class="card card-omega text-white shadow-lg">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-striped table-bordered align-middle">
                    <thead>
                        <tr class="text-danger">
                            <th>Date</th>
                            <th>Auteur / Code</th>
                            <th>Catégorie</th>
                            <th>Note</th>
                            <th>Commentaire</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($avis)): ?>
                            <tr><td colspan="5" class="text-center text-muted">Aucun retour enregistré pour le moment.</td></tr>
                        <?php else: ?>
                            <?php foreach($avis as $av): ?>
                                <tr>
                                    <td><?= $av['created_at'] ?></td>
                                    <td>
                                        <?php if($av['code_employe']): ?>
                                            <span class="fw-bold text-warning"><?= htmlspecialchars($av['prenom'] . ' ' . $av['nom']) ?></span><br>
                                            <small class="text-muted"><?= $av['code_employe'] ?></small>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Anonyme</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-dark border border-secondary"><?= htmlspecialchars($av['categorie']) ?></span></td>
                                    <td><strong class="text-warning"><?= $av['note'] ?> / 5</strong></td>
                                    <td><?= nl2br(htmlspecialchars($av['commentaire'])) ?></td>
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

<?php
require_once 'config/database.php';
include 'header.php';

$db = (new Database())->getConnection();
$message = '';
$type_msg = '';

// Traitement de l'émission d'un bulletin de paie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['emettre_paie'])) {
    $employe_id = intval($_POST['employe_id']);
    $periode = trim($_POST['periode']);
    $primes = floatval($_POST['primes']);
    $retenues = floatval($_POST['retenues']);

    if ($employe_id > 0 && !empty($periode)) {
        $stmt_emp = $db->prepare("SELECT salaire_base FROM employes WHERE id = ?");
        $stmt_emp->execute([$employe_id]);
        $emp = $stmt_emp->fetch(PDO::FETCH_ASSOC);

        if ($emp) {
            $salaire_base = floatval($emp['salaire_base']);
            $montant_net = ($salaire_base + $primes) - $retenues;

            try {
                $stmt_ins = $db->prepare("INSERT INTO paie (employe_id, periode, salaire_base, primes, retenues, montant_net, date_paiement, statut) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'Payé')");
                $stmt_ins->execute([$employe_id, $periode, $salaire_base, $primes, $retenues, $montant_net]);
                $message = "Bulletin émis avec succès ! Net à payer : " . number_format($montant_net, 0, ',', ' ') . " F CFA";
                $type_msg = "success";
            } catch (Exception $e) {
                $message = "Erreur : " . $e->getMessage();
                $type_msg = "danger";
            }
        } else {
            $message = "Employé introuvable.";
            $type_msg = "danger";
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
        $type_msg = "warning";
    }
}

$employes = $db->query("SELECT * FROM employes ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
$historique = $db->query("SELECT p.*, e.code_employe, e.nom, e.prenom, e.poste FROM paie p JOIN employes e ON p.employe_id = e.id ORDER BY p.id DESC")->fetchAll(PDO::FETCH_ASSOC);
$total_verse = $db->query("SELECT SUM(montant_net) as total FROM paie")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
?>

<div class="container my-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-file-invoice-dollar text-danger"></i> Gestion de la Paie & Rémunérations</h2>
        <div class="card bg-dark border-secondary px-3 py-2 text-end">
            <span class="text-muted small">Total Versé :</span>
            <h4 class="text-success mb-0 fw-bold"><?= number_format($total_verse, 0, ',', ' ') ?> F CFA</h4>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $type_msg ?> fw-bold shadow"><i class="fas fa-info-circle me-2"></i> <?= $message ?></div>
    <?php endif; ?>

    <!-- Formulaire -->
    <div class="card card-omega text-white shadow-lg mb-5">
        <div class="card-header bg-danger text-white fw-bold"><i class="fas fa-calculator me-1"></i> Émettre un Bulletin de Paie</div>
        <div class="card-body">
            <form method="POST">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Employé</label>
                        <select name="employe_id" id="employe_id" class="form-control bg-dark text-white border-secondary" required>
                            <option value="">Sélectionner un employé</option>
                            <?php foreach ($employes as $emp): ?>
                                <option value="<?= $emp['id'] ?>" data-salaire="<?= $emp['salaire_base'] ?>">
                                    <?= htmlspecialchars($emp['prenom'] . ' ' . $emp['nom']) ?> (<?= number_format($emp['salaire_base'], 0, ',', ' ') ?> F)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mois / Période</label>
                        <input type="text" name="periode" class="form-control bg-dark text-white border-secondary" value="<?= date('F Y') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Salaire de Base (F CFA)</label>
                        <input type="text" id="salaire_base_aff" class="form-control bg-dark text-white border-secondary" readonly value="0">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Primes & Avantages (F CFA)</label>
                        <input type="number" step="any" name="primes" id="primes" class="form-control bg-dark text-white border-secondary" value="0" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Retenues & Absences (F CFA)</label>
                        <input type="number" step="any" name="retenues" id="retenues" class="form-control bg-dark text-white border-secondary" value="0" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-warning fw-bold">Montant Net à Payer (F CFA)</label>
                        <input type="text" id="montant_net_aff" class="form-control bg-dark text-warning border-warning fw-bold fs-5" readonly value="0">
                    </div>
                </div>
                <button type="submit" name="emettre_paie" class="btn btn-omega fw-bold w-100"><i class="fas fa-paper-plane me-1"></i> Valider et Enregistrer le Paiement</button>
            </form>
        </div>
    </div>

    <!-- Historique des virements (La liste en bas) -->
    <div class="card card-omega text-white shadow-lg">
        <div class="card-header bg-secondary text-white fw-bold"><i class="fas fa-history me-1"></i> Historique des Virements de Paie</div>
        <div class="card-body p-0">
            <table class="table table-dark table-striped table-hover mb-0">
                <thead>
                    <tr class="text-secondary">
                        <th>Date</th>
                        <th>Code</th>
                        <th>Employé</th>
                        <th>Période</th>
                        <th class="text-end">Montant Net</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($historique)): ?>
                        <tr><td colspan="6" class="text-center text-muted">Aucun historique enregistré.</td></tr>
                    <?php else: foreach ($historique as $h): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($h['date_paiement'])) ?></td>
                            <td class="text-warning fw-bold"><?= htmlspecialchars($h['code_employe']) ?></td>
                            <td><?= htmlspecialchars($h['prenom'] . ' ' . $h['nom']) ?></td>
                            <td><?= htmlspecialchars($h['periode']) ?></td>
                            <td class="text-end text-success fw-bold"><?= number_format($h['montant_net'], 0, ',', ' ') ?> F</td>
                            <td><span class="badge bg-success"><?= htmlspecialchars($h['statut']) ?></span></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sel = document.getElementById('employe_id'), p = document.getElementById('primes'), r = document.getElementById('retenues'), net = document.getElementById('montant_net_aff'), base = document.getElementById('salaire_base_aff');
    function calc() {
        const opt = sel.options[sel.selectedIndex], s = parseFloat(opt.getAttribute('data-salaire')) || 0;
        base.value = s.toLocaleString('fr-FR');
        net.value = ((s + (parseFloat(p.value)||0)) - (parseFloat(r.value)||0)).toLocaleString('fr-FR') + ' F CFA';
    }
    sel.onchange = p.oninput = r.oninput = calc;
});
</script>

<?php include 'footer.php'; ?>

<?php
require_once 'config/database.php';
include 'header.php';

$db = (new Database())->getConnection();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code_employe = trim($_POST['code_employe']);
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $poste = trim($_POST['poste']);
    $departement_id = $_POST['departement_id'];
    $salaire_base = $_POST['salaire_base'];
    $date_embauche = $_POST['date_embauche'];

    try {
        $stmt = $db->prepare("UPDATE employes SET code_employe = ?, nom = ?, prenom = ?, email = ?, telephone = ?, poste = ?, departement_id = ?, salaire_base = ?, date_embauche = ? WHERE id = ?");
        $stmt->execute([$code_employe, $nom, prenom, $email, $telephone, $poste, $departement_id, $salaire_base, $date_embauche, $id]);
        $message = "Informations de l'employé mises à jour avec succès !";
    } catch (Exception $e) {
        $message = "Erreur lors de la mise à jour : " . $e->getMessage();
    }
}

$stmt = $db->prepare("SELECT * FROM employes WHERE id = ?");
$stmt->execute([$id]);
$emp = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$emp) {
    echo '<div class="container my-5"><div class="alert alert-danger text-center">Employé introuvable. <a href="employes.php" class="alert-link">Retour à la liste</a></div></div>';
    include 'footer.php';
    exit;
}

$departements = $db->query("SELECT * FROM departements")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-omega text-white shadow-lg p-4">
                <h3 class="text-danger fw-bold mb-4"><i class="fas fa-user-edit"></i> Modifier l'Employé</h3>
                
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success text-center"><?= $message ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Code Employé Unique</label>
                            <input type="text" name="code_employe" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($emp['code_employe']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Département</label>
                            <select name="departement_id" class="form-control bg-dark text-white border-secondary" required>
                                <option value="">Sélectionner un département</option>
                                <?php foreach($departements as $dep): ?>
                                    <option value="<?= $dep['id'] ?>" <?= ($dep['id'] == $emp['departement_id']) ? 'selected' : '' ?>><?= htmlspecialchars($dep['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($emp['nom']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Prénom</label>
                            <input type="text" name="prenom" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($emp['prenom']) ?>" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email professionnel</label>
                            <input type="email" name="email" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($emp['email']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Téléphone Mobile</label>
                            <input type="text" name="telephone" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($emp['telephone']) ?>" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Poste Occupé</label>
                            <input type="text" name="poste" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($emp['poste']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Salaire de Base (F CFA)</label>
                            <input type="number" step="1000" name="salaire_base" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($emp['salaire_base']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date d'embauche</label>
                            <input type="date" name="date_embauche" class="form-control bg-dark text-white border-secondary" value="<?= htmlspecialchars($emp['date_embauche']) ?>" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <a href="employes.php" class="btn btn-secondary">Retour à la liste</a>
                        <button type="submit" class="btn btn-omega fw-bold px-4">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<?php
require_once 'config/database.php';
include 'header.php';

$db = (new Database())->getConnection();
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
        $stmt = $db->prepare("INSERT INTO employes (code_employe, nom, prenom, email, telephone, poste, departement_id, salaire_base, date_embauche) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$code_employe, $nom, prenom, $email, $telephone, $poste, $departement_id, $salaire_base, $date_embauche]);
        echo '<div class="alert alert-success m-4 text-center">Employé enregistré avec succès ! <a href="employes.php" class="alert-link">Voir la liste</a></div>';
    } catch (Exception $e) {
        echo '<div class="alert alert-danger m-4 text-center">Erreur : ' . $e->getMessage() . '</div>';
    }
}

$departements = $db->query("SELECT * FROM departements")->fetchAll(PDO::FETCH_ASSOC);
$generated_code = 'OMEGA-2026-' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-omega text-white shadow-lg p-4">
                <h3 class="text-danger fw-bold mb-4"><i class="fas fa-user-plus"></i> Enregistrement d'un Nouvel Employé</h3>
                <form method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Code Employé Unique</label>
                            <input type="text" name="code_employe" class="form-control bg-dark text-white border-secondary" value="<?= $generated_code ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Département</label>
                            <select name="departement_id" class="form-control bg-dark text-white border-secondary" required>
                                <option value="">Sélectionner un département</option>
                                <?php foreach($departements as $dep): ?>
                                    <option value="<?= $dep['id'] ?>"><?= htmlspecialchars($dep['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" class="form-control bg-dark text-white border-secondary" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Prénom</label>
                            <input type="text" name="prenom" class="form-control bg-dark text-white border-secondary" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email professionnel</label>
                            <input type="email" name="email" class="form-control bg-dark text-white border-secondary" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Téléphone Mobile</label>
                            <input type="text" name="telephone" class="form-control bg-dark text-white border-secondary" value="+221" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Poste Occupé</label>
                            <input type="text" name="poste" class="form-control bg-dark text-white border-secondary" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Salaire de Base (F CFA)</label>
                            <input type="number" step="1000" name="salaire_base" class="form-control bg-dark text-white border-secondary" value="350000" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date d'embauche</label>
                            <input type="date" name="date_embauche" class="form-control bg-dark text-white border-secondary" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <a href="employes.php" class="btn btn-secondary">Retour</a>
                        <button type="submit" class="btn btn-omega fw-bold px-4">Enregistrer l'Employé</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

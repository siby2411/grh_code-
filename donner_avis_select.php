<?php
require_once 'config/database.php';
include 'header.php';

$db = (new Database())->getConnection();
$employes = $db->query("SELECT * FROM employes ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code_employe = trim($_POST['code_employe']);
    $note = intval($_POST['note']);
    $commentaire = trim($_POST['commentaire']);
    $categorie = trim($_POST['categorie']);

    $stmt = $db->prepare("INSERT INTO avis_employes (code_employe, note, commentaire, categorie) VALUES (?, ?, ?, ?)");
    $stmt->execute([$code_employe, $note, $commentaire, $categorie]);
    $message = "Votre évaluation a été enregistrée avec succès. Merci !";
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-omega text-white shadow-lg p-4">
                <h3 class="text-danger fw-bold mb-3 text-center"><i class="fas fa-comments"></i> Évaluation avec Sélection Employé</h3>
                <p class="text-light small text-center mb-4">Liez votre retour professionnel à votre code d'employé.</p>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-success text-center"><?= $message ?></div>
                <?php else: ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Sélectionnez votre identité (Employé)</label>
                            <select name="code_employe" class="form-control bg-dark text-white border-secondary" required>
                                <option value="">-- Choisir votre profil --</option>
                                <?php foreach($employes as $emp): ?>
                                    <option value="<?= htmlspecialchars($emp['code_employe']) ?>"><?= htmlspecialchars($emp['prenom'] . ' ' . $emp['nom'] . ' (' . $emp['code_employe'] . ')') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catégorie</label>
                            <select name="categorie" class="form-control bg-dark text-white border-secondary" required>
                                <option value="Climat Social">Climat Social & Ambiance</option>
                                <option value="Matériel & Outils">Matériel & Outils de Travail</option>
                                <option value="Organisation">Organisation & Management</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Note (sur 5)</label>
                            <select name="note" class="form-control bg-dark text-white border-secondary" required>
                                <option value="5">5/5 - Excellent</option>
                                <option value="4">4/5 - Très bien</option>
                                <option value="3">3/5 - Correct</option>
                                <option value="2">2/5 - À améliorer</option>
                                <option value="1">1/5 - Insuffisant</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Commentaire</label>
                            <textarea name="commentaire" rows="4" class="form-control bg-dark text-white border-secondary" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-omega w-100 fw-bold py-2">Envoyer l'évaluation</button>
                    </form>
                <?php endif; ?>
                <div class="text-center mt-3">
                    <a href="index.php" class="text-muted text-decoration-none small">&larr; Retour à l'accueil</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

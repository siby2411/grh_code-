<?php
session_start();
$licence = isset($_SESSION['licence_scan']) ? htmlspecialchars($_SESSION['licence_scan']) : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre avis - Dabakh Fitness</title>
    <link href="assets/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #121212; color: #fff; }
        .card { background-color: #1e1e1e; border: 1px solid #dc3545; }
        .btn-danger { background-color: #dc3545; border: none; }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100 py-4">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4 shadow-lg">
                <h2 class="text-center text-danger mb-3"><i class="fas fa-comments"></i> Votre Avis Compte</h2>
                <p class="text-center text-light mb-4">Aidez-nous à améliorer le <strong>Dabakh Fitness Wellness</strong> en répondant à nos questions.</p>
                
                <form action="traiter_avis.php" method="POST">
                    <!-- Champ Licence pré-rempli et verrouillé -->
                    <div class="mb-4">
                        <label class="form-label text-warning fw-bold">Numéro de Licence</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark text-light border-secondary"><i class="fas fa-id-card"></i></span>
                            <input type="text" name="numero_licence" value="<?php echo $licence; ?>" readonly class="form-control bg-dark text-light border-secondary">
                        </div>
                    </div>

                    <!-- Note globale -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Note globale (1 à 5)</label>
                        <select name="note" class="form-select bg-dark text-white border-secondary" required>
                            <option value="5">⭐⭐⭐⭐⭐ - Excellent</option>
                            <option value="4">⭐⭐⭐⭐ - Très bien</option>
                            <option value="3">⭐⭐⭐ - Moyen</option>
                            <option value="2">⭐⭐ - À améliorer</option>
                            <option value="1">⭐ - Médiocre</option>
                        </select>
                    </div>

                    <!-- Question 1 -->
                    <div class="mb-3">
                        <label class="form-label text-warning"><i class="fas fa-building"></i> Comment vous trouvez la salle ?</label>
                        <textarea name="avis_salle" class="form-control bg-dark text-white border-secondary" rows="2" placeholder="Votre avis sur les équipements, la propreté, l'ambiance..."></textarea>
                    </div>

                    <!-- Question 2 -->
                    <div class="mb-3">
                        <label class="form-label text-warning"><i class="fas fa-tools"></i> Quelles sont les choses à améliorer dans la salle ?</label>
                        <textarea name="avis_ameliorer" class="form-control bg-dark text-white border-secondary" rows="2" placeholder="Vos suggestions d'amélioration..."></textarea>
                    </div>

                    <!-- Question 3 -->
                    <div class="mb-3">
                        <label class="form-label text-warning"><i class="fas fa-handshake"></i> Que pensez-vous de l’accueil à la salle ?</label>
                        <textarea name="avis_accueil" class="form-control bg-dark text-white border-secondary" rows="2" placeholder="Votre avis sur l'accueil, l'équipe..."></textarea>
                    </div>

                    <!-- Question 4 -->
                    <div class="mb-3">
                        <label class="form-label text-warning"><i class="fas fa-dumbbell"></i> Par rapport au coaching, avez-vous des reproches ou des compliments à faire ?</label>
                        <textarea name="avis_coaching" class="form-control bg-dark text-white border-secondary" rows="3" placeholder="Vos retours concernant les coachs et les séances..."></textarea>
                    </div>

                    <!-- Champ commentaire général (compatibilité rétroactive) -->
                    <div class="mb-4">
                        <label class="form-label text-warning"><i class="fas fa-comment-dots"></i> Commentaires libres additionnels</label>
                        <textarea name="commentaire" class="form-control bg-dark text-white border-secondary" rows="2" placeholder="Autres remarques..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 fw-bold py-2">
                        <i class="fas fa-paper-plane"></i> Envoyer mon avis
                    </button>
                </form>
            </div>
            <p class="text-center mt-3 text-secondary small">Dabakh Fitness Wellness © 2026</p>
        </div>
    </div>
</div>

</body>
</html>

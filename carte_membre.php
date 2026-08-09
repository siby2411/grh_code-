<?php
require_once 'config/database.php';

$db = (new Database())->getConnection();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $db->prepare("SELECT e.*, d.nom as nom_departement FROM employes e LEFT JOIN departements d ON e.departement_id = d.id WHERE e.id = ?");
$stmt->execute([$id]);
$emp = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$emp) {
    die("Employé introuvable.");
}

$code = htmlspecialchars($emp['code_employe']);
$nom_complet = htmlspecialchars($emp['prenom'] . ' ' . $emp['nom']);
$poste = htmlspecialchars($emp['poste']);
$departement = htmlspecialchars($emp['nom_departement'] ?? 'Direction Générale');
$url_pointage = "http://127.0.0.1:8000/scanner.php?code=" . urlencode($code);
$qr_api = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($url_pointage);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Carte Professionnelle - <?= $nom_complet ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #0d1117; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .badge-pro { width: 420px; background: linear-gradient(135deg, #161b22 0%, #0d1117 100%); border: 3px solid #dc3545; border-radius: 15px; box-shadow: 0 10px 30px rgba(220, 53, 69, 0.3); color: #fff; overflow: hidden; }
        .badge-header { background: #dc3545; color: white; padding: 15px; text-align: center; }
        .print-btn { position: fixed; bottom: 20px; right: 20px; }
        @media print { .print-btn { display: none; } body { background: white; } }
    </style>
</head>
<body>

<div class="badge-pro">
    <div class="badge-header">
        <h5 class="mb-0 fw-bold text-uppercase" style="font-size: 0.9rem;">Omega Informatique Consulting</h5>
        <small class="text-light">Carte Professionnelle d'Accès & Pointage GRH</small>
    </div>
    <div class="p-4 text-center">
        <div class="mb-3">
            <span class="badge bg-dark border border-danger text-warning px-3 py-2 fs-6"><?= $code ?></span>
        </div>
        <h3 class="fw-bold text-white mb-1"><?= $nom_complet ?></h3>
        <p class="text-danger fw-semibold mb-1"><?= $poste ?></p>
        <p class="text-muted small mb-4"><?= $departement ?></p>

        <div class="bg-white p-2 d-inline-block rounded shadow mb-3">
            <img src="<?= $qr_api ?>" alt="QR Code Pointage" style="width: 130px; height: 130px;">
        </div>

        <div class="border-top border-secondary pt-3 mt-2 text-start small text-muted">
            <div><i class="fas fa-map-marker-alt text-danger me-1"></i> Sacré-Cœur 3 VDN, Dakar, Sénégal</div>
            <div><i class="fas fa-phone text-danger me-1"></i> +221 77 654 28 03 (Mr Mohamed Siby)</div>
        </div>
    </div>
</div>

<button onclick="window.print()" class="print-btn btn btn-danger btn-lg shadow fw-bold"><i class="fas fa-print me-2"></i> Imprimer la Carte</button>

</body>
</html>

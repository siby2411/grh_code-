<?php                         // equipement_etiquette.php - Générateur d'étiquettes QR Code prêtes à coller (OMEGA Suite)                             
ini_set('display_errors', 1); error_reporting(E_ALL);                                     
$db = null;                   
if (file_exists('config/database.php')) {                       
    require_once 'config/database.php';                         
    try { $db = (new Database())->getConnection(); } catch (Exception $e) {}
} elseif (file_exists('database.php')) {
    require_once 'database.php';
    try { $db = (new Database())->getConnection(); } catch (Exception $e) {}
}

$id = intval($_GET['id'] ?? 0);
$eq = null;

if ($db && $id > 0) {
    $stmt = $db->prepare("SELECT * FROM equipements WHERE id = ?");
    $stmt->execute([$id]);
    $eq = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$eq) {
    die("Équipement introuvable.");
}

$fiche_url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/equipement_fiche.php?id=' . $eq['id'];
// Optimisation : taille 300x300 et correction d'erreur élevée ECC = H pour impression photocopieuse
$qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&ecc=H&data=" . urlencode($fiche_url);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Étiquette - <?= htmlspecialchars($eq['nom_equipement']) ?></title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body { background: #fff; color: #000; font-family: Arial, sans-serif; }
        .etiquette-card {
            width: 320px;
            border: 2px dashed #333;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            margin: 30px auto;
            background: #fff;
        }
        @media print {
            .no-print { display: none; }
            .etiquette-card { border: 1px solid #000; margin: 0; box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="container text-center my-4">
        <div class="no-print mb-3">
            <button onclick="window.print();" class="btn btn-dark fw-bold"><i class="fas fa-print me-1"></i> Imprimer cette étiquette</button>
            <a href="equipements.php" class="btn btn-outline-secondary">Retour au Parc</a>
        </div>

        <div class="etiquette-card">
            <h5 class="fw-bold mb-1 text-uppercase text-dark"><?= htmlspecialchars($eq['nom_equipement']) ?></h5>
            <span class="badge bg-dark text-white px-2 py-1 mb-2"><?= htmlspecialchars($eq['code_equipement']) ?></span>

            <div class="my-2">
                <img src="<?= $qr_url ?>" alt="QR Code" class="img-fluid" style="width: 160px; height: 160px;">
            </div>

            <div class="small text-muted border-top pt-2 mt-2 text-start">
                <div><strong>Catégorie :</strong> <?= htmlspecialchars($eq['categorie']) ?></div>
                <div><strong>État :</strong> <?= htmlspecialchars($eq['etat_usure']) ?></div>
                <div><strong>Maintenance :</strong> <?= htmlspecialchars($eq['reparateur_assigne']) ?></div>
            </div>
        </div>
    </div>
</body>
</html>

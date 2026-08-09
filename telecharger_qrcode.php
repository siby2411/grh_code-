<?php
require_once 'config/database.php';

$db = (new Database())->getConnection();
$code = isset($_GET['code']) ? trim($_GET['code']) : '';

$stmt = $db->prepare("SELECT * FROM employes WHERE code_employe = ?");
$stmt->execute([$code]);
$emp = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$emp) {
    die("Employé introuvable.");
}

$url_pointage = "http://127.0.0.1:8000/scanner.php?code=" . urlencode($code);
$qr_api = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($url_pointage);

$image_data = file_get_contents($qr_api);
if ($image_data === FALSE) {
    die("Erreur lors de la génération du QR Code.");
}

$filename = "QRCode_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $code) . ".png";

header('Content-Type: image/png');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($image_data));
echo $image_data;
exit;

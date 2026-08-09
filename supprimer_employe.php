<?php
require_once 'config/database.php';

$db = (new Database())->getConnection();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    try {
        $stmt = $db->prepare("DELETE FROM employes WHERE id = ?");
        $stmt->execute([$id]);
    } catch (Exception $e) {
        // Erreur de contrainte de clé étrangère potentielle
    }
}

header("Location: employes.php");
exit;

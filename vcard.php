<?php
// vcard.php - Génération d'une fiche de contact vCard pour un employé
require_once 'config/database.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("Employé non spécifié.");
}

try {
    $db = (new Database())->getConnection();
    
    // Récupérer les informations de l'employé (ajustez le nom des colonnes selon votre table)
    $stmt = $db->prepare("SELECT * FROM employes WHERE id = ?");
    $stmt->execute([$id]);
    $employe = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$employe) {
        die("Employé introuvable.");
    }

    $nom = $employe['nom'] ?? 'Inconnu';
    $prenom = $employe['prenom'] ?? '';
    $poste = $employe['poste'] ?? 'Collaborateur';
    $telephone = $employe['telephone'] ?? '';
    $email = $employe['email'] ?? '';
    $entreprise = "OMEGA Suite / GRH";

    // Forcer le téléchargement ou l'affichage de la vCard
    header('Content-Type: text/vcard; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $prenom . '_' . $nom . '.vcf"');

    // Format vCard standard (v3.0)
    echo "BEGIN:VCARD\r\n";
    echo "VERSION:3.0\r\n";
    echo "N:$nom;$prenom;;;\r\n";
    echo "FN:$prenom $nom\r\n";
    echo "ORG:$entreprise\r\n";
    echo "TITLE:$poste\r\n";
    echo "TEL;TYPE=WORK,VOICE:$telephone\r\n";
    echo "EMAIL;TYPE=PREV,INTERNET:$email\r\n";
    echo "END:VCARD\r\n";

} catch (Exception $e) {
    die("Erreur technique : " . $e->getMessage());
}
?>

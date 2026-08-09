<?php
require_once 'config/database.php';
include 'header.php';
$db = (new Database())->getConnection();
$type = $_GET['type'] ?? '';

// Debug: Afficher si on entre bien dans la page
echo "<div class='container mt-4'>";

if ($type == 'paiements_adherent') {
    $query = "SELECT a.prenom, a.nom, p.date_paiement, p.montant FROM paiements p JOIN adherents a ON p.adherent_id = a.id";
} elseif ($type == 'paiements_discipline') {
    // Vérifiez que discipline_id existe dans votre table adherents
    $query = "SELECT d.nom, SUM(p.montant) as total FROM paiements p JOIN adherents a ON p.adherent_id = a.id JOIN disciplines d ON a.discipline_id = d.id GROUP BY d.nom";
} elseif ($type == 'rentabilite') {
    $query = "SELECT d.nom, SUM(p.montant) as profit FROM paiements p JOIN adherents a ON p.adherent_id = a.id JOIN disciplines d ON a.discipline_id = d.id GROUP BY d.nom ORDER BY profit DESC";
} else {
    die("Type non reconnu");
}

$stmt = $db->query($query);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$data) {
    echo "<div class='alert alert-info'>Aucune donnée trouvée. Vérifiez que vos adhérents sont liés à des disciplines et que des paiements existent.</div>";
} else {
    echo "<table class='table'><thead><tr>";
    foreach(array_keys($data[0]) as $th) echo "<th>".ucfirst($th)."</th>";
    echo "</tr></thead><tbody>";
    foreach($data as $row) {
        echo "<tr>";
        foreach($row as $cell) echo "<td>".htmlspecialchars($cell)."</td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
}
echo "</div>";
include 'footer.php';

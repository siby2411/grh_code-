<?php
// generer_vcf.php - Générateur de fichier de contact standard vCard (.vcf)
$nom = $_GET['nom'] ?? 'Mohamed Siby';
$poste = $_GET['poste'] ?? 'Software Developer';
$tel = $_GET['tel'] ?? '';
$email = $_GET['email'] ?? '';
$portfolio = $_GET['portfolio'] ?? '';

header('Content-Type: text/x-vcard; charset=utf-8');
header('Content-Disposition: attachment; filename="contact_omega.vcf"');

echo "BEGIN:VCARD\n";
echo "VERSION:3.0\n";
echo "FN:" . $nom . "\n";
echo "TITLE:" . $poste . "\n";
echo "TEL;TYPE=CELL:" . $tel . "\n";
echo "EMAIL:" . $email . "\n";
echo "URL:" . $portfolio . "\n";
echo "ORG:OMEGA SUITE Enterprise\n";
echo "END:VCARD\n";
exit;

#!/bin/bash
# ==============================================================================
# Script de nettoyage des anciens fichiers Dabakh Fitness & Lancement Serveur GRH
# OMEGA INFORMATIQUE CONSULTING - Sacré-Cœur 3 VDN, Dakar
# ==============================================================================

echo "[*] Suppression des fichiers obsolètes liés à l'ancienne application..."

# Liste des fichiers et dossiers de l'ancienne application Dabakh Fitness à supprimer
rm -f adherent_details.php adherents.php editer_adherent.php reset_adherents.php
rm -f factures.php liste_factures.php produits.php stats_boutique.php
rm -f formateurs.php disciplines.php tarifs.php challenges.php calendrier.php
rm -f fitness.jpeg tai.jpeg header_updated.php index_improved.php indexok.php indexok2.php
rm -f database.sql omega_marker_config.json envoyer_qrcode.php traiter_avis.php verifier_trigger.php
rm -rf database

echo "[*] Nettoyage terminé avec succès !"
echo "[*] Démarrage du serveur web PHP intégré pour l'application GRH..."

# Lancement du serveur PHP sur le port 8000
php -S 127.0.0.1:8000

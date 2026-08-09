#!/bin/bash
# /root/qrcode/backup_qrcode.sh

# Définition du chemin correct pour Android / Termux
DEST="/storage/emulated/0/qrcode"

# Création des dossiers nécessaires
mkdir -p "$DEST/files"

echo "Début de la sauvegarde..."

# 1. Sauvegarde des fichiers sources
# On utilise rsync pour synchroniser uniquement les modifications
rsync -av /root/qrcode/ "$DEST/files/"

# 2. Sauvegarde de la base de données grh_qrcode
# Note: assurez-vous que le mot de passe est bien configuré ou vide si non requis
mysqldump -u root grh_qrcode > "$DEST/backup_db.sql"

if [ $? -eq 0 ]; then
    echo "Sauvegarde effectuée avec succès dans $DEST"
else
    echo "Erreur lors de la sauvegarde de la base de données."
fi

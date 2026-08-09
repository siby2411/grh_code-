<?php
// donner_avis_anonyme.php
include 'header.php';
?>

<div class="container my-5">
    <!-- Section WhatsApp & Support GRH Direct -->
    <div class="row mb-4">
        <div class="col-md-12 text-center">
            <div class="card bg-dark border-secondary p-3 shadow-lg">
                <h5 class="text-white"><i class="fab fa-whatsapp text-success"></i> Support GRH en direct (OMEGA)</h5>
                <p class="text-muted small mb-2">Besoin d'assistance directe ou d'un échange confidentiel avec la direction ?</p>
                <div class="d-flex justify-content-center align-items-center gap-3">
                    <a href="https://wa.me/221776542803?text=Bonjour,%20je%20souhaite%20contacter%20le%20service%20GRH%20d'OMEGA%20Informatique%20Consulting." 
                       target="_blank" class="btn btn-success btn-sm">
                       <i class="fab fa-whatsapp"></i> Contacter le Directeur Technique (+221 77 654 28 03)
                    </a>
                </div>
                <!-- QR Code WhatsApp dynamique via l'API QRServer -->
                <div class="mt-3">
                    <?php 
                    $wa_url = urlencode("https://wa.me/221776542803?text=Bonjour,%20contact%20GRH%20OMEGA");
                    $qr_wa_img = "https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=" . $wa_url;
                    ?>
                    <img src="<?= $qr_wa_img ?>" alt="QR Code WhatsApp GRH" class="bg-white p-1 rounded" style="width:100px; height:100px;">
                    <p class="small text-secondary mt-1 mb-0">Scanner pour ouvrir WhatsApp directement</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire d'Avis Anonyme -->
    <div class="card card-omega text-white shadow-lg border-danger">
        <div class="card-header bg-danger text-white text-center fw-bold">
            <i class="fas fa-user-secret me-2"></i> OMEGA Informatique CONSULTING — Feedback Collaborateur (Mode Anonyme)
        </div>
        <div class="card-body">
            <h3 class="text-warning mb-3">Votre Avis, Votre Voix</h3>
            <p class="text-muted">Exprimez-vous librement en toute confidentialité. Vos retours nous permettent d'améliorer notre climat social et l'efficacité de notre gestion GRH.</p>
            
            <form action="traitement_avis.php" method="POST">
                <!-- Note Globale -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Note globale de l'environnement de travail (1 à 5)</label>
                    <div class="d-flex gap-3">
                        <?php for($i=5; $i>=1; $i--): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="note" value="<?= $i ?>" id="note<?= $i ?>" required>
                            <label class="form-check-label" for="note<?= $i ?>"><?= str_repeat('⭐', $i) ?></label>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Questions GRH -->
                <div class="mb-3">
                    <label class="form-label">Comment évaluez-vous l'organisation interne ?</label>
                    <textarea name="org_interne" class="form-control bg-dark text-white border-secondary" rows="2"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Quels aspects de la gestion du personnel sont à améliorer ?</label>
                    <textarea name="points_amelioration" class="form-control bg-dark text-white border-secondary" rows="2"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Que pensez-vous du climat social et de l'accueil de la direction ?</label>
                    <textarea name="climat_social" class="form-control bg-dark text-white border-secondary" rows="2"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Feedback sur votre encadrement / management :</label>
                    <textarea name="coaching" class="form-control bg-dark text-white border-secondary" rows="2"></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Commentaires libres additionnels :</label>
                    <textarea name="commentaires" class="form-control bg-dark text-white border-secondary" rows="3"></textarea>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-danger fw-bold">
                        <i class="fas fa-paper-plane me-1"></i> Envoyer mon avis anonyme
                    </button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center">
            <a href="index.php" class="text-white text-decoration-none small"><i class="fas fa-arrow-left me-1"></i> Retour à l'accueil</a>
        </div>
    </div>
</div>

<footer class="text-center text-muted small py-4">
    OMEGA Informatique CONSULTING — Sacré-Cœur 3 VDN, Dakar, Sénégal<br>
    Consultant en informatique : Mr Mohamed Siby | Mobile : +221 77 654 28 03<br>
    © 2026 Gestion du Personnel GRH. Tous droits réservés.
</footer>

<?php include 'footer.php'; ?>

<!-- Widget de Recherche & Topographie Croisée - Omega / Dabakh Fitness -->
<div class="card shadow-sm border-0 my-4 bg-light">
    <div class="card-body p-4">
        <h4 class="card-title text-dark fw-bold mb-3">
            <i class="fas fa-search-location text-primary"></i> Localisateur Global du Quartier & Siège Technique
        </h4>
        <p class="text-muted small mb-3">
            Tapez un mot-clé (ex: <strong>« mz 07 »</strong>, <strong>« mamoune »</strong>, <strong>« petits pas »</strong>, <strong>« dabakh »</strong> ou <strong>« omega »</strong>) pour afficher l'ensemble des repères de la zone.
        </p>
        
        <div class="input-group mb-3">
            <input type="text" id="searchInputOmega" class="form-control" placeholder="Rechercher par rue, école, résidence, fitness..." onkeyup="filterOmegaSearch()">
            <button class="btn btn-primary" type="button" onclick="filterOmegaSearch()">
                <i class="fas fa-search"></i> Explorer
            </button>
        </div>

        <!-- Résultat dynamique intelligent -->
        <div id="omegaSearchResult" class="mt-3"></div>
    </div>
</div>

<script>
function filterOmegaSearch() {
    let input = document.getElementById('searchInputOmega').value.toLowerCase().trim();
    let resultDiv = document.getElementById('omegaSearchResult');
    
    if (input === "") {
        resultDiv.innerHTML = "";
        return;
    }

    // Détection élargie de tous les repères de l'écosystème
    if (input.includes("mz") || input.includes("07") || input.includes("omega") || input.includes("mamoune") || input.includes("petits pas") || input.includes("dabakh") || input.includes("domicile")) {
        resultDiv.innerHTML = `
            <div class="alert alert-success border-0 shadow-sm animate__animated animate__fadeIn">
                <h5 class="fw-bold text-success mb-2">
                    <i class="fas fa-map-marked-alt"></i> Écosystème Connecté - Sacré-Cœur 3 VDN (Dakar)
                </h5>
                <ul class="list-unstyled mb-3 small text-dark">
                    <li class="mb-1"><strong><i class="fas fa-home text-danger"></i> Domicile & Siège technique :</strong> Rue MZ 07 (Point de départ et Lab Omega Informatique Consulting).</li>
                    <li class="mb-1"><strong><i class="fas fa-school text-primary"></i> Repère Nord :</strong> École <em>Les Petits Pas</em> (située sur la ruelle parallèle).</li>
                    <li class="mb-1"><strong><i class="fas fa-building text-warning"></i> Repère Est :</strong> <em>Résidences Mamoune</em> (visibles de profil à l'est depuis l'intersection).</li>
                    <li class="mb-1"><strong><i class="fas fa-dumbbell text-success"></i> Centre Partenaire :</strong> <em>Dabakh Fitness</em> (à 50 mètres à gauche de l'intersection).</li>
                </ul>
                <hr class="my-2">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="badge bg-dark">Implémentation Validée - 2026</span>
                    <a href="https://maps.google.com/?q=Sacre-Coeur+3+VDN+Dakar+Senegal" target="_blank" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-external-link-alt"></i> Ouvrir la zone sur Google Maps
                    </a>
                </div>
            </div>
        `;
    } else {
        resultDiv.innerHTML = `
            <div class="alert alert-warning border-0 small text-muted">
                Aucun résultat pour "<strong>${input}</strong>". Essayez de taper <code>mz 07</code>, <code>mamoune</code>, <code>petits pas</code> ou <code>dabakh</code>.
            </div>
        `;
    }
}
</script>

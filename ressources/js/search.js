document.addEventListener('DOMContentLoaded', function() {
    // Références aux éléments du DOM
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('searchInput');
    const searchLoader = document.getElementById('searchLoader');
    
    // Vérifier que les éléments existent
    if (!searchForm || !searchInput) {
        console.error('Éléments de recherche non trouvables');
        return;
    }
    
    // Variable pour stocker le timeout de recherche
    let searchTimeout;
    let isSearching = false;

    // Gérer la soumission du formulaire
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!isSearching) {
            performSearch();
        } else {
            console.log('Recherche déjà en cours, veuillez patienter...');
        }
    });

    // Fonction principale de recherche
    function performSearch() {
        const query = searchInput.value.trim();
        
        if (!query) {
            console.log('Requête vide, recherche annulée');
            return;
        }

        // Indiquer qu'une recherche est en cours
        isSearching = true;
        
        // Afficher l'indicateur de chargement
        if (searchLoader) {
            searchLoader.style.display = 'block';
        }

        // Mettre à jour l'URL
        const newUrl = `${window.location.pathname}?page=films/search&query=${encodeURIComponent(query)}`;
        window.history.pushState({ query: query }, '', newUrl);

        // Ajouter un timeout côté client (5 secondes)
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            if (searchLoader) {
                searchLoader.textContent = 'La recherche prend trop de temps. Veuillez réessayer.';
                setTimeout(function() {
                    searchLoader.style.display = 'none';
                    searchLoader.textContent = 'Recherche en cours...';
                    isSearching = false;
                }, 3000);
            }
        }, 5000);

        // Construire l'URL de l'API
        const apiUrl = `${window.location.origin}/AP3SiteFilms/index.php?page=films/search&query=${encodeURIComponent(query)}&ajax=1`;

        // Faire la requête AJAX
        fetch(apiUrl)
            .then(response => {
                clearTimeout(searchTimeout);
                if (!response.ok) {
                    throw new Error('Erreur réseau');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                
                // Si nous ne sommes pas sur la page de recherche, rediriger
                if (!window.location.href.includes('page=films/search')) {
                    window.location.href = newUrl;
                    return;
                }
                
                // Remplacer le contenu complet de la page
                document.documentElement.innerHTML = data.html;
                document.title = data.title;
                
                // Réinitialiser les scripts
                const scripts = document.querySelectorAll('script');
                scripts.forEach(script => {
                    if (script.src) {
                        const newScript = document.createElement('script');
                        newScript.src = script.src;
                        document.body.appendChild(newScript);
                    } else if (script.textContent) {
                        const newScript = document.createElement('script');
                        newScript.textContent = script.textContent;
                        document.body.appendChild(newScript);
                    }
                });
                
                console.log('Page de recherche mise à jour avec succès');
                
                // Réinitialiser le formulaire de recherche
                const newSearchForm = document.getElementById('searchForm');
                const newSearchInput = document.getElementById('searchInput');
                
                if (newSearchForm && newSearchInput) {
                    newSearchInput.value = query;
                    newSearchForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        // Récupérer la nouvelle valeur de recherche
                        const newQuery = newSearchInput.value.trim();
                        if (newQuery) {
                            // Mettre à jour l'URL
                            const newSearchUrl = `${window.location.pathname}?page=films/search&query=${encodeURIComponent(newQuery)}`;
                            window.history.pushState({ query: newQuery }, '', newSearchUrl);
                            
                            // Recharger la page avec la nouvelle requête
                            window.location.href = newSearchUrl;
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                const errorMessage = `<div class="error-container"><h1>Une erreur est survenue</h1><p>${error.message}</p></div>`;
                
                const mainContent = document.querySelector('main');
                if (mainContent) {
                    mainContent.innerHTML = errorMessage;
                } else {
                    alert('Erreur: ' + error.message);
                }
            })
            .finally(() => {
                // Masquer l'indicateur de chargement
                const currentSearchLoader = document.getElementById('searchLoader');
                if (currentSearchLoader) {
                    currentSearchLoader.style.display = 'none';
                    currentSearchLoader.textContent = 'Recherche en cours...';
                }
                
                // Réinitialiser l'état de recherche
                isSearching = false;
            });
    }

    // Gérer le bouton retour du navigateur
    window.addEventListener('popstate', function(e) {
        if (e.state && e.state.query) {
            const currentSearchInput = document.getElementById('searchInput');
            if (currentSearchInput) {
                currentSearchInput.value = e.state.query;
                window.location.reload();
            } else {
                window.location.reload();
            }
        } else {
            window.location.reload();
        }
    });

    console.log('Script de recherche initialisé');
});

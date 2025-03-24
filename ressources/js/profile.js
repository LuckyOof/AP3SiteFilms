document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('watchlistFilters');
    const loadingOverlay = document.querySelector('.loading-overlay');
    
    // Gestion des filtres
    filterForm.addEventListener('submit', function(e) {
        loadingOverlay.classList.add('active');
    });

    // Supprimer un film de la watchlist
    document.querySelectorAll('.btn-remove').forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            const filmId = this.dataset.filmId;
            
            if (confirm('Voulez-vous vraiment retirer ce film de votre watchlist ?')) {
                loadingOverlay.classList.add('active');
                
                try {
                    const response = await fetch(`${URL}films/watchlist/remove/${filmId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (response.ok) {
                        // Recharger la page pour mettre à jour la watchlist
                        window.location.reload();
                    } else {
                        alert('Une erreur est survenue lors de la suppression du film.');
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue lors de la communication avec le serveur.');
                } finally {
                    loadingOverlay.classList.remove('active');
                }
            }
        });
    });

    // Masquer l'overlay de chargement une fois la page chargée
    window.addEventListener('load', function() {
        loadingOverlay.classList.remove('active');
    });
});

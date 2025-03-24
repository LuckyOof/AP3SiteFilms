// Fonctions utilitaires
const Utils = {
    // Formater une date en format français
    formatDate: (dateString) => {
        const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('fr-FR', options);
    },

    // Formater un nombre en euros
    formatCurrency: (amount) => {
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR'
        }).format(amount);
    },

    // Afficher un message d'alerte
    showAlert: (message, type = 'success') => {
        const alertContainer = document.createElement('div');
        alertContainer.className = `alert alert-${type} fade-in`;
        alertContainer.textContent = message;

        // Ajouter l'alerte au début du main
        const main = document.querySelector('main');
        main.insertBefore(alertContainer, main.firstChild);

        // Supprimer l'alerte après 3 secondes
        setTimeout(() => {
            alertContainer.remove();
        }, 3000);
    }
};

// Gestionnaire d'événements pour les formulaires
document.addEventListener('DOMContentLoaded', () => {
    // Gérer les soumissions de formulaire en AJAX
    document.querySelectorAll('form[data-ajax="true"]').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: form.method,
                    body: formData
                });

                const data = await response.json();
                
                if (data.success) {
                    Utils.showAlert(data.message, 'success');
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                } else {
                    Utils.showAlert(data.message, 'error');
                }
            } catch (error) {
                Utils.showAlert('Une erreur est survenue', 'error');
                console.error('Erreur:', error);
            }
        });
    });

    // Gérer les liens de suppression avec confirmation
    document.querySelectorAll('a[data-confirm]').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            if (confirm(link.dataset.confirm)) {
                window.location.href = link.href;
            }
        });
    });
});

// Gestionnaire de la watchlist
const WatchlistManager = {
    toggle: async (filmId) => {
        try {
            const response = await fetch(`${URL}toggleWatchlist`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `filmId=${filmId}`
            });

            const data = await response.json();
            
            if (data.success) {
                // Mettre à jour l'interface
                const button = document.querySelector(`[data-watchlist-id="${filmId}"]`);
                if (button) {
                    button.classList.toggle('in-watchlist');
                    button.querySelector('i').classList.toggle('fas');
                    button.querySelector('i').classList.toggle('far');
                }
                Utils.showAlert(data.message);
            } else {
                Utils.showAlert(data.message, 'error');
            }
        } catch (error) {
            Utils.showAlert('Erreur lors de la mise à jour de la watchlist', 'error');
            console.error('Erreur:', error);
        }
    }
};

// Export des fonctionnalités
window.Utils = Utils;
window.WatchlistManager = WatchlistManager;

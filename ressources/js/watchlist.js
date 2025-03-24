async function toggleWatchlist(filmId, button) {
    try {
        const action = button.dataset.inWatchlist === 'true' ? 'remove' : 'add';
        const response = await fetch(URL + 'front/toggleWatchlist', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `filmId=${filmId}&action=${action}`
        });

        const data = await response.json();
        
        if (data.success) {
            const isNowInWatchlist = action === 'add';
            button.dataset.inWatchlist = isNowInWatchlist;
            button.innerHTML = isNowInWatchlist ? 
                '<i class="fas fa-check"></i> Dans ma watchlist' : 
                '<i class="fas fa-plus"></i> Ajouter à ma watchlist';
            button.classList.toggle('in-watchlist', isNowInWatchlist);
        } else {
            alert(data.error || 'Une erreur est survenue');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Une erreur est survenue lors de la communication avec le serveur');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Redirection vers la page de détail du film lorsqu'on clique sur une ligne
    const filmRows = document.querySelectorAll('.film-row');
    filmRows.forEach(row => {
        row.addEventListener('click', function() {
            const filmId = this.getAttribute('data-id');
            window.location.href = `index.php?action=show&controller=films&id=${filmId}`;
        });
    });

    // Mise à jour automatique du formulaire lors du changement de filtre
    const filterSelects = document.querySelectorAll('#filter-form select');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            // Si on change le type de classement, on réinitialise l'ordre à DESC
            if (this.id === 'sort') {
                document.getElementById('order').value = 'DESC';
            }
            
            // Soumettre le formulaire
            document.getElementById('filter-form').submit();
        });
    });
});

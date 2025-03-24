/**
 * Script pour amu00e9liorer les tableaux d'administration
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialisation du tri des tableaux
    initTableSorting();
    
    // Initialisation des boutons de suppression
    initDeleteButtons();
    
    // Initialisation de la recherche dans les tableaux
    initTableSearch();
});

/**
 * Initialise le tri des colonnes dans les tableaux
 */
function initTableSorting() {
    const tables = document.querySelectorAll('.admin-table');
    
    tables.forEach(table => {
        const headers = table.querySelectorAll('th.sortable');
        
        headers.forEach(header => {
            header.addEventListener('click', function() {
                const index = Array.from(header.parentNode.children).indexOf(header);
                const isAsc = header.classList.contains('sorted-asc');
                
                // Ru00e9initialiser toutes les colonnes
                headers.forEach(h => {
                    h.classList.remove('sorted-asc', 'sorted-desc');
                });
                
                // Du00e9finir la nouvelle direction de tri
                if (isAsc) {
                    header.classList.add('sorted-desc');
                    sortTable(table, index, false);
                } else {
                    header.classList.add('sorted-asc');
                    sortTable(table, index, true);
                }
            });
        });
    });
}

/**
 * Trie un tableau selon une colonne et une direction
 */
function sortTable(table, columnIndex, asc) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    // Trier les lignes
    const sortedRows = rows.sort((a, b) => {
        const cellA = a.querySelectorAll('td')[columnIndex].textContent.trim();
        const cellB = b.querySelectorAll('td')[columnIndex].textContent.trim();
        
        // Vu00e9rifier si c'est une date
        if (isDate(cellA) && isDate(cellB)) {
            const dateA = new Date(cellA.split('/').reverse().join('-'));
            const dateB = new Date(cellB.split('/').reverse().join('-'));
            return asc ? dateA - dateB : dateB - dateA;
        }
        
        // Vu00e9rifier si c'est un nombre
        if (!isNaN(cellA) && !isNaN(cellB)) {
            return asc ? parseFloat(cellA) - parseFloat(cellB) : parseFloat(cellB) - parseFloat(cellA);
        }
        
        // Tri alphanu00e9mu00e9rique
        return asc ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
    });
    
    // Vider et remplir le tableau avec les lignes triu00e9es
    while (tbody.firstChild) {
        tbody.removeChild(tbody.firstChild);
    }
    
    sortedRows.forEach(row => {
        tbody.appendChild(row);
    });
}

/**
 * Vu00e9rifie si une chau00eene est une date au format jj/mm/aaaa
 */
function isDate(dateStr) {
    const regex = /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/;
    return regex.test(dateStr);
}

/**
 * Initialise les boutons de suppression avec confirmation
 */
function initDeleteButtons() {
    const deleteButtons = document.querySelectorAll('.btn-delete');
    const modal = document.getElementById('deleteModal');
    
    if (!modal) return;
    
    const filmTitle = document.getElementById('filmTitle');
    const confirmDelete = document.getElementById('confirmDelete');
    const closeButtons = modal.querySelectorAll('.close, .btn-cancel');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const id = this.dataset.id;
            const title = this.dataset.title;
            
            filmTitle.textContent = title;
            confirmDelete.href = `${window.URL}admin/deleteFilm/${id}`;
            
            modal.style.display = 'block';
        });
    });
    
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    });
    
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
}

/**
 * Initialise la recherche dans les tableaux
 */
function initTableSearch() {
    const searchInputs = document.querySelectorAll('.admin-search input');
    
    searchInputs.forEach(input => {
        input.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const table = document.querySelector(this.dataset.target);
            
            if (!table) return;
            
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
}

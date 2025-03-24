/**
 * Script pour les fonctionnalitu00e9s du header
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les dropdowns
    initDropdowns();
    
    // Fonction pour initialiser les menus du00e9roulants
    function initDropdowns() {
        const dropdowns = document.querySelectorAll('.dropdown');
        
        dropdowns.forEach(dropdown => {
            const link = dropdown.querySelector('.nav-link');
            const menu = dropdown.querySelector('.dropdown-menu');
            
            if (link && menu) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    menu.classList.toggle('active');
                });
                
                // Fermer le menu lorsqu'on clique ailleurs
                document.addEventListener('click', function(e) {
                    if (!dropdown.contains(e.target)) {
                        menu.classList.remove('active');
                    }
                });
            }
        });
    }
});

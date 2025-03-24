/**
 * Gestion du thu00e8me clair/sombre
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialiser le thu00e8me
    initTheme();
    
    // Ajouter un u00e9couteur d'u00e9vu00e9nement pour le bouton de thu00e8me
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }
});

/**
 * Initialise le thu00e8me en fonction des pru00e9fu00e9rences de l'utilisateur
 */
function initTheme() {
    // Vu00e9rifier si l'utilisateur a du00e9ju00e0 une pru00e9fu00e9rence
    const savedTheme = localStorage.getItem('theme');
    
    // Si l'utilisateur a une pru00e9fu00e9rence, l'appliquer
    if (savedTheme) {
        document.documentElement.setAttribute('data-theme', savedTheme);
        updateThemeIcon(savedTheme);
    } else {
        // Sinon, vu00e9rifier les pru00e9fu00e9rences du systu00e8me
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const theme = prefersDark ? 'dark' : 'light';
        
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        updateThemeIcon(theme);
    }
}

/**
 * Bascule entre le thu00e8me clair et sombre
 */
function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    // Appliquer le nouveau thu00e8me
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    
    // Mettre u00e0 jour l'icu00f4ne
    updateThemeIcon(newTheme);
}

/**
 * Met u00e0 jour l'icu00f4ne du bouton de thu00e8me
 */
function updateThemeIcon(theme) {
    const themeToggle = document.getElementById('themeToggle');
    if (!themeToggle) return;
    
    const icon = themeToggle.querySelector('i');
    if (!icon) return;
    
    if (theme === 'dark') {
        icon.className = 'fas fa-sun';
        themeToggle.setAttribute('title', 'Passer au thu00e8me clair');
    } else {
        icon.className = 'fas fa-moon';
        themeToggle.setAttribute('title', 'Passer au thu00e8me sombre');
    }
}

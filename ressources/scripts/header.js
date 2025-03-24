// Fonction pour ouvrir/fermer le menu
function toggleMenu() {
    const menu = document.getElementById("menu-sidebar");
    const overlay = document.getElementById("overlay");

    // Active/Désactive le menu et l'overlay
    menu.classList.toggle("active");
    overlay.style.display = menu.classList.contains("active") ? "block" : "none";
}

// Fermer le menu en cliquant en dehors
function closeMenu(event) {
    const menu = document.getElementById("menu-sidebar");
    const menuIcon = document.getElementById("menu-icon");

    // Vérifie si le clic est en dehors du menu et du bouton
    if (!menu.contains(event.target) && !menuIcon.contains(event.target)) {
        menu.classList.remove("active");
        document.getElementById("overlay").style.display = "none";
    }
}

// Ajoute l'overlay dans le DOM
document.addEventListener("DOMContentLoaded", () => {
    const overlay = document.createElement("div");
    overlay.id = "overlay";
    document.body.appendChild(overlay);

    // Ferme le menu si on clique sur l'overlay
    overlay.addEventListener("click", () => {
        toggleMenu();
    });

    // Ferme le menu si on clique ailleurs
    document.addEventListener("click", closeMenu);
});


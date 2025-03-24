// Détecte le changement de thème en temps réel
const themeMediaQuery = window.matchMedia("(prefers-color-scheme: dark)");

function applyTheme() {
    if (themeMediaQuery.matches) {
        document.body.classList.add("dark-theme");
        document.body.classList.remove("light-theme");
    } else {
        document.body.classList.add("light-theme");
        document.body.classList.remove("dark-theme");
    }
}

themeMediaQuery.addEventListener("change", applyTheme);
applyTheme();

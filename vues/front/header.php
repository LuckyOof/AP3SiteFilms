<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title><?php echo $titre ?></title>
        <!-- Inclure Font Awesome pour les icônes -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <!-- Inclure les feuilles de style spécifiques -->
        <link rel="stylesheet" href="<?= CSS_URL ?>header.css">
        <link rel="stylesheet" href="<?= CSS_URL ?>showStyle.css">
        <!-- Inclure le script search.js directement dans le header -->
        <script>
            const URL = '<?= URL ?>';
        </script>
        <script src="<?= JS_URL ?>search.js"></script>
    </head>
    <body>
    <?php 
    // Vérifier si l'utilisateur est connecté
    $isLoggedIn = isset($_SESSION['user']);
    // Vérifier si l'utilisateur est admin
    $isAdmin = $isLoggedIn && isset($_SESSION['user']['estAdmin']) && $_SESSION['user']['estAdmin'] == 1;
    ?>
    <script src="<?= JS_URL ?>header.js"> </script>

    <?php 
    // Mise à jour complète du header avec de nouvelles fonctionnalités et un design moderne
    ?>
    <header class="site-header">
        <!-- Logo et titre -->
        <div class="header-left">
            <a href="<?= URL ?>" class="logo-link">
                <img src="<?= IMAGES_URL ?>oh!LiveRatingLogo.png" alt="LogoSite" class="site-logo">
            </a>
        </div>

        <!-- Barre de recherche -->
        <div class="search-container">
            <form action="javascript:void(0);" class="search-box" id="searchForm">
                <input type="hidden" name="page" value="films/search">
                <input type="text" name="query" id="searchInput" placeholder="Rechercher un film..." autocomplete="off" value="<?= isset($query) ? htmlspecialchars($query) : '' ?>">
                <div id="searchLoader" class="search-loader" style="display: none;">Recherche en cours...</div>
            </form>
        </div>

        <!-- Navigation principale -->
        <nav class="main-nav">
            <ul class="nav-list">
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link">Films <i class="fas fa-chevron-down"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?= URL ?>films/popular">Les plus populaires</a></li>
                        <li><a href="<?= URL ?>films/topRated">Les mieux notés</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="<?= URL ?>news" class="nav-link">Nouveautés</a>
                </li>
                <li class="nav-item">
                    <a href="<?= URL ?>classement" class="nav-link">Classement</a>
                </li>
            </ul>
        </nav>

        <!-- Menu utilisateur -->
        <div class="user-menu">
            <?php if($isLoggedIn): ?>
                <div class="user-dropdown">
                    <button class="user-button" id="profileToggle">
                        <div class="user-button-content">
                            <i class="fas fa-user-circle"></i>
                            <span><?= htmlspecialchars($_SESSION['user']['pseudo']) ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </button>
                    <!-- Sidebar pour profil utilisateur -->
                    <div class="profile-sidebar" id="profileSidebar">
                        <div class="sidebar-header">
                            <div class="user-avatar">
                                <i class="fas fa-user-circle fa-3x"></i>
                            </div>
                            <div class="user-info">
                                <h3><?= htmlspecialchars($_SESSION['user']['pseudo']) ?></h3>
                                <p><?= htmlspecialchars($_SESSION['user']['email']) ?></p>
                            </div>
                            <button class="close-sidebar" id="closeSidebar">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="sidebar-menu">
                            <a href="<?= URL ?>profile" class="sidebar-item">
                                <i class="fas fa-user"></i> Mon Profil
                            </a>
                            <!-- <a href="<?= URL ?>watchlist" class="sidebar-item">
                                <i class="fas fa-list"></i> Ma Watchlist
                            </a> -->
                            <!-- <a href="<?= URL ?>reviews" class="sidebar-item">
                                <i class="fas fa-star"></i> Mes Avis
                            </a> -->
                            
                            <?php if($isAdmin): ?>
                            <div class="sidebar-divider"></div>
                            <a href="<?= URL ?>admin" class="sidebar-item admin-link">
                                <i class="fas fa-cogs"></i> Administration
                            </a>
                            <?php endif; ?>
                            
                            <div class="sidebar-divider"></div>
                            <a href="<?= URL ?>logout" class="sidebar-item logout">
                                <i class="fas fa-sign-out-alt"></i> Déconnexion
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="auth-buttons">
                    <a href="<?= URL ?>login" class="btn btn-login">
                        <i class="fas fa-sign-in-alt"></i> Connexion
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </header>
    
    <!-- Script pour la sidebar -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profileToggle = document.getElementById('profileToggle');
            const profileSidebar = document.getElementById('profileSidebar');
            const closeSidebar = document.getElementById('closeSidebar');
            
            if (profileToggle && profileSidebar && closeSidebar) {
                // Ouvrir la sidebar - S'assurer que tout le bouton est cliquable
                profileToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation(); // Empêcher la propagation de l'événement
                    profileSidebar.classList.toggle('active');
                });
                
                // Fermer la sidebar
                closeSidebar.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation(); // Empêcher la propagation de l'événement
                    profileSidebar.classList.remove('active');
                });
                
                // Fermer la sidebar si on clique en dehors
                document.addEventListener('click', function(e) {
                    if (profileSidebar.classList.contains('active') && 
                        !profileSidebar.contains(e.target) && 
                        e.target !== profileToggle && 
                        !profileToggle.contains(e.target)) {
                        profileSidebar.classList.remove('active');
                    }
                });
                
                // Empêcher la fermeture lors du clic à l'intérieur de la sidebar
                profileSidebar.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        });
    </script>
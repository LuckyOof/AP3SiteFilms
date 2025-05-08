<div class="profile-container">
    <?php if (isset($_GET['success'])) : ?>
        <div class="success">Profil mis à jour avec succès</div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])) : ?>
        <div class="error"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <div class="profile-header">
        <h2>Mon Profil</h2>
        <button class="edit-profile-btn" onclick="toggleEditMode()">Modifier le profil</button>
    </div>

    <!-- Informations du profil -->
    <div class="profile-info" id="profile-view">
        <div class="info-group">
            <label>Pseudo :</label>
            <span><?= htmlspecialchars($_SESSION['user']['pseudo']) ?></span>
        </div>
        <div class="info-group">
            <label>Nom :</label>
            <span><?= htmlspecialchars($_SESSION['user']['nom']) ?></span>
        </div>
        <div class="info-group">
            <label>Prénom :</label>
            <span><?= htmlspecialchars($_SESSION['user']['prenom']) ?></span>
        </div>
        <div class="info-group">
            <label>Email :</label>
            <span><?= htmlspecialchars($_SESSION['user']['email']) ?></span>
        </div>
    </div>

    <!-- Formulaire de modification -->
    <form action="<?= URL ?>updateProfile" method="POST" class="profile-form hidden" id="profile-edit">
        <div class="form-group">
            <label for="pseudo">Pseudo :</label>
            <input type="text" id="pseudo" name="pseudo" value="<?= htmlspecialchars($_SESSION['user']['pseudo']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($_SESSION['user']['nom']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($_SESSION['user']['prenom']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['user']['email']) ?>" required>
        </div>

        <div class="password-section">
            <h3>Changer le mot de passe</h3>
            <div class="form-group password-field">
                <label for="current_password">Mot de passe actuel :</label>
                <div class="password-input-container">
                    <input type="password" id="current_password" name="current_password">
                    <span class="toggle-password" title="Afficher/Masquer le mot de passe">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                    </span>
                </div>
            </div>
            
            <div class="form-group password-field">
                <label for="new_password">Nouveau mot de passe :</label>
                <div class="password-input-container">
                    <input type="password" id="new_password" name="new_password">
                    <span class="toggle-password" title="Afficher/Masquer le mot de passe">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                    </span>
                </div>
                <div class="password-requirements">
                    <p>Le mot de passe doit contenir au moins :</p>
                    <ul>
                        <li id="length">12 caractères</li>
                        <li id="uppercase">Une lettre majuscule</li>
                        <li id="number">Un chiffre</li>
                        <li id="special">Un caractère spécial</li>
                    </ul>
                </div>
            </div>
            
            <div class="form-group password-field">
                <label for="confirm_password">Confirmer le mot de passe :</label>
                <div class="password-input-container">
                    <input type="password" id="confirm_password" name="confirm_password">
                    <span class="toggle-password" title="Afficher/Masquer le mot de passe">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                    </span>
                </div>
                <div id="password-match-message"></div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="save-btn">Enregistrer</button>
            <button type="button" class="cancel-btn" onclick="toggleEditMode()">Annuler</button>
        </div>
    </form>

    <!-- Section Watchlist -->
    <!-- <div class="profile-section watchlist-section">
        <h2>Ma Watchlist</h2> -->

        <!-- Filtres et tri -->
        <!-- <div class="watchlist-controls">
            <form id="watchlistFilters" method="POST">
                <div class="filters">
                    <div class="filter-group">
                        <label for="genre">Genre :</label>
                        <select name="genre" id="genre">
                            <option value="">Tous les genres</option>
                            <?php foreach ($filter_options['genres'] as $genre): ?>
                                <option value="<?= htmlspecialchars($genre) ?>" <?= isset($_POST['genre']) && $_POST['genre'] === $genre ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($genre) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="langue">Langue :</label>
                        <select name="langue" id="langue">
                            <option value="">Toutes les langues</option>
                            <?php foreach ($filter_options['langues'] as $langue): ?>
                                <option value="<?= htmlspecialchars($langue) ?>" <?= isset($_POST['langue']) && $_POST['langue'] === $langue ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($langue) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="annee">Année :</label>
                        <select name="annee" id="annee">
                            <option value="">Toutes les années</option>
                            <?php foreach ($filter_options['annees'] as $annee): ?>
                                <option value="<?= htmlspecialchars($annee) ?>" <?= isset($_POST['annee']) && $_POST['annee'] == $annee ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($annee) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group duree-filter">
                        <label>Durée (minutes) :</label>
                        <div class="duree-inputs">
                            <input type="number" name="dureeMin" id="dureeMin" placeholder="Min" min="0" value="<?= isset($_POST['dureeMin']) ? htmlspecialchars($_POST['dureeMin']) : '' ?>">
                            <span>à</span>
                            <input type="number" name="dureeMax" id="dureeMax" placeholder="Max" min="0" value="<?= isset($_POST['dureeMax']) ? htmlspecialchars($_POST['dureeMax']) : '' ?>">
                        </div>
                    </div>

                    <div class="sort-group">
                        <label for="sort">Trier par :</label>
                        <select name="sort" id="sort">
                            <option value="dateAjout" <?= (!isset($_POST['sort']) || $_POST['sort'] === 'dateAjout') ? 'selected' : '' ?>>Date d'ajout</option>
                            <option value="titre" <?= isset($_POST['sort']) && $_POST['sort'] === 'titre' ? 'selected' : '' ?>>Titre</option>
                            <option value="dateSortie" <?= isset($_POST['sort']) && $_POST['sort'] === 'dateSortie' ? 'selected' : '' ?>>Date de sortie</option>
                            <option value="duree" <?= isset($_POST['sort']) && $_POST['sort'] === 'duree' ? 'selected' : '' ?>>Durée</option>
                        </select>
                        <select name="order" id="order">
                            <option value="DESC" <?= (!isset($_POST['order']) || $_POST['order'] === 'DESC') ? 'selected' : '' ?>>Décroissant</option>
                            <option value="ASC" <?= isset($_POST['order']) && $_POST['order'] === 'ASC' ? 'selected' : '' ?>>Croissant</option>
                        </select>
                    </div>

                    <button type="submit" class="apply-filters">Appliquer</button>
                    <a href="?<?= http_build_query(['page' => 'profile']) ?>" class="reset-filters">Réinitialiser</a>
                </div>
            </form>
        </div> -->

        <!-- Liste des films -->
        <!-- <?php if (empty($watchlist)): ?>
            <div class="no-watchlist">
                <i class="fas fa-film"></i>
                <p>Votre watchlist est vide</p>
                <a href="<?= URL ?>" class="browse-movies">Parcourir les films</a>
            </div>
        <?php else: ?>
            <div class="watchlist-grid">
                <?php foreach ($watchlist as $film): ?>
                    <div class="film-card">
                        <img class="film-image" src="<?= !empty($film['image']) ? "/AP3SiteFilms/ressources/images/films/" . $film['image'] : URL . 'public/images/films/default.jpg' ?>" alt="<?= htmlspecialchars($film['titre']) ?>">
                        <div class="film-info">
                            <h3><?= htmlspecialchars($film['titre']) ?></h3>
                            <p class="film-meta">
                                <span><i class="fas fa-clock"></i> <?= $film['duree'] ?> min</span>
                                <span><i class="fas fa-calendar"></i> <?= date('Y', strtotime($film['dateSortie'])) ?></span>
                            </p>
                            <div class="film-actions">
                                <a href="<?= URL ?>films/<?= $film['idFilm'] ?>" class="btn-details">Détails</a>
                                <button class="btn-remove" data-film-id="<?= $film['idFilm'] ?>">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div> -->

    <!-- Indicateur de chargement -->
    <div class="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- Section des avis -->
    <div class="reviews-section">
        <h3>Mes Avis</h3>
        <?php if (!empty($reviews)) : ?>
            <div class="reviews-list">
                <?php foreach ($reviews as $review) : ?>
                    <div class="review-card">
                        <div class="review-header">
                            <h4><?= htmlspecialchars($review['film_titre']) ?></h4>
                            <div class="review-rating">Note : <?= htmlspecialchars($review['note']) ?>/5</div>
                        </div>
                        <div class="review-content">
                            <p><?= htmlspecialchars($review['commentaire']) ?></p>
                            <div class="review-date">Publié le <?= (new DateTime($review['datePublication']))->format('d/m/Y') ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="no-reviews">Vous n'avez pas encore publié d'avis.</p>
        <?php endif; ?>
    </div>
    <button class="logout"><a href="logout">Se déconnecter</a></button>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<script>
function toggleEditMode() {
    const viewMode = document.getElementById('profile-view');
    const editMode = document.getElementById('profile-edit');
    
    if (viewMode.classList.contains('hidden')) {
        viewMode.classList.remove('hidden');
        editMode.classList.add('hidden');
    } else {
        viewMode.classList.add('hidden');
        editMode.classList.remove('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Gestion des filtres de watchlist
    const filterForm = document.getElementById('watchlistFilters');
    const filterInputs = filterForm.querySelectorAll('select, input');
    
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            filterForm.submit();
        });
    });
    
    // Gestion des boutons de suppression de la watchlist
    document.querySelectorAll('.remove-from-watchlist').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const filmId = this.getAttribute('data-film-id');
            removeFromWatchlist(filmId, this);
        });
    });
    
    // Validation du mot de passe
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const lengthCheck = document.getElementById('length');
    const uppercaseCheck = document.getElementById('uppercase');
    const numberCheck = document.getElementById('number');
    const specialCheck = document.getElementById('special');
    const passwordMatchMessage = document.getElementById('password-match-message');
    
    // Fonction pour vérifier le mot de passe
    function checkPassword() {
        if (!newPasswordInput.value) {
            lengthCheck.classList.remove('valid');
            uppercaseCheck.classList.remove('valid');
            numberCheck.classList.remove('valid');
            specialCheck.classList.remove('valid');
            return;
        }
        
        const password = newPasswordInput.value;
        
        // Vérifier la longueur
        if (password.length >= 12) {
            lengthCheck.classList.add('valid');
        } else {
            lengthCheck.classList.remove('valid');
        }
        
        // Vérifier la présence d'une majuscule
        if (/[A-Z]/.test(password)) {
            uppercaseCheck.classList.add('valid');
        } else {
            uppercaseCheck.classList.remove('valid');
        }
        
        // Vérifier la présence d'un chiffre
        if (/[0-9]/.test(password)) {
            numberCheck.classList.add('valid');
        } else {
            numberCheck.classList.remove('valid');
        }
        
        // Vérifier la présence d'un caractère spécial
        if (/[^A-Za-z0-9]/.test(password)) {
            specialCheck.classList.add('valid');
        } else {
            specialCheck.classList.remove('valid');
        }
        
        // Vérifier si les mots de passe correspondent
        checkPasswordsMatch();
    }
    
    // Fonction pour vérifier si les mots de passe correspondent
    function checkPasswordsMatch() {
        const password = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (!password || !confirmPassword) {
            passwordMatchMessage.textContent = '';
            passwordMatchMessage.className = '';
            return;
        }
        
        if (password === confirmPassword) {
            passwordMatchMessage.textContent = 'Les mots de passe correspondent';
            passwordMatchMessage.className = 'match-success';
        } else {
            passwordMatchMessage.textContent = 'Les mots de passe ne correspondent pas';
            passwordMatchMessage.className = 'match-error';
        }
    }
    
    // Vérifier le mot de passe à chaque modification
    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', checkPassword);
    }
    
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', checkPasswordsMatch);
    }
    
    // Gestion des boutons pour afficher/masquer le mot de passe
    const toggleButtons = document.querySelectorAll('.toggle-password');
    toggleButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
});

async function removeFromWatchlist(filmId, button) {
    const loadingOverlay = document.querySelector('.loading-overlay');
    loadingOverlay.style.display = 'flex';
    
    try {
        const response = await fetch('<?= URL ?>toggleWatchlist', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `filmId=${filmId}&action=remove`
        });
        
        const data = await response.json();
        
        if (data.success) {
            const filmCard = button.closest('.film-card');
            filmCard.remove();
            
            // Vérifier s'il reste des films dans la watchlist
            const remainingFilms = document.querySelectorAll('.film-card');
            if (remainingFilms.length === 0) {
                const watchlistGrid = document.querySelector('.watchlist-grid');
                const emptyMessage = document.createElement('div');
                emptyMessage.className = 'empty-watchlist';
                emptyMessage.textContent = 'Votre watchlist est vide';
                watchlistGrid.parentNode.replaceChild(emptyMessage, watchlistGrid);
            }
        } else {
            alert(data.message || 'Erreur lors de la suppression du film');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Une erreur est survenue lors de la communication avec le serveur');
    } finally {
        loadingOverlay.style.display = 'none';
    }
}
</script>


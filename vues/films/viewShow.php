<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <?php if (isset($_SESSION['user'])) : ?>
    <meta name="user-id" content="<?= $_SESSION['user']['id'] ?>">
    <?php endif; ?>
</head>
<body>
    <div class="movie-details">
        <div class="movie-header">
            <h1><?= htmlspecialchars($film['titre']) ?></h1>
            <div class="movie-rating">
                <div class="movie-rating-stars">
                    <?php
                    // Utiliser la moyenne du tableau retourné par getAverageRating()
                    $noteMoyenne = $moyenne['moyenne'] ?? 0;
                    $noteMoyenne = round($noteMoyenne * 2) / 2; // Arrondir à 0.5 près
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $noteMoyenne) {
                            echo '<i class="fas fa-star"></i>';
                        } elseif ($i - 0.5 <= $noteMoyenne) {
                            echo '<i class="fas fa-star-half-alt"></i>';
                        } else {
                            echo '<i class="far fa-star"></i>';
                        }
                    }
                    ?>
                </div>
                <div class="movie-rating-average"><?= $noteMoyenne ? number_format($noteMoyenne, 1) : '0' ?></div>
                <div class="movie-rating-count">(<?= $moyenne['nombre'] ?? 0 ?> avis)</div>
            </div>
            <!-- <button 
                class="watchlist-btn <?= $in_watchlist ? 'in-watchlist' : '' ?>"
                onclick="toggleWatchlist(<?= $film['idFilm'] ?>, this)"
                data-in-watchlist="<?= $in_watchlist ? 'true' : 'false' ?>">
                <?php if ($in_watchlist) : ?>
                    <i class="fas fa-check"></i> Dans ma watchlist
                <?php else : ?>
                    <i class="fas fa-plus"></i> Ajouter à ma watchlist
                <?php endif; ?>
            </button> -->
        </div>

        <div class="movie-main-info">
            <div class="movie-poster">
                <?php if ($film['image']) : ?>
                    <img src="<?= URL ?>ressources/images/films/<?= $film['image'] ?>" alt="<?= htmlspecialchars($film['titre']) ?>">
                <?php else : ?>
                    <div class="no-image">Pas d'image disponible</div>
                <?php endif; ?>
            </div>

            <div class="movie-details-info">
                <div class="info-group">
                    <label>Date de sortie :</label>
                    <span><?= (new DateTime($film['dateSortie']))->format('d/m/Y') ?></span>
                </div>

                <div class="info-group">
                    <label>Durée :</label>
                    <span><?= $film['duree'] ?> minutes</span>
                </div>

                <div class="info-group">
                    <label>Langue originale :</label>
                    <span><?= htmlspecialchars($film['langueVO']) ?></span>
                </div>

                <div class="info-group">
                    <label>Réalisateur :</label>
                    <span><?= htmlspecialchars($film['realisateurPrenom'] . ' ' . $film['realisateurNom']) ?></span>
                </div>

                <?php if (!empty($film['genres'])) : ?>
                    <div class="info-group">
                        <label>Genres :</label>
                        <div class="genres-list">
                            <?php 
                            // Convertir la chaîne de genres en tableau
                            $genresArray = explode(',', $film['genres']);
                            foreach ($genresArray as $genre) : ?>
                                <a href="<?= URL ?>index.php?page=films&genre=<?= urlencode($genre) ?>" class="genre-tag">
                                    <?= htmlspecialchars($genre) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($film['boxOffice']) || !empty($film['coutTotal'])) : ?>
                    <div class="info-group">
                        <label>Informations financières :</label>
                        <?php if (!empty($film['coutTotal'])) : ?>
                            <div class="financial-item">
                                <span class="financial-label">Budget :</span>
                                <span class="financial-value"><?= number_format((float)$film['coutTotal'], 0, ',', ' ') ?> M€</span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($film['boxOffice'])) : ?>
                            <div class="financial-item">
                                <span class="financial-label">Box Office :</span>
                                <span class="financial-value"><?= number_format((float)$film['boxOffice'], 0, ',', ' ') ?> M€</span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="movie-description">
            <h2>Synopsis</h2>
            <p id="descri"><?= nl2br(htmlspecialchars($film['descri'])) ?></p>
        </div>

        <?php if (!empty($film['trailer'])) : ?>
            <div class="movie-trailer">
                <h2>Bande annonce</h2>
                <div class="trailer-container">
                    <?php
                    // Traitement de l'URL YouTube pour l'intégration correcte
                    $videoUrl = $film['trailer'];
                    $embedUrl = $videoUrl;
                    
                    // Si c'est une URL YouTube, la convertir en URL d'intégration
                    if (strpos($videoUrl, 'youtube.com/watch') !== false) {
                        // Extraire l'ID de la vidéo YouTube
                        preg_match('/[\?\&]v=([^\?\&]+)/', $videoUrl, $matches);
                        if (isset($matches[1])) {
                            $videoId = $matches[1];
                            $embedUrl = 'https://www.youtube.com/embed/' . $videoId;
                        }
                    } elseif (strpos($videoUrl, 'youtu.be/') !== false) {
                        // Format court de YouTube
                        $videoId = substr(parse_url($videoUrl, PHP_URL_PATH), 1);
                        $embedUrl = 'https://www.youtube.com/embed/' . $videoId;
                    }
                    ?>
                    <iframe 
                        src="<?= $embedUrl ?>" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
        <?php endif; ?>

        <!-- Section des avis -->
        <div class="avis-container">
            <h2>Commentaires</h2>
            
            <!-- Message de notification -->
            <div id="messageContainer" class="message"></div>
            
            <!-- Formulaire d'avis (visible uniquement si l'utilisateur est connecté) -->
            <?php if ($isLoggedIn) : ?>
                <div class="avis-form">
                    <h3>Ajouter un commentaire</h3>
                    <form id="avisForm" action="<?= URL ?>index.php?page=films/ajouterAvis" method="post">
                        <input type="hidden" name="idFilm" value="<?= $film['idFilm'] ?>">
                        <input type="hidden" id="avisNote" name="note" value="<?= $avisUtilisateur ? $avisUtilisateur['note'] : '0' ?>">
                        <input type="hidden" id="idAvis" name="idAvis" value="<?= $avisUtilisateur ? $avisUtilisateur['numAvis'] : null ?>">

                        <div class="avis-stars">
                            <?php for ($i = 1; $i <= 5; $i++) : ?>
                                <span class="avis-star <?= ($avisUtilisateur && $avisUtilisateur['note'] >= $i) ? 'selected' : '' ?>" data-rating="<?= $i ?>">
                                    <i class="fas fa-star"></i>
                                </span>
                            <?php endfor; ?>
                        </div>
                        
                        <textarea class="avis-textarea" name="commentaire" placeholder="Qu'avez-vous pensé de ce film ?"><?= $avisUtilisateur ? htmlspecialchars($avisUtilisateur['commentaire']) : '' ?></textarea>
                        
                        <button type="submit" class="avis-submit">
                            <?= $avisUtilisateur ? 'Modifier' : 'Commenter' ?>
                        </button>
                    </form>
                </div>
            <?php else : ?>
                <div class="login-prompt">
                    <p>Vous devez être <a href="<?= URL ?>index.php?page=login">connecté</a> pour laisser un commentaire.</p>
                </div>
            <?php endif; ?>
            
            <!-- Liste des avis -->
            <div class="avis-list">
                <?php if (!empty($avis)) : ?>
                    <?php foreach ($avis as $a) : ?>
                        <div class="avis-item">
                            <div class="avis-header">
                                <span class="avis-user"><?= htmlspecialchars($a['utilisateurPseudo'] ?? 'Utilisateur') ?></span>
                                <span class="avis-rating">
                                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                                        <?php if ($i <= $a['note']) : ?>
                                            <i class="fas fa-star"></i>
                                        <?php elseif ($i - 0.5 <= $a['note']) : ?>
                                            <i class="fas fa-star-half-alt"></i>
                                        <?php else : ?>
                                            <i class="far fa-star"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </span>
                                <span class="avis-date"><?= (new DateTime($a['datePublication']))->format('d/m/Y') ?></span>
                                <?php if ($isLoggedIn && $a['idUtilisateur'] == $_SESSION['user']['id']) : ?>
                                    <button class="delete-avis" data-avis-id="<?= $a['numAvis'] ?>" data-film-id="<?= $film['idFilm'] ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                            <div class="avis-content"><?= nl2br(htmlspecialchars($a['commentaire'])) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="no-avis">Aucun commentaire pour ce film</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    const URL = '<?= URL ?>';
    function toggleWatchlist(filmId, button) {
        const isInWatchlist = button.getAttribute('data-in-watchlist') === 'true';
        
        fetch(`${URL}index.php?page=films/toggleWatchlist&id=${filmId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                button.setAttribute('data-in-watchlist', (!isInWatchlist).toString());
                button.classList.toggle('in-watchlist');
                button.innerHTML = !isInWatchlist ? 
                    '<i class="fas fa-check"></i> Dans ma watchlist' : 
                    '<i class="fas fa-plus"></i> Ajouter à ma watchlist';
            }
        })
        .catch(error => console.error('Error:', error));
    }
    </script>
</body>
</html>
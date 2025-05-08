<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
</head>
<body>
    <div class="movies-container">
        <h1>Les plus populaires</h1>
        <div class="movies-grid">
            <?php foreach ($films as $movie) : ?>
                <div class="movie-card">
                    <div class="movie-image">
                        <?php if ($movie['image']) : ?>
                            <img src="/AP3SiteFilms/ressources/images/films/<?= $movie['image'] ?>" alt="<?= htmlspecialchars($movie['titre']) ?>">
                        <?php else : ?>
                            <div class="no-image">Pas d'image</div>
                        <?php endif; ?>
                    </div>
                    <div class="movie-info">
                        <h3><?= htmlspecialchars($movie['titre']) ?></h3>
                        <p class="movie-date">Sortie le <?= (new DateTime($movie['dateSortie']))->format('d/m/Y') ?></p>
                        <p class="movie-rating"><i class="fas fa-star"></i> <?= $movie['moyenne_notes'] ?>/5 (<?= $movie['nombre_notes'] ?> avis)</p>
                        <p class="movie-description"><?= htmlspecialchars(substr($movie['descri'], 0, 150)) ?>...</p>
                        <div class="movie-actions">
                            <a href="<?= URL ?>index.php?page=films/show&id=<?= $movie['idFilm'] ?>" class="view-btn">Voir plus</a>
                            <!-- <button 
                                class="watchlist-btn <?= isset($watchlistStatus[$movie['idFilm']]) && $watchlistStatus[$movie['idFilm']] ? 'in-watchlist' : '' ?>"
                                onclick="toggleWatchlist(<?= $movie['idFilm'] ?>, this)"
                                data-in-watchlist="<?= isset($watchlistStatus[$movie['idFilm']]) && $watchlistStatus[$movie['idFilm']] ? 'true' : 'false' ?>">
                                <?php if (isset($watchlistStatus[$movie['idFilm']]) && $watchlistStatus[$movie['idFilm']]) : ?>
                                    <i class="fas fa-check"></i> Dans ma watchlist
                                <?php else : ?>
                                    <i class="fas fa-plus"></i> Ajouter à ma watchlist
                                <?php endif; ?>
                            </button> -->
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>

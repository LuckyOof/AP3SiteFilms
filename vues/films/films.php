<div class="films-page">
    <!-- En-tête avec filtres -->
    <div class="films-header">
        <h1>Tous les films</h1>
        
        <div class="films-filters">
            <form method="POST" action="<?= URL ?>films" class="filter-form">
                <div class="filter-group">
                    <label for="genre">Genre :</label>
                    <select name="genre" id="genre">
                        <option value="">Tous les genres</option>
                        <?php foreach ($genres as $genre): ?>
                            <option value="<?= htmlspecialchars($genre) ?>" <?= $currentGenre === $genre ? 'selected' : '' ?>>
                                <?= htmlspecialchars($genre) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="sort">Trier par :</label>
                    <select name="sort" id="sort">
                        <option value="dateSortie" <?= $currentSort === 'dateSortie' ? 'selected' : '' ?>>Date de sortie</option>
                        <option value="titre" <?= $currentSort === 'titre' ? 'selected' : '' ?>>Titre</option>
                        <option value="duree" <?= $currentSort === 'duree' ? 'selected' : '' ?>>Durée</option>
                        <?php if (isset($rating_enabled)): ?>
                            <option value="rating" <?= $currentSort === 'rating' ? 'selected' : '' ?>>Note</option>
                        <?php endif; ?>
                    </select>
                    <select name="order" id="order">
                        <option value="DESC" <?= $currentOrder === 'DESC' ? 'selected' : '' ?>>Décroissant</option>
                        <option value="ASC" <?= $currentOrder === 'ASC' ? 'selected' : '' ?>>Croissant</option>
                    </select>
                </div>

                <button type="submit" class="btn-filter">Appliquer</button>
            </form>
        </div>
    </div>

    <!-- Grille des films -->
    <div class="films-grid">
        <?php foreach ($films as $film): ?>
            <div class="film-card">
                <div class="film-poster">
                    <img src="/AP3SiteFilms/ressources/images/films/<?= $film['image'] ?>" 
                         alt="<?= htmlspecialchars($film['titre']) ?>"
                         loading="lazy"
                         class="film-image">
                    
                    <?php if (isset($film['moyenne_notes'])): ?>
                        <div class="rating-badge">
                            <i class="fas fa-star"></i>
                            <span><?= number_format($film['moyenne_notes'], 1) ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="film-overlay">
                        <div class="film-actions">
                            <a href="<?= URL ?>index.php?page=films/show&id=<?= $film['idFilm'] ?>" class="btn-details">
                                <i class="fas fa-info-circle"></i> Détails
                            </a>
                            <!-- <?php if (isset($_SESSION['user'])): ?>
                                <button class="btn-favorite <?= isset($film['in_watchlist']) && $film['in_watchlist'] ? 'active' : '' ?>"
                                        data-film-id="<?= $film['idFilm'] ?>">
                                    <i class="<?= isset($film['in_watchlist']) && $film['in_watchlist'] ? 'fas' : 'far' ?> fa-heart"></i>
                                </button>
                            <?php endif; ?> -->
                        </div>
                    </div>
                </div>

                <div class="film-info">
                    <h3><?= htmlspecialchars($film['titre']) ?></h3>
                    <div class="film-meta">
                        <span><i class="fas fa-clock"></i> <?= $film['duree'] ?> min</span>
                        <span><i class="fas fa-calendar"></i> <?= (new DateTime($film['dateSortie']))->format('Y') ?></span>
                        <span>
                            <i class="fas fa-film"></i> <?= htmlspecialchars($film['genres'][0]) ?>
                        </span>
                    </div>
                    <p class="film-description"><?= htmlspecialchars(substr($film['descri'], 0, 100)) ?>...</p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination
    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($current_page > 1): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $current_page - 1])) ?>" class="page-link">
                    <i class="fas fa-chevron-left"></i> Précédent
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == 1 || $i == $total_pages || ($i >= $current_page - 2 && $i <= $current_page + 2)): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                       class="page-link <?= $i === $current_page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php elseif ($i == $current_page - 3 || $i == $current_page + 3): ?>
                    <span class="page-dots">...</span>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $current_page + 1])) ?>" class="page-link">
                    Suivant <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?> -->
</div>

<!-- Message si aucun film -->
<?php if (empty($films)): ?>
    <div class="no-films">
        <i class="fas fa-film"></i>
        <p>Aucun film ne correspond à vos critères</p>
        <a href="<?= URL ?>/AP3SiteFilms/films" class="btn-reset">Réinitialiser les filtres</a>
    </div>
<?php endif; ?>

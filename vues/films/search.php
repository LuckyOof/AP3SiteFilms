<?php if (!empty($films)): ?>
    <div class="search-results-container">
        <h1>Résultats de recherche pour "<?= htmlspecialchars($query) ?>"</h1>
        <div class="films-grid">
            <?php foreach ($films as $film): ?>
                <div class="film-card">
                    <a href="<?= URL ?>index.php?page=films/show&id=<?= $film['idFilm'] ?>">
                        <img src="<?= IMAGES_URL . 'films/' . basename($film['image']) ?>" alt="<?= htmlspecialchars($film['titre']) ?>" 
                             class="film-image" onerror="this.src='<?= IMAGES_URL ?>films/default.jpg'">
                        <div class="film-info">
                            <h3 class="film-title"><?= htmlspecialchars($film['titre']) ?></h3>
                            <p class="film-director"><?= htmlspecialchars($film['realisateurPrenom'] . ' ' . $film['realisateurNom']) ?></p>
                            <?php if (!empty($film['genres'])): ?>
                                <div class="film-genres">
                                    <?php 
                                    // Convertir la chaine de genres en tableau
                                    $genresArray = explode(',', $film['genres']);
                                    foreach ($genresArray as $genre): ?>
                                        <span class="genre-tag"><?= htmlspecialchars($genre) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <div class="film-rating">
                                <span class="stars">★</span>
                                <span class="rating-value"><?= number_format($film['moyenne_notes'], 1) ?></span>
                                <span class="rating-count">(<?= $film['nombre_notes'] ?>)</span>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php else: ?>
    <div class="no-results">
        <h1>Aucun résultat trouvé pour "<?= htmlspecialchars($query) ?>"</h1>
        <p>Essayez avec d'autres mots-clés ou vérifiez l'orthographe.</p>
    </div>
<?php endif; ?>

<?php
// Bannière principale avec les derniers films
$featuredFilm = $latest_films[0] ?? null;
?>

<!-- Bannière principale -->
<?php if ($featuredFilm): ?>
<div class="hero-banner" style="background-image: url('<?= URL ?>ressources/images/films/<?= !empty($featuredFilm['image']) ? $featuredFilm['image'] : 'default.jpg' ?>')">
    <div class="hero-content">
        <h1><?= htmlspecialchars($featuredFilm['titre']) ?></h1>
        <p class="hero-description"><?= htmlspecialchars(substr($featuredFilm['descri'], 0, 200)) ?>...</p>
        <div class="hero-meta">
            <span><i class="fas fa-clock"></i> <?= $featuredFilm['duree'] ?> min</span>
            <span><i class="fas fa-calendar"></i> <?= (new DateTime($featuredFilm['dateSortie']))->format('Y') ?></span>
            <span><i class="fas fa-film"></i> <?= htmlspecialchars($featuredFilm['realisateurPrenom'] . ' ' . $featuredFilm['realisateurNom']) ?></span>
        </div>
        <a href="<?= URL ?>index.php?page=films/show&id=<?= $featuredFilm['idFilm'] ?>" class="hero-cta">Voir plus</a>
    </div>
</div>
<?php endif; ?>

<!-- Derniers films -->
<section class="films-section">
    <div class="section-header">
        <h2>Dernières sorties</h2>
        <a href="<?= URL ?>films" class="view-all">Voir tout <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="films-grid">
        <?php foreach (array_slice($latest_films, 1) as $film): ?>
            <div class="film-card">
                <div class="film-poster">
                    <img src="<?= URL ?>ressources/images/films/<?= !empty($film['image']) ? $film['image'] : 'default.jpg' ?>" 
                         alt="<?= htmlspecialchars($film['titre']) ?>">
                    <div class="film-overlay">
                        <a href="<?= URL ?>index.php?page=films/show&id=<?= $film['idFilm'] ?>" class="btn-details">Voir plus</a>
                    </div>
                </div>
                <div class="film-info">
                    <h3><?= htmlspecialchars($film['titre']) ?></h3>
                    <div class="film-meta">
                        <span><i class="fas fa-clock"></i> <?= $film['duree'] ?> min</span>
                        <span><i class="fas fa-calendar"></i> <?= (new DateTime($film['dateSortie']))->format('Y') ?></span>
                        <span><i class="fas fa-film"></i> <?= htmlspecialchars($film['realisateurPrenom'] . ' ' . $film['realisateurNom']) ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Films les mieux notés -->
<section class="films-section top-rated">
    <div class="section-header">
        <h2>Les mieux notés</h2>
        <a href="<?= URL ?>films" class="view-all">Voir tout <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="films-grid">
        <?php foreach ($top_rated_films as $film): ?>
            <div class="film-card">
                <div class="film-poster">
                    <img src="<?= URL ?>ressources/images/films/<?= !empty($film['image']) ? $film['image'] : 'default.jpg' ?>" 
                         alt="<?= htmlspecialchars($film['titre']) ?>">
                    <div class="film-overlay">
                        <a href="<?= URL ?>index.php?page=films/show&id=<?= $film['idFilm'] ?>" class="btn-details">Voir plus</a>
                    </div>
                    <div class="rating-badge">
                        <i class="fas fa-star"></i>
                        <span><?= number_format($film['moyenne_notes'], 1) ?></span>
                    </div>
                </div>
                <div class="film-info">
                    <h3><?= htmlspecialchars($film['titre']) ?></h3>
                    <div class="film-meta">
                        <span><i class="fas fa-clock"></i> <?= $film['duree'] ?> min</span>
                        <span><i class="fas fa-user-friends"></i> <?= $film['nombre_notes'] ?> avis</span>
                        <span><i class="fas fa-film"></i> <?= htmlspecialchars($film['realisateurPrenom'] . ' ' . $film['realisateurNom']) ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Films par genre -->
<section class="films-section genres-section">
    <div class="section-header">
        <h2>Explorer par genre</h2>
    </div>
    <?php foreach ($films_by_genre as $genre => $films): ?>
        <?php if (!empty($films)): ?>
            <div class="genre-block">
                <div class="genre-header">
                    <h3><?= htmlspecialchars($genre) ?></h3>
                    <a href="<?= URL ?>films" class="view-all">Voir tout <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="films-slider">
                    <?php foreach ($films as $film): ?>
                        <div class="film-card">
                            <div class="film-poster">
                                <img src="<?= URL ?>ressources/images/films/<?= !empty($film['image']) ? $film['image'] : 'default.jpg' ?>" 
                                     alt="<?= htmlspecialchars($film['titre']) ?>">
                                <div class="film-overlay">
                                    <a href="<?= URL ?>index.php?page=films/show&id=<?= $film['idFilm'] ?>" class="btn-details">Voir plus</a>
                                </div>
                            </div>
                            <div class="film-info">
                                <h3><?= htmlspecialchars($film['titre']) ?></h3>
                                <div class="film-meta">
                                    <span><i class="fas fa-clock"></i> <?= $film['duree'] ?> min</span>
                                    <span><i class="fas fa-calendar"></i> <?= (new DateTime($film['dateSortie']))->format('Y') ?></span>
                                    <span><i class="fas fa-film"></i> <?= htmlspecialchars($film['realisateurPrenom'] . ' ' . $film['realisateurNom']) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</section>

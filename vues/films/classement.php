<head>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4"><?= $titre ?></h1>
            
            <!-- Filtres -->
            <div class="card mb-4 filter-card">
                <div class="card-header">
                    <h5 class="mb-0">Filtres</h5>
                </div>
                <div class="card-body">
                    <form action="classement" method="POST" id="filter-form" class="row">
                        <input type="hidden" name="controller" value="films">
                        <input type="hidden" name="action" value="classement">
                        <!-- Type de classement -->
                        <div class="col-md-3 mb-3">
                            <label for="sort" class="form-label">Classement par</label>
                            <select name="sort" id="sort" class="form-select">
                                <option value="note" <?= $current_sort['by'] == 'note' ? 'selected' : '' ?>>Note moyenne</option>
                                <option value="popularite" <?= $current_sort['by'] == 'popularite' ? 'selected' : '' ?>>Popularité</option>
                                <option value="recents" <?= $current_sort['by'] == 'recents' ? 'selected' : '' ?>>Date de sortie</option>
                                <option value="boxOffice" <?= $current_sort['by'] == 'boxOffice' ? 'selected' : '' ?>>Box-office</option>
                            </select>
                        </div>
                        
                        <!-- Ordre de tri -->
                        <div class="col-md-3 mb-3">
                            <label for="order" class="form-label">Ordre</label>
                            <select name="order" id="order" class="form-select">
                                <option value="DESC" <?= $current_sort['order'] == 'DESC' ? 'selected' : '' ?>>Décroissant</option>
                                <option value="ASC" <?= $current_sort['order'] == 'ASC' ? 'selected' : '' ?>>Croissant</option>
                            </select>
                        </div>
                        
                        <!-- Nombre de résultats -->
                        <div class="col-md-3 mb-3">
                            <label for="limit" class="form-label">Nombre de films</label>
                            <select name="limit" id="limit" class="form-select">
                                <option value="10" <?= $current_sort['limit'] == 10 ? 'selected' : '' ?>>10</option>
                                <option value="20" <?= $current_sort['limit'] == 20 ? 'selected' : '' ?>>20</option>
                                <option value="50" <?= $current_sort['limit'] == 50 ? 'selected' : '' ?>>50</option>
                            </select>
                        </div>
                        
                        <!-- Bouton Appliquer -->
                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Appliquer</button>
                        </div>
                        
                        <!-- Filtres supplémentaires -->
                        <div class="col-md-4 mb-3">
                            <label for="genre" class="form-label">Genre</label>
                            <select name="genre" id="genre" class="form-select">
                                <option value="">Tous les genres</option>
                                <?php foreach ($filter_options['genres'] as $genre): ?>
                                    <option value="<?= $genre ?>" <?= isset($_POST['genre']) && $_POST['genre'] == $genre ? 'selected' : '' ?>>
                                        <?= $genre ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="annee" class="form-label">Année</label>
                            <select name="annee" id="annee" class="form-select">
                                <option value="">Toutes les années</option>
                                <?php foreach ($filter_options['annees'] as $annee): ?>
                                    <option value="<?= $annee ?>" <?= isset($_POST['annee']) && $_POST['annee'] == $annee ? 'selected' : '' ?>>
                                        <?= $annee ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="langue" class="form-label">Langue originale</label>
                            <select name="langue" id="langue" class="form-select">
                                <option value="">Toutes les langues</option>
                                <?php foreach ($filter_options['langues'] as $langue): ?>
                                    <option value="<?= $langue ?>" <?= isset($_POST['langue']) && $_POST['langue'] == $langue ? 'selected' : '' ?>>
                                        <?= $langue ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Résultats du classement -->
            <div class="classement-results">
                <?php if (empty($classement)): ?>
                    <div class="alert alert-info">Aucun film ne correspond à vos critères de recherche.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Rang</th>
                                    <th>Film</th>
                                    <th>Réalisateur</th>
                                    <th>Date de sortie</th>
                                    <th>Note</th>
                                    <th>Avis</th>
                                    <?php if ($current_sort['by'] == 'boxOffice'): ?>
                                    <th>Box-office</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $rank = 1; ?>
                                <?php foreach ($classement as $film): ?>
                                    <tr class="film-row" data-id="<?= $film['idFilm'] ?>">
                                        <td class="rank"><?= $rank++ ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="film-thumbnail me-3">
                                                    <img src="<?= URL ?>ressources/images/films/<?= !empty($film['image']) ? $film['image'] : 'default.jpg' ?>" alt="<?= $film['titre'] ?>" class="img-thumbnail">
                                                </div>
                                                <div>
                                                    <h5 class="mb-0"><?= $film['titre'] ?></h5>
                                                    <small class="text-muted">
                                                        <?php if (!empty($film['genres'])): ?>
                                                            <?= implode(', ', $film['genres']) ?>
                                                        <?php else: ?>
                                                            Non catégorisé
                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= $film['realisateurPrenom'] . ' ' . $film['realisateurNom'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($film['dateSortie'])) ?></td>
                                        <td>
                                            <div class="rating-stars">
                                                <?php 
                                                // Convertir la note de l'échelle 0-1 à l'échelle 0-5
                                                $note = isset($film['note_moyenne']) ? $film['note_moyenne'] * 5 : 0;
                                                $note = round($note * 2) / 2; // Arrondir à 0.5 près
                                                for ($i = 1; $i <= 5; $i++): 
                                                ?>
                                                    <?php if ($i <= $note): ?>
                                                        <i class="fas fa-star"></i>
                                                    <?php elseif ($i - 0.5 == $note): ?>
                                                        <i class="fas fa-star-half-alt"></i>
                                                    <?php else: ?>
                                                        <i class="far fa-star"></i>
                                                    <?php endif; ?>
                                                <?php endfor; ?>
                                                <span class="ms-2"><?= number_format($note, 1) ?>/5</span>
                                            </div>
                                        </td>
                                        <td><?= isset($film['nombre_avis']) ? $film['nombre_avis'] : 0 ?></td>
                                        <?php if ($current_sort['by'] == 'boxOffice' && isset($film['recettes'])): ?>
                                        <td><?= number_format($film['recettes'], 0, ',', ' ') ?> $</td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

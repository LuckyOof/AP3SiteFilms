<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <?php if ($acteur['photo']): ?>
                    <img src="<?= IMAGES_URL ?>acteurs/<?= $acteur['photo'] ?>" class="card-img-top" alt="<?= htmlspecialchars($acteur['prenom'] . ' ' . $acteur['nom']) ?>">
                <?php else: ?>
                    <img src="<?= IMAGES_URL ?>default-actor.jpg" class="card-img-top" alt="Photo par défaut">
                <?php endif; ?>
                
                <div class="card-body">
                    <h1 class="card-title"><?= htmlspecialchars($acteur['prenom'] . ' ' . $acteur['nom']) ?></h1>
                    <?php if ($acteur['nationalite']): ?>
                        <p class="card-text"><i class="fas fa-globe"></i> <?= htmlspecialchars($acteur['nationalite']) ?></p>
                    <?php endif; ?>
                    <?php if ($acteur['dateNaissance']): ?>
                        <p class="card-text"><i class="fas fa-birthday-cake"></i> <?= date('d/m/Y', strtotime($acteur['dateNaissance'])) ?></p>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['user']) && $_SESSION['user']['estAdmin']): ?>
                        <div class="mt-3">
                            <a href="<?= URL ?>admin/acteurs/edit/<?= $acteur['idActeur'] ?>" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <h2>Filmographie</h2>
            <?php if (!empty($films)): ?>
                <div class="row row-cols-1 row-cols-md-2 g-4">
                    <?php foreach ($films as $film): ?>
                        <div class="col">
                            <div class="card h-100">
                                <?php if ($film['image']): ?>
                                    <img src="<?= IMAGES_URL ?>films/<?= $film['image'] ?>" class="card-img-top" alt="<?= htmlspecialchars($film['titre']) ?>">
                                <?php else: ?>
                                    <img src="<?= IMAGES_URL ?>default-movie.jpg" class="card-img-top" alt="Image par défaut">
                                <?php endif; ?>
                                
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($film['titre']) ?></h5>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> <?= date('Y', strtotime($film['dateSortie'])) ?>
                                        </small>
                                    </p>
                                    <a href="<?= URL ?>films/<?= $film['idFilm'] ?>" class="btn btn-primary">
                                        <i class="fas fa-info-circle"></i> Voir le film
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Aucun film n'est associé à cet acteur.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($_SESSION['user']) && $_SESSION['user']['estAdmin']): ?>
        <!-- Modal de confirmation de suppression -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Êtes-vous sûr de vouloir supprimer l'acteur <?= htmlspecialchars($acteur['prenom'] . ' ' . $acteur['nom']) ?> ?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <a href="<?= URL ?>admin/acteurs/delete/<?= $acteur['idActeur'] ?>" class="btn btn-danger">Supprimer</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

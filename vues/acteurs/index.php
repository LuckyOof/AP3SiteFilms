<div class="container mt-4">
    <h1 class="mb-4">Acteurs</h1>

    <?php if (isset($_SESSION['user']) && $_SESSION['user']['estAdmin']): ?>
        <div class="mb-4">
            <a href="<?= URL ?>admin/acteurs/add" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter un acteur
            </a>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success'] ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error'] ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($acteurs as $acteur): ?>
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($acteur['prenom'] . ' ' . $acteur['nom']) ?></h5>
                        <?php if (isset($acteur['nationalite']) && !empty($acteur['nationalite'])): ?>
                            <p class="card-text"><i class="fas fa-globe"></i> <?= htmlspecialchars($acteur['nationalite']) ?></p>
                        <?php endif; ?>
                        <?php if (isset($acteur['dateNaissance']) && !empty($acteur['dateNaissance'])): ?>
                            <p class="card-text"><i class="fas fa-birthday-cake"></i> <?= date('d/m/Y', strtotime($acteur['dateNaissance'])) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="card-footer">
                        <a href="<?= URL ?>acteurs/<?= $acteur['idActeur'] ?>" class="btn btn-info">
                            <i class="fas fa-info-circle"></i> Détails
                        </a>
                        <?php if (isset($_SESSION['user']) && $_SESSION['user']['estAdmin']): ?>
                            <a href="<?= URL ?>admin/acteurs/edit/<?= $acteur['idActeur'] ?>" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $acteur['idActeur'] ?>">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if (isset($_SESSION['user']) && $_SESSION['user']['estAdmin']): ?>
                <!-- Modal de confirmation de suppression -->
                <div class="modal fade" id="deleteModal<?= $acteur['idActeur'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $acteur['idActeur'] ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel<?= $acteur['idActeur'] ?>">Confirmer la suppression</h5>
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
        <?php endforeach; ?>
    </div>
</div>

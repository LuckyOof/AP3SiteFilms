<?php
/**
 * Vue du tableau de bord d'administration
 */
?>

<div class="admin-container">
    <div class="admin-header">
        <h1><i class="fas fa-tachometer-alt"></i> Tableau de bord d'administration</h1>
    </div>
    
    <?php if(isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_type'] ?>">
            <p><?= $_SESSION['message'] ?></p>
        </div>
        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
    <?php endif; ?>
    
    <div class="dashboard-stats">
        <div class="stat-card">
            <h3><i class="fas fa-film"></i> Films</h3>
            <p class="stat-number"><?= $totalFilms ?></p>
            <a href="<?= URL ?>admin/films" class="btn btn-primary">Gérer les films</a>
        </div>
        
        <div class="stat-card">
            <h3><i class="fas fa-tags"></i> Genres</h3>
            <p class="stat-number"><?= $totalGenres ?></p>
            <a href="<?= URL ?>admin/genres" class="btn btn-primary">Gérer les genres</a>
        </div>

        <div class="stat-card">
            <h3><i class="fas fa-video"></i> Réalisateurs</h3>
            <p class="stat-number"><?= $totalRealisateurs ?></p>
            <a href="<?= URL ?>admin/realisateurs" class="btn btn-primary">Gérer les réalisateurs</a>
        </div>
        
        <div class="stat-card">
            <h3><i class="fas fa-user-tie"></i> Acteurs</h3>
            <p class="stat-number"><?= $totalActeurs ?></p>
            <!-- <a href="<?= URL ?>admin/acteurs" class="btn btn-primary">Gérer les acteurs</a> -->
        </div>
        
        <div class="stat-card">
            <h3><i class="fas fa-star"></i> Avis</h3>
            <p class="stat-number"><?= $totalAvis ?></p>
            <!-- <a href="<?= URL ?>admin/avis" class="btn btn-primary">Gérer les avis</a> -->
        </div>
        
        <div class="stat-card">
            <h3><i class="fas fa-users"></i> Utilisateurs</h3>
            <p class="stat-number"><?= $totalUtilisateurs ?></p>
            <!-- <a href="<?= URL ?>admin/utilisateurs" class="btn btn-primary">Gérer les utilisateurs</a> -->
        </div>
    </div>
    
    <div class="dashboard-actions">
        <h2><i class="fas fa-bolt"></i> Actions rapides</h2>
        <div class="action-buttons">
            <a href="<?= URL ?>admin/addFilm" class="btn btn-add"><i class="fas fa-plus"></i> Ajouter un film</a>
            <a href="<?= URL ?>admin/addGenre" class="btn btn-add"><i class="fas fa-plus"></i> Ajouter un genre</a>
            <a href="<?= URL ?>admin/addActeur" class="btn btn-add"><i class="fas fa-plus"></i> Ajouter un acteur</a>
            <a href="<?= URL ?>admin/addRealisateur" class="btn btn-add"><i class="fas fa-plus"></i> Ajouter un réalisateur</a>
        </div>
    </div>
    
    <div class="recent-activity">
        <h2><i class="fas fa-clock"></i> Films récemment ajoutés</h2>
        
        <div class="admin-search">
            <input type="text" class="search-input" placeholder="Rechercher un film..." data-target=".admin-table">
        </div>
        
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="sortable">Titre</th>
                        <th class="sortable">Date de sortie</th>
                        <th class="sortable">Durée</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($recentFilms)): ?>
                        <?php foreach($recentFilms as $film): ?>
                            <tr>
                                <td><?= htmlspecialchars($film['titre']) ?></td>
                                <td><?= date('d/m/Y', strtotime($film['dateSortie'])) ?></td>
                                <td><?= $film['duree'] ?> min</td>
                                <td class="actions">
                                    <a href="<?= URL ?>index.php?page=films/show&id=<?= $film['idFilm'] ?>" class="btn-action btn-view" title="Voir"><i class="fas fa-eye"></i></a>
                                    <a href="<?= URL ?>admin/editFilm/<?= $film['idFilm'] ?>" class="btn-action btn-edit" title="Modifier"><i class="fas fa-edit"></i></a>
                                    <a href="#" class="btn-action btn-delete" data-id="<?= $film['idFilm'] ?>" data-title="<?= htmlspecialchars($film['titre']) ?>" title="Supprimer"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Aucun film récent</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Confirmation de suppression</h2>
        <p>Êtes-vous sûr de vouloir supprimer le film <span id="filmTitle"></span> ?</p>
        <div class="modal-actions">
            <a href="#" id="confirmDelete" class="btn btn-danger">Supprimer</a>
            <button class="btn btn-cancel">Annuler</button>
        </div>
    </div>
</div>

<script>
    // Variable globale pour l'URL de base
    window.URL = "<?= URL ?>";
</script>

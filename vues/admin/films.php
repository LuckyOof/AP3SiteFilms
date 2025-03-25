<div class="admin-container">
    <div class="admin-header">
        <h1><i class="fas fa-film"></i> Gestion des Films</h1>
        <a href="<?= URL ?>admin/addFilm" class="btn-add"><i class="fas fa-plus-circle"></i> Ajouter un film</a>
    </div>
    
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success'] ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['errors'])): ?>
        <div class="alert alert-danger">
            <?php foreach($_SESSION['errors'] as $error): ?>
                <p><?= $error ?></p>
            <?php endforeach; ?>
            <?php unset($_SESSION['errors']); ?>
        </div>
    <?php endif; ?>
    
    <div class="admin-search">
        <input type="text" id="searchFilm" placeholder="Rechercher un film..." class="search-input">
    </div>
    
    <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Affiche</th>
                    <th>Titre</th>
                    <th>Durée</th>
                    <th>Date de sortie</th>
                    <th>Réalisateur</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="filmsTableBody">
                <?php if(empty($films)): ?>
                    <tr>
                        <td colspan="7" class="text-center">Aucun film trouvé</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($films as $film): ?>
                        <tr>
                            <td><?= $film['idFilm'] ?></td>
                            <td>
                                <?php if(!empty($film['urlAffiche'])): ?>
                                    <img src="<?= IMAGES_URL ?>films/<?= $film['urlAffiche'] ?>" alt="<?= $film['titre'] ?>" class="admin-thumbnail">
                                <?php else: ?>
                                    <div class="no-image">Pas d'image</div>
                                <?php endif; ?>
                            </td>
                            <td><?= $film['titre'] ?></td>
                            <td><?= $film['duree'] ?> min</td>
                            <td><?= date('d/m/Y', strtotime($film['dateSortie'])) ?></td>
                            <td><?= $film['nomRealisateur'] . ' ' . $film['prenomRealisateur'] ?></td>
                            <td class="actions">
                                <a href="<?= URL ?>index.php?page=films/show&id=<?= $film['idFilm'] ?>" class="btn-view" title="Voir"><i class="fas fa-eye"></i></a>
                                <a href="<?= URL ?>admin/editFilm/<?= $film['idFilm'] ?>" class="btn-edit" title="Modifier"><i class="fas fa-edit"></i></a>
                                <a href="#" class="btn-delete" data-id="<?= $film['idFilm'] ?>" data-title="<?= $film['titre'] ?>" title="Supprimer"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Confirmation de suppression</h2>
        <p>Êtes-vous sûr de vouloir supprimer le film <span id="filmTitle"></span> ?</p>
        <p class="warning">Cette action est irréversible et supprimera également tous les avis associés à ce film.</p>
        <div class="modal-buttons">
            <button id="cancelDelete" class="btn-cancel">Annuler</button>
            <form id="deleteForm" method="POST" style="display:inline;">
                <input type="hidden" id="filmIdInput" name="idFilm" value="">
                <button type="submit" class="btn-confirm">Supprimer</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Recherche de films
        const searchInput = document.getElementById('searchFilm');
        const tableBody = document.getElementById('filmsTableBody');
        const rows = tableBody.querySelectorAll('tr');
        
        searchInput.addEventListener('keyup', function() {
            const searchTerm = searchInput.value.toLowerCase();
            
            rows.forEach(row => {
                const title = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const director = row.querySelector('td:nth-child(6)').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || director.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Modal de suppression
        const modal = document.getElementById('deleteModal');
        const deleteButtons = document.querySelectorAll('.btn-delete');
        const closeBtn = document.querySelector('.close');
        const cancelBtn = document.getElementById('cancelDelete');
        const deleteForm = document.getElementById('deleteForm');
        const filmTitleSpan = document.getElementById('filmTitle');
        const filmIdInput = document.getElementById('filmIdInput');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const filmId = this.getAttribute('data-id');
                const filmTitle = this.getAttribute('data-title');
                
                filmTitleSpan.textContent = filmTitle;
                filmIdInput.value = filmId;
                deleteForm.action = '<?= URL ?>admin/deleteFilm';
                modal.style.display = 'block';
            });
        });
        
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
        
        cancelBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
        
        window.addEventListener('click', function(e) {
            if (e.target == modal) {
                modal.style.display = 'none';
            }
        });
    });
</script>

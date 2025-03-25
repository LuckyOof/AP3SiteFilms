<?php
/**
 * Vue de la liste des genres pour l'administration
 */
?>

<div class="admin-container">
    <div class="admin-header">
        <h1><i class="fas fa-tags"></i> Administration des genres</h1>
        <a href="<?= URL ?>admin/addGenre" class="btn btn-add"><i class="fas fa-plus"></i> Ajouter un genre</a>
    </div>
    
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <p><?= $_SESSION['success'] ?></p>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['errors'])): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach($_SESSION['errors'] as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>
    
    <div class="admin-search">
        <input type="text" class="search-input" placeholder="Rechercher un genre..." data-target=".admin-table">
    </div>
    
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th class="sortable">Libellé</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($genres)): ?>
                    <?php foreach($genres as $genre): ?>
                        <tr>
                            <td><?= htmlspecialchars($genre['libelle']) ?></td>
                            <td class="actions">
                                <a href="<?= URL ?>admin/editGenre/<?= $genre['idGenre'] ?>" class="btn-action btn-edit" title="Modifier"><i class="fas fa-edit"></i></a>
                                <a href="#" class="btn-action btn-delete" data-id="<?= $genre['idGenre'] ?>" data-name="<?= htmlspecialchars($genre['libelle']) ?>" title="Supprimer"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2" class="text-center">Aucun genre trouvé</td>
                    </tr>
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
        <p>Êtes-vous sûr de vouloir supprimer le genre <span id="genreName"></span> ?</p>
        <div class="modal-actions">
            <form id="deleteForm" method="post" action="<?= URL ?>admin/deleteGenre">
                <input type="hidden" name="idGenre" value="">
                <button type="button" class="btn btn-cancel">Annuler</button>
                <button type="submit" class="btn btn-danger">Supprimer</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion de la suppression
        const deleteButtons = document.querySelectorAll('.btn-delete');
        const deleteModal = document.getElementById('deleteModal');
        const closeModal = document.querySelector('.close');
        const cancelButton = document.querySelector('.btn-cancel');
        const deleteForm = document.getElementById('deleteForm');
        const genreName = document.getElementById('genreName');
        const idInput = deleteForm.querySelector('input[name="idGenre"]');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                
                genreName.textContent = name;
                idInput.value = id;
                deleteModal.style.display = 'block';
            });
        });
        
        closeModal.addEventListener('click', function() {
            deleteModal.style.display = 'none';
        });
        
        cancelButton.addEventListener('click', function() {
            deleteModal.style.display = 'none';
        });
        
        window.addEventListener('click', function(e) {
            if (e.target == deleteModal) {
                deleteModal.style.display = 'none';
            }
        });
    });
</script>

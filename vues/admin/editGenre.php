<?php
/**
 * Vue du formulaire de modification de genre
 */
?>

<div class="admin-container">
    <div class="admin-header">
        <h1><i class="fas fa-tag"></i> Modifier un genre</h1>
        <a href="<?= URL ?>admin/genres" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour à la liste</a>
    </div>
    
    <?php if(isset($_SESSION['errors'])): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach($_SESSION['errors'] as $error): ?>
                    <li><?= utf8_encode($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>
    
    <div class="form-container">
        <form action="<?= URL ?>admin/updateGenre" method="post" class="admin-form">
            <input type="hidden" name="idGenre" value="<?= $genre['idGenre'] ?>">
            
            <div class="form-group">
                <label for="libelle">Libellé du genre *</label>
                <input type="text" id="libelle" name="libelle" class="form-control" required
                       value="<?= isset($_SESSION['form_data']['libelle']) ? utf8_encode(htmlspecialchars($_SESSION['form_data']['libelle'])) : utf8_encode(htmlspecialchars($genre['libelle'])) ?>">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="<?= URL ?>admin/genres" class="btn btn-cancel">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php 
// Nettoyer les données de formulaire en session
if(isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
?>

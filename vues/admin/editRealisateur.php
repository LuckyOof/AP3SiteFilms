<?php
/**
 * Vue du formulaire de modification de réalisateur
 */
?>

<div class="admin-container">
    <div class="admin-header">
        <h1><i class="fas fa-video"></i> Modifier un réalisateur</h1>
        <a href="<?= URL ?>admin/realisateurs" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour à la liste</a>
    </div>
    
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
    
    <div class="form-container">
        <form action="<?= URL ?>admin/updateRealisateur" method="post" class="admin-form">
            <input type="hidden" name="idReal" value="<?= $realisateur['idReal'] ?>">
            
            <div class="form-group">
                <label for="nom">Nom *</label>
                <input type="text" id="nom" name="nom" required value="<?= isset($_SESSION['form_data']['nom']) ? htmlspecialchars($_SESSION['form_data']['nom']) : htmlspecialchars($realisateur['nom']) ?>">
            </div>
            
            <div class="form-group">
                <label for="prenom">Prénom *</label>
                <input type="text" id="prenom" name="prenom" required value="<?= isset($_SESSION['form_data']['prenom']) ? htmlspecialchars($_SESSION['form_data']['prenom']) : htmlspecialchars($realisateur['prenom']) ?>">
            </div>
            
            <div class="form-group">
                <label for="dateNaissance">Date de naissance *</label>
                <input type="date" id="dateNaissance" name="dateNaissance" required value="<?= isset($_SESSION['form_data']['dateNaissance']) ? $_SESSION['form_data']['dateNaissance'] : $realisateur['dateNaissance'] ?>">
            </div>
            
            <div class="form-group">
                <label for="nationalite">Nationalité *</label>
                <input type="text" id="nationalite" name="nationalite" required value="<?= isset($_SESSION['form_data']['nationalite']) ? htmlspecialchars($_SESSION['form_data']['nationalite']) : htmlspecialchars($realisateur['nationalite']) ?>">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Mettre à jour</button>
                <a href="<?= URL ?>admin/realisateurs" class="btn btn-cancel">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php
// Nettoyer les données du formulaire après affichage
if(isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
?>

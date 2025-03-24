<?php
// Vérifier s'il y a un message de succès ou d'erreur à afficher
$message = isset($message) ? $message : null;
$messageClass = isset($messageClass) ? $messageClass : null;
$realisateurs = isset($realisateurs) ? $realisateurs : array();
$genres = isset($genres) ? $genres : array();
$acteurs = isset($acteurs) ? $acteurs : array();
?>

<div class="add-film-container">
    <h2><i class="fas fa-plus-circle"></i> Ajouter un film</h2>

    <?php if ($message): ?>
        <div class="alert <?php echo $messageClass; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form action="<?= URL ?>addFilm" method="POST" class="add-film-form" enctype="multipart/form-data">
        <div class="add-film-section">
            <div class="form-group">
                <label for="titre">Titre <span class="required">*</span></label>
                <input type="text" id="titre" name="titre" required>
            </div>

            <div class="form-group">
                <label for="descri">Description <span class="required">*</span></label>
                <textarea id="descri" name="descri" required></textarea>
            </div>

            <div class="form-group">
                <label for="duree">Durée (minutes) <span class="required">*</span></label>
                <input type="number" id="duree" name="duree" min="1" required>
            </div>

            <div class="form-group">
                <label for="dateSortie">Date de sortie <span class="required">*</span></label>
                <input type="date" id="dateSortie" name="dateSortie" required>
            </div>

            <div class="form-group">
                <label for="budget">Budget (en $)</label>
                <input type="number" id="budget" name="budget" min="0" step="0.01" value="0">
            </div>

            <div class="form-group">
                <label for="recette">Recette (en $)</label>
                <input type="number" id="recette" name="recette" min="0" step="0.01" value="0">
            </div>

            <div class="form-group">
                <label for="image">Affiche du film</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>

            <div class="form-group">
                <label for="idReal">Réalisateurs <span class="required">*</span></label>
                <select id="idReal" name="idReal" required>
                    <option value="">Sélectionnez un réalisateur</option>
                    <?php foreach ($realisateurs as $realisateur): ?>
                        <option value="<?= $realisateur['idReal'] ?>"><?= $realisateur['prenom'] . ' ' . $realisateur['nom'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="bandeAnnonce">URL de la bande-annonce</label>
                <input type="url" id="bandeAnnonce" name="bandeAnnonce" placeholder="https://www.youtube.com/watch?v=...">
            </div>

            <div class="form-group">
                <label for="langueVO">Langue originale</label>
                <input type="text" id="langueVO" name="langueVO">
            </div>

            <div class="form-group">
                <label>Genres</label>
                <div class="genres-container">
                    <?php foreach ($genres as $genre): ?>
                    <div class="genre-checkbox">
                        <input type="checkbox" id="genre<?= $genre['idGenre'] ?>" name="genres[]" value="<?= $genre['idGenre'] ?>">
                        <label for="genre<?= $genre['idGenre'] ?>"><?= $genre['libelle'] ?></label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label>Acteurs</label>
                <div class="acteurs-container">
                    <?php foreach ($acteurs as $acteur): ?>
                    <div class="acteur-checkbox">
                        <input type="checkbox" id="acteur<?= $acteur['idActeur'] ?>" name="acteurs[]" value="<?= $acteur['idActeur'] ?>">
                        <label for="acteur<?= $acteur['idActeur'] ?>"><?= $acteur['prenom'] . ' ' . $acteur['nom'] ?></label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= URL ?>admin/films" class="btn btn-secondary">Retour à la liste</a>
            <button type="submit" class="btn btn-primary">Ajouter le film</button>
        </div>
    </form>
</div>

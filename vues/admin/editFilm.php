<div class="admin-container">
    <div class="admin-header">
        <h1><i class="fas fa-edit"></i> Modifier le film : <?= htmlspecialchars($film['titre']) ?></h1>
        <a href="<?= URL ?>admin/films" class="btn-back"><i class="fas fa-arrow-left"></i> Retour à la liste</a>
    </div>
    
    <?php if(isset($_SESSION['errors'])): ?>
        <div class="alert alert-danger">
            <?php foreach($_SESSION['errors'] as $error): ?>
                <p><?= $error ?></p>
            <?php endforeach; ?>
            <?php unset($_SESSION['errors']); ?>
        </div>
    <?php endif; ?>
    
    <div class="admin-form-container">
        <form action="<?= URL ?>admin/updateFilm" method="post" enctype="multipart/form-data" class="admin-form">
            <input type="hidden" name="idFilm" value="<?= $film['idFilm'] ?>">
            
            <div class="form-group">
                <label for="titre">Titre *</label>
                <input type="text" id="titre" name="titre" required value="<?= isset($_SESSION['form_data']['titre']) ? htmlspecialchars($_SESSION['form_data']['titre']) : htmlspecialchars($film['titre']) ?>">
            </div>
            
            <div class="form-group">
                <label for="description">Description *</label>
                <textarea id="description" name="description" rows="5" required><?= isset($_SESSION['form_data']['description']) ? htmlspecialchars($_SESSION['form_data']['description']) : (isset($film['descri']) ? htmlspecialchars($film['descri']) : '') ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group half">
                    <label for="duree">Durée (minutes) *</label>
                    <input type="number" id="duree" name="duree" min="1" required value="<?= isset($_SESSION['form_data']['duree']) ? htmlspecialchars($_SESSION['form_data']['duree']) : htmlspecialchars($film['duree']) ?>">
                </div>
                
                <div class="form-group half">
                    <label for="dateSortie">Date de sortie *</label>
                    <input type="date" id="dateSortie" name="dateSortie" required value="<?= isset($_SESSION['form_data']['dateSortie']) ? htmlspecialchars($_SESSION['form_data']['dateSortie']) : htmlspecialchars($film['dateSortie']) ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group half">
                    <label for="coutTotal">Coût total (en $)</label>
                    <input type="number" id="coutTotal" name="coutTotal" min="0" step="0.01" value="<?= isset($_SESSION['form_data']['coutTotal']) ? htmlspecialchars($_SESSION['form_data']['coutTotal']) : (isset($film['coutTotal']) ? htmlspecialchars($film['coutTotal']) : '0') ?>">
                </div>
                
                <div class="form-group half">
                    <label for="boxOffice">Box Office (en $)</label>
                    <input type="number" id="boxOffice" name="boxOffice" min="0" step="0.01" value="<?= isset($_SESSION['form_data']['boxOffice']) ? htmlspecialchars($_SESSION['form_data']['boxOffice']) : (isset($film['boxOffice']) ? htmlspecialchars($film['boxOffice']) : '0') ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="affiche">Affiche du film</label>
                <?php if(!empty($film['urlAffiche'])): ?>
                <div class="current-image">
                    <p>Image actuelle :</p>
                    <img src="<?= URL . 'ressources/images/films/' . $film['urlAffiche'] ?>" alt="<?= $film['titre'] ?>" class="thumbnail">
                </div>
                <?php endif; ?>
                <div class="file-upload">
                    <input type="file" id="affiche" name="affiche" accept="image/*">
                    <span id="fileName">Aucun fichier choisi</span>
                </div>
                <small>Formats acceptés : JPG, JPEG, PNG, GIF</small>
                <small>Laissez vide pour conserver l'image actuelle</small>
            </div>
            
            <div class="form-group">
                <label for="idReal">Réalisateur *</label>
                <select id="idReal" name="idReal" required>
                    <option value="">Sélectionnez un réalisateur</option>
                    <?php foreach($realisateurs as $realisateur): ?>
                        <option value="<?= $realisateur['idReal'] ?>" <?= (isset($_SESSION['form_data']['idReal']) && $_SESSION['form_data']['idReal'] == $realisateur['idReal']) || (!isset($_SESSION['form_data']) && $film['idReal'] == $realisateur['idReal']) ? 'selected' : '' ?>>
                            <?= $realisateur['nom'] . ' ' . $realisateur['prenom'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="genres">Genres *</label>
                <div class="checkbox-group">
                    <?php 
                    // Préparer un tableau d'IDs de genres pour faciliter la vérification
                    $selectedGenres = [];
                    if (isset($_SESSION['form_data']['genres'])) {
                        $selectedGenres = $_SESSION['form_data']['genres'];
                    } elseif (isset($filmGenres)) {
                        foreach ($filmGenres as $genre) {
                            $selectedGenres[] = $genre['idGenre'];
                        }
                    }
                    ?>
                    
                    <?php foreach($genres as $genre): ?>
                        <div class="genre-item" data-id="<?= $genre['idGenre'] ?>">
                            <input type="checkbox" id="genre_<?= $genre['idGenre'] ?>" name="genres[]" value="<?= $genre['idGenre'] ?>" <?= in_array($genre['idGenre'], $selectedGenres) ? 'checked' : '' ?> class="hidden-checkbox">
                            <span class="genre-name"><?= $genre['libelle'] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="acteurs">Acteurs principaux</label>
                <?php 
                // Préparer un tableau d'IDs d'acteurs pour faciliter la vérification
                $selectedActors = [];
                if (isset($_SESSION['form_data']['acteurs'])) {
                    $selectedActors = $_SESSION['form_data']['acteurs'];
                } elseif (isset($filmActeurs)) {
                    foreach ($filmActeurs as $acteur) {
                        $selectedActors[] = $acteur['idActeur'];
                    }
                }
                ?>
                <div class="checkbox-group">
                    <?php foreach($acteurs as $acteur): ?>
                        <div class="actor-item" data-id="<?= $acteur['idActeur'] ?>">
                            <input type="checkbox" id="acteur-<?= $acteur['idActeur'] ?>" name="acteurs[]" value="<?= $acteur['idActeur'] ?>" <?= in_array($acteur['idActeur'], $selectedActors) ? 'checked' : '' ?> class="hidden-checkbox">
                            <span class="actor-name"><?= $acteur['nom'] . ' ' . $acteur['prenom'] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="urlBandeAnnonce">URL de la bande-annonce (YouTube)</label>
                <input type="url" id="urlBandeAnnonce" name="urlBandeAnnonce" placeholder="https://www.youtube.com/watch?v=..." value="<?= isset($_SESSION['form_data']['urlBandeAnnonce']) ? htmlspecialchars($_SESSION['form_data']['urlBandeAnnonce']) : (isset($film['trailer']) ? htmlspecialchars($film['trailer']) : '') ?>">
            </div>
            
            <div class="form-group">
                <label for="langueOriginale">Langue originale</label>
                <input type="text" id="langueOriginale" name="langueOriginale" value="<?= isset($_SESSION['form_data']['langueOriginale']) ? htmlspecialchars($_SESSION['form_data']['langueOriginale']) : (isset($film['langueVO']) ? htmlspecialchars($film['langueVO']) : '') ?>">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit">Mettre à jour le film</button>
                <a href="<?= URL ?>admin/films" class="btn-cancel">Annuler</a>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Affichage du nom du fichier sélectionné
        document.getElementById('affiche').addEventListener('change', function(event) {
            var fileName = event.target.files[0] ? event.target.files[0].name : 'Aucun fichier choisi';
            document.getElementById('fileName').textContent = fileName;
        });

        // Gestion des acteurs cliquables
        const actorItems = document.querySelectorAll('.actor-item');
        actorItems.forEach(item => {
            item.addEventListener('click', function() {
                const checkbox = this.querySelector('input[type="checkbox"]');
                checkbox.checked = !checkbox.checked;
                this.classList.toggle('selected', checkbox.checked);
            });
        });
        
        // Initialiser l'état des acteurs sélectionnés
        actorItems.forEach(item => {
            const checkbox = item.querySelector('input[type="checkbox"]');
            if (checkbox.checked) {
                item.classList.add('selected');
            }
        });

        // Gestion des genres cliquables
        const genreItems = document.querySelectorAll('.genre-item');
        genreItems.forEach(item => {
            item.addEventListener('click', function() {
                const checkbox = this.querySelector('input[type="checkbox"]');
                checkbox.checked = !checkbox.checked;
                this.classList.toggle('selected', checkbox.checked);
            });
        });
        
        // Initialiser l'état des genres sélectionnés
        genreItems.forEach(item => {
            const checkbox = item.querySelector('input[type="checkbox"]');
            if (checkbox.checked) {
                item.classList.add('selected');
            }
        });

        // Prévisualisation de l'image
        const imageInput = document.getElementById('affiche');
        
        // Supprimer toute prévisualisation existante pour éviter les doublons
        const existingPreviews = document.querySelectorAll('.image-preview');
        existingPreviews.forEach(preview => preview.remove());
        
        // Créer un nouveau conteneur de prévisualisation
        const previewContainer = document.createElement('div');
        previewContainer.className = 'image-preview';
        previewContainer.style.display = 'none';
        previewContainer.innerHTML = '<img id="imagePreview" src="#" alt="Prévisualisation" style="max-width: 300px; margin-top: 10px;" />';
        
        // Insérer le conteneur après l'input
        if (imageInput.nextSibling) {
            imageInput.parentNode.insertBefore(previewContainer, imageInput.nextSibling);
        } else {
            imageInput.parentNode.appendChild(previewContainer);
        }
        
        const imagePreview = document.getElementById('imagePreview');
        
        imageInput.addEventListener('change', function() {
            // Supprimer toute prévisualisation existante à chaque changement
            document.querySelectorAll('.image-preview').forEach(preview => {
                if (preview !== previewContainer) {
                    preview.remove();
                }
            });
            
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                
                reader.readAsDataURL(this.files[0]);
            } else {
                previewContainer.style.display = 'none';
            }
        });
        
        // Validation du formulaire
        document.querySelector('form').addEventListener('submit', function(e) {
            let valid = true;
            
            const titre = document.getElementById('titre').value.trim();
            const description = document.getElementById('description').value.trim();
            const duree = document.getElementById('duree').value;
            const dateSortie = document.getElementById('dateSortie').value;
            const idReal = document.getElementById('idReal').value;
            const genres = document.querySelectorAll('input[name="genres[]"]:checked');
            
            if (titre === '') {
                valid = false;
                alert('Le titre est obligatoire');
            } else if (description === '') {
                valid = false;
                alert('La description est obligatoire');
            } else if (duree <= 0) {
                valid = false;
                alert('La durée doit être supérieure à 0');
            } else if (dateSortie === '') {
                valid = false;
                alert('La date de sortie est obligatoire');
            } else if (idReal === '') {
                valid = false;
                alert('Le réalisateur est obligatoire');
            } else if (genres.length === 0) {
                valid = false;
                alert('Veuillez sélectionner au moins un genre');
            }
            
            if (!valid) {
                e.preventDefault();
            }
        });
    });
</script>

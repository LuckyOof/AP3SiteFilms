<div class="container mt-4">
    <h1>Ajouter un acteur</h1>

    <?php if (isset($message)): ?>
        <div class="alert alert-<?= $messageClass === 'error' ? 'danger' : 'success' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <form action="<?= URL ?>admin/acteurs/add" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="nom" class="form-label">Nom *</label>
            <input type="text" class="form-control" id="nom" name="nom" required>
            <div class="invalid-feedback">
                Le nom est obligatoire
            </div>
        </div>

        <div class="mb-3">
            <label for="prenom" class="form-label">Prénom *</label>
            <input type="text" class="form-control" id="prenom" name="prenom" required>
            <div class="invalid-feedback">
                Le prénom est obligatoire
            </div>
        </div>

        <div class="mb-3">
            <label for="dateNaissance" class="form-label">Date de naissance</label>
            <input type="date" class="form-control" id="dateNaissance" name="dateNaissance">
        </div>

        <div class="mb-3">
            <label for="nationalite" class="form-label">Nationalité</label>
            <input type="text" class="form-control" id="nationalite" name="nationalite">
        </div>

        <div class="mb-3">
            <label for="photo" class="form-label">Photo</label>
            <input type="file" class="form-control" id="photo" name="photo" accept="image/jpeg,image/png">
            <div class="form-text">Formats acceptés : JPG, JPEG, PNG</div>
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Ajouter</button>
            <a href="<?= URL ?>admin/acteurs" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<script>
// Script de validation du formulaire
(function () {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        })
})()
</script>

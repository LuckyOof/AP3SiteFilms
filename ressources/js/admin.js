/**
 * Script JavaScript pour l'administration
 */

document.addEventListener('DOMContentLoaded', function() {
    // Gestion des alertes
    const alerts = document.querySelectorAll('.alert');
    if (alerts.length > 0) {
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            }, 5000);
        });
    }
    
    // Initialisation des sélections multiples
    const multiSelects = document.querySelectorAll('select[multiple]');
    if (multiSelects.length > 0) {
        multiSelects.forEach(select => {
            // Ici, on pourrait ajouter une bibliothèque de sélection multiple améliorée
            // comme Select2 ou Choices.js si nécessaire
        });
    }
    
    // Validation côté client des formulaires
    const adminForms = document.querySelectorAll('.admin-form');
    if (adminForms.length > 0) {
        adminForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Validation des champs requis
                const requiredFields = form.querySelectorAll('[required]');
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('error');
                    } else {
                        field.classList.remove('error');
                    }
                });
                
                // Validation des genres (au moins un doit être sélectionné)
                const genreCheckboxes = form.querySelectorAll('input[name="genres[]"]');
                if (genreCheckboxes.length > 0) {
                    let genreSelected = false;
                    genreCheckboxes.forEach(checkbox => {
                        if (checkbox.checked) {
                            genreSelected = true;
                        }
                    });
                    
                    if (!genreSelected) {
                        isValid = false;
                        const genreContainer = document.querySelector('.checkbox-group');
                        if (genreContainer) {
                            genreContainer.classList.add('error');
                        }
                    } else {
                        const genreContainer = document.querySelector('.checkbox-group');
                        if (genreContainer) {
                            genreContainer.classList.remove('error');
                        }
                    }
                }
                
                if (!isValid) {
                    e.preventDefault();
                    // Afficher un message d'erreur
                    const errorMessage = document.createElement('div');
                    errorMessage.className = 'alert alert-danger';
                    errorMessage.innerHTML = '<p>Veuillez remplir tous les champs obligatoires.</p>';
                    
                    const existingError = form.querySelector('.alert.alert-danger');
                    if (existingError) {
                        existingError.remove();
                    }
                    
                    form.insertBefore(errorMessage, form.firstChild);
                    
                    // Scroll vers le haut du formulaire
                    window.scrollTo({
                        top: form.offsetTop - 20,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }
    
    // Prévisualisation d'image
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    if (imageInputs.length > 0) {
        imageInputs.forEach(input => {
            const previewContainer = document.createElement('div');
            previewContainer.className = 'image-preview';
            previewContainer.style.display = 'none';
            
            const previewImage = document.createElement('img');
            previewImage.alt = 'Prévisualisation';
            previewContainer.appendChild(previewImage);
            
            input.parentNode.insertBefore(previewContainer, input.nextSibling);
            
            input.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewContainer.style.display = 'block';
                    }
                    
                    reader.readAsDataURL(this.files[0]);
                } else {
                    previewContainer.style.display = 'none';
                }
            });
        });
    }
    
    // Gestion des modales de confirmation
    const modals = document.querySelectorAll('.modal');
    if (modals.length > 0) {
        modals.forEach(modal => {
            const closeBtn = modal.querySelector('.close');
            const cancelBtn = modal.querySelector('.btn-cancel');
            
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    modal.style.display = 'none';
                });
            }
            
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    modal.style.display = 'none';
                });
            }
            
            // Fermer la modale en cliquant en dehors
            window.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
    }
    
    // Gestion des boutons de suppression
    const deleteButtons = document.querySelectorAll('.btn-delete');
    if (deleteButtons.length > 0) {
        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            const confirmBtn = deleteModal.querySelector('#confirmDelete');
            const filmTitleSpan = deleteModal.querySelector('#filmTitle');
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const filmId = this.getAttribute('data-id');
                    const filmTitle = this.getAttribute('data-title');
                    
                    if (filmTitleSpan) {
                        filmTitleSpan.textContent = filmTitle;
                    }
                    
                    if (confirmBtn) {
                        confirmBtn.href = `${window.URL}admin/deleteFilm/${filmId}`;
                    }
                    
                    deleteModal.style.display = 'block';
                });
            });
        }
    }
    
    // Filtrage du tableau
    const searchInput = document.getElementById('searchFilm');
    if (searchInput) {
        const tableBody = document.getElementById('filmsTableBody');
        if (tableBody) {
            const rows = tableBody.querySelectorAll('tr');
            
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                
                rows.forEach(row => {
                    let found = false;
                    const cells = row.querySelectorAll('td');
                    
                    cells.forEach(cell => {
                        if (cell.textContent.toLowerCase().includes(searchTerm)) {
                            found = true;
                        }
                    });
                    
                    if (found) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    }
});

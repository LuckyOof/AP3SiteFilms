document.addEventListener('DOMContentLoaded', function() {
    // Références aux éléments du DOM
    const avisForm = document.getElementById('avisForm');
    const avisStars = document.querySelectorAll('.avis-star');
    const avisNote = document.getElementById('avisNote');
    const deleteAvisButtons = document.querySelectorAll('.delete-avis');
    
    // Initialiser la note sélectionnée
    let selectedRating = 0;
    
    // Gérer la sélection des étoiles
    if (avisStars.length > 0) {
        avisStars.forEach(star => {
            // Afficher les étoiles au survol
            star.addEventListener('mouseover', function() {
                const rating = parseInt(this.getAttribute('data-rating'));
                highlightStars(rating);
            });
            
            // Rétablir les étoiles sélectionnées lorsque la souris quitte
            star.addEventListener('mouseout', function() {
                highlightStars(selectedRating);
            });
            
            // Sélectionner une note
            star.addEventListener('click', function() {
                selectedRating = parseInt(this.getAttribute('data-rating'));
                avisNote.value = selectedRating;
                highlightStars(selectedRating);
            });
        });
    }
    
    // Fonction pour mettre en surbrillance les étoiles
    function highlightStars(rating) {
        avisStars.forEach(star => {
            const starRating = parseInt(star.getAttribute('data-rating'));
            if (starRating <= rating) {
                star.classList.add('selected');
            } else {
                star.classList.remove('selected');
            }
        });
    }
    
    // Gérer la soumission du formulaire d'avis
    if (avisForm) {
        avisForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Vérifier que la note est valide
            if (avisNote.value < 1 || avisNote.value > 5) {
                showMessage('Veuillez sélectionner une note entre 1 et 5 étoiles', 'error');
                return;
            }
            
            // Préparer les données du formulaire
            const formData = new FormData(avisForm);
            
            // Envoyer la requête AJAX
            fetch(avisForm.getAttribute('action'), {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // Vérifier si la réponse est OK avant de parser le JSON
                if (!response.ok) {
                    throw new Error('Erreur serveur: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Déterminer si c'était une création ou une modification
                    const isEdit = avisForm.querySelector('input[name="numAvis"]')?.value;
                    const message = isEdit ? 'Votre avis a été modifié avec succès' : data.message;
                    
                    showMessage(message, 'success');
                    
                    // Mettre à jour l'affichage des avis
                    updateAvisDisplay(data.avis, data.moyenne);
                    
                    // Réinitialiser le formulaire
                    const commentaireTextarea = avisForm.querySelector('textarea[name="commentaire"]');
                    if (commentaireTextarea) {
                        commentaireTextarea.value = '';
                    }
                    
                    // Réinitialiser la note
                    selectedRating = 0;
                    avisNote.value = '0';
                    highlightStars(0);
                    
                    // Réinitialiser le formulaire complet
                    avisForm.reset();
                    
                    // Si on était en mode édition, restaurer l'état initial du formulaire
                    if (isEdit) {
                        // Supprimer le champ numAvis
                        const numAvisInput = avisForm.querySelector('input[name="numAvis"]');
                        if (numAvisInput) {
                            numAvisInput.value = '';
                        }
                        
                        // Restaurer le texte du bouton
                        const submitButton = avisForm.querySelector('button[type="submit"]');
                        if (submitButton) {
                            submitButton.textContent = 'Envoyer mon avis';
                            submitButton.classList.remove('edit-mode');
                        }
                        
                        // Supprimer le bouton d'annulation
                        const cancelButton = avisForm.querySelector('.cancel-edit');
                        if (cancelButton) {
                            cancelButton.remove();
                        }
                    }
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showMessage('Une erreur est survenue lors de l\'envoi de votre avis', 'error');
            });
        });
    }
    
    // Gérer la suppression des avis
    if (deleteAvisButtons.length > 0) {
        deleteAvisButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (confirm('Voulez-vous vraiment supprimer cet avis ?')) {
                    const numAvis = this.getAttribute('data-avis-id');
                    const idFilm = this.getAttribute('data-film-id');
                    
                    // Préparer les données
                    const formData = new FormData();
                    formData.append('numAvis', numAvis);
                    formData.append('idFilm', idFilm);
                    
                    // Envoyer la requête AJAX
                    fetch(`${URL}index.php?page=films/supprimerAvis`, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        // Vérifier si la réponse est OK avant de parser le JSON
                        if (!response.ok) {
                            throw new Error('Erreur serveur: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showMessage(data.message, 'success');
                            // Mettre à jour l'affichage des avis
                            updateAvisDisplay(data.avis, data.moyenne);
                        } else {
                            showMessage(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        showMessage('Une erreur est survenue lors de la suppression de votre avis', 'error');
                    });
                }
            });
        });
    }
    
    // Fonction pour afficher un message
    function showMessage(message, type) {
        const messageContainer = document.getElementById('messageContainer');
        if (messageContainer) {
            messageContainer.textContent = message;
            messageContainer.className = `message ${type}`;
            messageContainer.style.display = 'block';
            
            // Masquer le message après 3 secondes
            setTimeout(() => {
                messageContainer.style.display = 'none';
            }, 3000);
        } else {
            alert(message);
        }
    }
    
    // Fonction pour mettre à jour l'affichage des avis
    function updateAvisDisplay(avis, moyenne) {
        // Mettre à jour la note moyenne
        const moyenneElement = document.querySelector('.movie-rating-average');
        if (moyenneElement) {
            moyenneElement.textContent = moyenne ? parseFloat(moyenne).toFixed(1) : '0';
        }
        
        // Mettre à jour la liste des avis
        const avisContainer = document.querySelector('.avis-list');
        if (avisContainer) {
            // Vider le conteneur
            avisContainer.innerHTML = '';
            
            if (avis && avis.length > 0) {
                // Ajouter chaque avis
                avis.forEach(item => {
                    const avisElement = document.createElement('div');
                    avisElement.className = 'avis-item';
                    
                    // Vérifier si c'est l'avis de l'utilisateur connecté
                    const isUserAvis = item.idUtilisateur == document.querySelector('meta[name="user-id"]')?.getAttribute('content');
                    
                    // Créer le contenu HTML de l'avis
                    avisElement.innerHTML = `
                        <div class="avis-header">
                            <div class="avis-user">${item.pseudo}</div>
                            <div class="avis-date">${new Date(item.datePublication).toLocaleDateString()}</div>
                            <div class="avis-rating">
                                ${generateStarsHTML(parseFloat(item.note))}
                            </div>
                            ${isUserAvis ? `
                                <button class="edit-avis" data-avis-id="${item.numAvis}" data-film-id="${item.idFilm}" data-note="${item.note}" data-commentaire="${item.commentaire.replace(/"/g, '&quot;')}"><i class="fas fa-edit"></i></button>
                                <button class="delete-avis" data-avis-id="${item.numAvis}" data-film-id="${item.idFilm}"><i class="fas fa-trash"></i></button>
                            ` : ''}
                        </div>
                        <div class="avis-content">${item.commentaire}</div>
                    `;
                    
                    avisContainer.appendChild(avisElement);
                });
                
                // Réinitialiser les écouteurs d'événements pour les boutons de suppression
                const newDeleteButtons = document.querySelectorAll('.delete-avis');
                newDeleteButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        if (confirm('Voulez-vous vraiment supprimer cet avis ?')) {
                            const numAvis = this.getAttribute('data-avis-id');
                            const idFilm = this.getAttribute('data-film-id');
                            
                            // Préparer les données
                            const formData = new FormData();
                            formData.append('numAvis', numAvis);
                            formData.append('idFilm', idFilm);
                            
                            // Envoyer la requête AJAX
                            fetch(`${URL}index.php?page=films/supprimerAvis`, {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => {
                                // Vérifier si la réponse est OK avant de parser le JSON
                                if (!response.ok) {
                                    throw new Error('Erreur serveur: ' + response.status);
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    showMessage(data.message, 'success');
                                    // Mettre à jour l'affichage des avis
                                    updateAvisDisplay(data.avis, data.moyenne);
                                } else {
                                    showMessage(data.message, 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Erreur:', error);
                                showMessage('Une erreur est survenue lors de la suppression de votre avis', 'error');
                            });
                        }
                    });
                });
                
                // Réinitialiser les écouteurs d'événements pour les boutons d'édition
                const newEditButtons = document.querySelectorAll('.edit-avis');
                newEditButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        // Récupérer les données de l'avis
                        const numAvis = this.getAttribute('data-avis-id');
                        const idFilm = this.getAttribute('data-film-id');
                        const note = parseFloat(this.getAttribute('data-note'));
                        const commentaire = this.getAttribute('data-commentaire');
                        
                        // Mettre à jour le formulaire avec les données de l'avis
                        if (avisForm) {
                            // Ajouter un champ caché pour l'ID de l'avis
                            let numAvisInput = avisForm.querySelector('input[name="numAvis"]');
                            if (!numAvisInput) {
                                numAvisInput = document.createElement('input');
                                numAvisInput.type = 'hidden';
                                numAvisInput.name = 'numAvis';
                                avisForm.appendChild(numAvisInput);
                            }
                            numAvisInput.value = numAvis;
                            
                            // Mettre à jour le commentaire
                            const commentaireTextarea = avisForm.querySelector('textarea[name="commentaire"]');
                            if (commentaireTextarea) {
                                commentaireTextarea.value = commentaire;
                            }
                            
                            // Mettre à jour la note
                            selectedRating = note;
                            avisNote.value = note;
                            highlightStars(note);
                            
                            // Changer le texte du bouton de soumission
                            const submitButton = avisForm.querySelector('button[type="submit"]');
                            if (submitButton) {
                                submitButton.textContent = 'Modifier mon avis';
                                // Ajouter une classe pour le style
                                submitButton.classList.add('edit-mode');
                            }
                            
                            // Faire défiler jusqu'au formulaire
                            avisForm.scrollIntoView({ behavior: 'smooth' });
                            
                            // Ajouter un bouton d'annulation
                            let cancelButton = avisForm.querySelector('.cancel-edit');
                            if (!cancelButton) {
                                cancelButton = document.createElement('button');
                                cancelButton.type = 'button';
                                cancelButton.className = 'cancel-edit';
                                cancelButton.textContent = 'Annuler';
                                submitButton.parentNode.insertBefore(cancelButton, submitButton.nextSibling);
                                
                                // Ajouter un écouteur d'événements pour l'annulation
                                cancelButton.addEventListener('click', function() {
                                    // Réinitialiser le formulaire
                                    avisForm.reset();
                                    selectedRating = 0;
                                    avisNote.value = '0';
                                    highlightStars(0);
                                    
                                    // Supprimer le champ numAvis
                                    if (numAvisInput) {
                                        numAvisInput.value = '';
                                    }
                                    
                                    // Restaurer le texte du bouton
                                    if (submitButton) {
                                        submitButton.textContent = 'Envoyer mon avis';
                                        submitButton.classList.remove('edit-mode');
                                    }
                                    
                                    // Supprimer le bouton d'annulation
                                    this.remove();
                                });
                            }
                        }
                    });
                });
            } else {
                // Aucun avis
                avisContainer.innerHTML = '<div class="no-avis">Aucun avis pour ce film</div>';
            }
        }
    }
    
    // Fonction pour générer les étoiles HTML
    function generateStarsHTML(note) {
        let starsHTML = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= note) {
                starsHTML += '<i class="fas fa-star"></i>';
            } else if (i - 0.5 <= note) {
                starsHTML += '<i class="fas fa-star-half-alt"></i>';
            } else {
                starsHTML += '<i class="far fa-star"></i>';
            }
        }
        return starsHTML;
    }
});

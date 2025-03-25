<?php
require_once MODELES_PATH . "films/filmModele.php";
require_once MODELES_PATH . "genres/genreModele.php";
require_once MODELES_PATH . "acteurs/acteurModele.php";
require_once MODELES_PATH . "realisateurs/realisateurModele.php";
require_once MODELES_PATH . "utilisateur/utilisateurModele.php";
require_once MODELES_PATH . "avis/avisModele.php";

class AdminController {
    private $filmModele;
    private $genreModele;
    private $acteurModele;
    private $realisateurModele;
    private $utilisateurModele;
    private $avisModele;

    public function __construct() {
        $this->filmModele = new FilmModele();
        $this->genreModele = new GenreModele();
        $this->acteurModele = new ActeurModele();
        $this->realisateurModele = new RealisateurModele();
        $this->utilisateurModele = new UtilisateurModele();
        $this->avisModele = new AvisModele();
    }
    
    /**
     * Vérifie si l'utilisateur est administrateur
     * @return bool
     */
    private function checkAdmin() {
        if (!isset($_SESSION['user']) || !$_SESSION['user']['estAdmin']) {
            $_SESSION['message'] = "Vous devez être administrateur pour accéder à cette section.";
            $_SESSION['message_type'] = "danger";
            header('Location: ' . URL . 'login');
            exit();
        }
        return true;
    }
    
    /**
     * Affiche la liste des films pour l'administration
     */
    public function films() {
        $this->checkAdmin();
        $films = $this->filmModele->getAllFilms();
        
        $data_page = [
            "page_description" => "Administration des films",
            "page_title" => "Administration des films",
            "films" => $films,
            "css" => ["admin.css"],
            "js" => ["admin.js"],
            "view" => [
                "vues/front/header.php",
                "vues/admin/films.php",
                "vues/front/footer.php"
            ],
            "template" => "vues/front/layout.php"
        ];
        
        $this->genererPage($data_page);
    }
    
    /**
     * Affiche le formulaire d'ajout de film
     */
    public function addFilm() {
        $this->checkAdmin();
        // Récupérer les réalisateurs, genres, etc. pour les listes déroulantes
        $realisateurs = $this->realisateurModele->getAllRealisateurs();
        $genres = $this->genreModele->getAllGenres();
        $acteurs = $this->acteurModele->getAllActeurs();
        
        $data_page = [
            "page_description" => "Ajouter un film",
            "page_title" => "Ajouter un film",
            "realisateurs" => $realisateurs,
            "genres" => $genres,
            "acteurs" => $acteurs,
            "css" => ["admin.css", "form-checkboxes.css"],
            "js" => ["admin.js"],
            "view" => [
                "vues/front/header.php",
                "vues/admin/addFilm.php",
                "vues/front/footer.php"
            ],
            "template" => "vues/front/layout.php"
        ];
        
        $this->genererPage($data_page);
    }
    
    /**
     * Traite le formulaire d'ajout de film
     */
    public function saveFilm() {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL . 'admin/films');
            exit();
        }
        
        // Récupérer les données du formulaire
        $titre = isset($_POST['titre']) ? trim($_POST['titre']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $duree = isset($_POST['duree']) ? intval($_POST['duree']) : 0;
        $dateSortie = isset($_POST['dateSortie']) ? $_POST['dateSortie'] : null;
        $coutTotal = isset($_POST['coutTotal']) ? floatval($_POST['coutTotal']) : 0;
        $boxOffice = isset($_POST['boxOffice']) ? floatval($_POST['boxOffice']) : 0;
        $idReal = isset($_POST['idReal']) ? intval($_POST['idReal']) : 0;
        $genres = isset($_POST['genres']) ? $_POST['genres'] : [];
        $acteurs = isset($_POST['acteurs']) ? $_POST['acteurs'] : [];
        $urlBandeAnnonce = isset($_POST['urlBandeAnnonce']) ? trim($_POST['urlBandeAnnonce']) : '';
        $langueOriginale = isset($_POST['langueOriginale']) ? trim($_POST['langueOriginale']) : '';
        
        // Validation des données
        $errors = [];
        if (empty($titre)) {
            $errors[] = "Le titre est obligatoire";
        }
        if (empty($description)) {
            $errors[] = "La description est obligatoire";
        }
        if ($duree <= 0) {
            $errors[] = "La durée doit être supérieure à 0";
        }
        if (empty($dateSortie)) {
            $errors[] = "La date de sortie est obligatoire";
        }
        if ($idReal <= 0) {
            $errors[] = "Le réalisateur est obligatoire";
        }
        if (empty($genres)) {
            $errors[] = "Veuillez sélectionner au moins un genre";
        }
        
        // Vérifier si un film avec le même titre existe déjà
        if (!empty($titre) && $this->filmModele->filmExistsByTitle($titre)) {
            $errors[] = "Un film avec ce titre existe déjà";
        }
        
        // Traitement de l'image
        $urlAffiche = '';
        if (isset($_FILES['affiche']) && $_FILES['affiche']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['affiche']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                // Transformer le titre en format camelCase pour le nom de fichier
                $titreFormatted = strtolower($titre);
                // Remplacer les caractères spéciaux par des espaces
                $titreFormatted = preg_replace('/[^a-zA-Z0-9\s]/', ' ', $titreFormatted);
                // Mettre en majuscule la première lettre après chaque espace
                $titreFormatted = preg_replace_callback('/\s+(\w)/', function($matches) {
                    return strtoupper($matches[1]);
                }, $titreFormatted);
                // Supprimer tous les espaces
                $titreFormatted = str_replace(' ', '', $titreFormatted);
                // Limiter la longueur
                $titreFormatted = substr($titreFormatted, 0, 50);
                $newname = $titreFormatted . '.' . $ext;
                $destination = 'ressources/images/films/' . $newname;
                
                if (move_uploaded_file($_FILES['affiche']['tmp_name'], $destination)) {
                    $urlAffiche = $newname;
                } else {
                    $errors[] = "Erreur lors de l'upload de l'image";
                }
            } else {
                $errors[] = "Format d'image non autorisé. Utilisez JPG, JPEG, PNG ou GIF";
            }
        }
        
        // Si erreurs, rediriger vers le formulaire avec les erreurs
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST; // Pour repopuler le formulaire
            header('Location: ' . URL . 'admin/addFilm');
            exit();
        }
        
        // Ajouter le film
        $idFilm = $this->filmModele->addFilm($titre, $description, $duree, $dateSortie, $coutTotal, $boxOffice, $urlAffiche, $idReal, $urlBandeAnnonce, $langueOriginale);
        
        if ($idFilm) {
            // Ajouter les genres
            foreach ($genres as $idGenre) {
                $this->filmModele->addGenreToFilm($idFilm, $idGenre);
            }
            
            // Ajouter les acteurs
            if (!empty($acteurs)) {
                foreach ($acteurs as $idActeur) {
                    $this->filmModele->addActorToFilm($idFilm, $idActeur);
                }
            }
            
            $_SESSION['success'] = "Le film a été ajouté avec succès";
        } else {
            $_SESSION['errors'] = ["Une erreur est survenue lors de l'ajout du film"];
        }
        
        header('Location: ' . URL . 'admin/films');
        exit();
    }
    
    /**
     * Affiche le formulaire de modification d'un film
     */
    public function editFilm($idFilm) {
        $this->checkAdmin();
        $film = $this->filmModele->getFilmById($idFilm);
        
        if (!$film) {
            $_SESSION['errors'] = ["Film non trouvé"];
            header('Location: ' . URL . 'admin/films');
            exit();
        }
        
        // Récupérer les réalisateurs, genres, etc. pour les listes déroulantes
        $realisateurs = $this->realisateurModele->getAllRealisateurs();
        $genres = $this->genreModele->getAllGenres();
        $acteurs = $this->acteurModele->getAllActeurs();
        
        // Récupérer les genres et acteurs du film
        $filmGenres = $this->filmModele->getGenresByFilmId($idFilm);
        $filmActeurs = $this->filmModele->getActorsByFilmId($idFilm);
        
        $data_page = [
            "page_description" => "Modifier un film",
            "page_title" => "Modifier un film",
            "film" => $film,
            "realisateurs" => $realisateurs,
            "genres" => $genres,
            "acteurs" => $acteurs,
            "filmGenres" => $filmGenres,
            "filmActeurs" => $filmActeurs,
            "css" => ["admin.css", "form-checkboxes.css"],
            "js" => ["admin.js"],
            "view" => [
                "vues/front/header.php",
                "vues/admin/editFilm.php",
                "vues/front/footer.php"
            ],
            "template" => "vues/front/layout.php"
        ];
        
        $this->genererPage($data_page);
    }
    
    /**
     * Traite le formulaire de modification de film
     */
    public function updateFilm() {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL . 'admin/films');
            exit();
        }
        
        $idFilm = isset($_POST['idFilm']) ? intval($_POST['idFilm']) : 0;
        
        if ($idFilm <= 0) {
            $_SESSION['errors'] = ["Film non valide"];
            header('Location: ' . URL . 'admin/films');
            exit();
        }
        
        // Récupérer les données du formulaire
        $titre = isset($_POST['titre']) ? trim($_POST['titre']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $duree = isset($_POST['duree']) ? intval($_POST['duree']) : 0;
        $dateSortie = isset($_POST['dateSortie']) ? $_POST['dateSortie'] : null;
        $coutTotal = isset($_POST['coutTotal']) ? floatval($_POST['coutTotal']) : 0;
        $boxOffice = isset($_POST['boxOffice']) ? floatval($_POST['boxOffice']) : 0;
        $idReal = isset($_POST['idReal']) ? intval($_POST['idReal']) : 0;
        $genres = isset($_POST['genres']) ? $_POST['genres'] : [];
        $acteurs = isset($_POST['acteurs']) ? $_POST['acteurs'] : [];
        $urlBandeAnnonce = isset($_POST['urlBandeAnnonce']) ? trim($_POST['urlBandeAnnonce']) : '';
        $langueOriginale = isset($_POST['langueOriginale']) ? trim($_POST['langueOriginale']) : '';
        
        // Validation des données
        $errors = [];
        if (empty($titre)) {
            $errors[] = "Le titre est obligatoire";
        }
        if (empty($description)) {
            $errors[] = "La description est obligatoire";
        }
        if ($duree <= 0) {
            $errors[] = "La durée doit être supérieure à 0";
        }
        if (empty($dateSortie)) {
            $errors[] = "La date de sortie est obligatoire";
        }
        if ($idReal <= 0) {
            $errors[] = "Le réalisateur est obligatoire";
        }
        if (empty($genres)) {
            $errors[] = "Veuillez sélectionner au moins un genre";
        }
        
        // Vérifier si un film avec le même titre existe déjà (en excluant le film en cours de modification)
        if (!empty($titre) && $this->filmModele->filmExistsByTitle($titre, $idFilm)) {
            $errors[] = "Un film avec ce titre existe déjà";
        }
        
        // Récupérer l'URL de l'affiche actuelle
        $film = $this->filmModele->getFilmById($idFilm);
        $urlAffiche = $film['image'];
        
        // Traitement de l'image si une nouvelle est fournie
        if (isset($_FILES['affiche']) && $_FILES['affiche']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['affiche']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                // Transformer le titre en format camelCase pour le nom de fichier
                $titreFormatted = strtolower($titre);
                // Remplacer les caractères spéciaux par des espaces
                $titreFormatted = preg_replace('/[^a-zA-Z0-9\s]/', ' ', $titreFormatted);
                // Mettre en majuscule la première lettre après chaque espace
                $titreFormatted = preg_replace_callback('/\s+(\w)/', function($matches) {
                    return strtoupper($matches[1]);
                }, $titreFormatted);
                // Supprimer tous les espaces
                $titreFormatted = str_replace(' ', '', $titreFormatted);
                // Limiter la longueur
                $titreFormatted = substr($titreFormatted, 0, 50);
                $newname = $titreFormatted . '.' . $ext;
                $destination = 'ressources/images/films/' . $newname;
                
                if (move_uploaded_file($_FILES['affiche']['tmp_name'], $destination)) {
                    // Supprimer l'ancienne image si elle existe
                    if (!empty($film['image'])) {
                        $oldFile = 'ressources/images/films/' . $film['image'];
                        if (file_exists($oldFile)) {
                            unlink($oldFile);
                        }
                    }
                    $urlAffiche = $newname;
                } else {
                    $errors[] = "Erreur lors de l'upload de l'image";
                }
            } else {
                $errors[] = "Format d'image non autorisé. Utilisez JPG, JPEG, PNG ou GIF";
            }
        }
        
        // Si erreurs, rediriger vers le formulaire avec les erreurs
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST; // Pour repopuler le formulaire
            header('Location: ' . URL . 'admin/editFilm/' . $idFilm);
            exit();
        }
        
        // Mettre à jour le film
        $success = $this->filmModele->updateFilm($idFilm, $titre, $description, $duree, $dateSortie, $coutTotal, $boxOffice, $urlAffiche, $idReal, $urlBandeAnnonce, $langueOriginale);
        
        if ($success) {
            // Mettre à jour les genres
            $this->filmModele->deleteGenresFromFilm($idFilm);
            foreach ($genres as $idGenre) {
                $this->filmModele->addGenreToFilm($idFilm, $idGenre);
            }
            
            // Mettre à jour les acteurs
            $this->filmModele->deleteActorsFromFilm($idFilm);
            if (!empty($acteurs)) {
                foreach ($acteurs as $idActeur) {
                    $this->filmModele->addActorToFilm($idFilm, $idActeur);
                }
            }
            
            $_SESSION['success'] = "Le film a été mis à jour avec succès";
        } else {
            $_SESSION['errors'] = ["Une erreur est survenue lors de la mise à jour du film"];
        }
        
        header('Location: ' . URL . 'admin/films');
        exit();
    }
    
    /**
     * Supprime un film
     */
    public function deleteFilm($idFilm = null) {
        $this->checkAdmin();
        
        // Si l'ID n'est pas fourni dans l'URL, vérifier s'il est fourni en POST
        if ($idFilm === null && isset($_POST['idFilm'])) {
            $idFilm = intval($_POST['idFilm']);
        }
        
        // Vérifier si l'ID est valide
        if (!$idFilm || $idFilm <= 0) {
            $_SESSION['errors'] = ["ID de film invalide"];
            header('Location: ' . URL . 'admin/films');
            exit();
        }
        
        // Vérifier si le film existe
        $film = $this->filmModele->getFilmById($idFilm);
        
        if (!$film) {
            $_SESSION['errors'] = ["Film non trouvé"];
            header('Location: ' . URL . 'admin/films');
            exit();
        }
        
        try {
            // Supprimer les avis associés au film
            $this->avisModele->deleteAvisByFilmId($idFilm);
            
            // Supprimer les relations
            $this->filmModele->deleteGenresFromFilm($idFilm);
            $this->filmModele->deleteActorsFromFilm($idFilm);
            
            // Supprimer le film
            $success = $this->filmModele->deleteFilm($idFilm);
            
            if ($success) {
                // Supprimer l'image du film si elle existe
                if (!empty($film['image'])) {
                    $imagePath = 'ressources/images/films/' . $film['image'];
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                
                $_SESSION['success'] = "Le film a été supprimé avec succès";
            } else {
                $_SESSION['errors'] = ["Une erreur est survenue lors de la suppression du film"];
            }
        } catch (Exception $e) {
            $_SESSION['errors'] = ["Erreur lors de la suppression du film : " . $e->getMessage()];
        }
        
        header('Location: ' . URL . 'admin/films');
        exit();
    }
    
    /**
     * Affiche la liste des réalisateurs pour l'administration
     */
    public function realisateurs() {
        $this->checkAdmin();
        $realisateurs = $this->realisateurModele->getAllRealisateurs();
        
        $data_page = [
            "page_description" => "Administration des réalisateurs",
            "page_title" => "Administration des réalisateurs",
            "realisateurs" => $realisateurs,
            "css" => ["admin.css"],
            "js" => ["admin.js"],
            "view" => [
                "vues/front/header.php",
                "vues/admin/realisateurs.php",
                "vues/front/footer.php"
            ],
            "template" => "vues/front/layout.php"
        ];
        
        $this->genererPage($data_page);
    }
    
    /**
     * Affiche le formulaire d'ajout de réalisateur
     */
    public function addRealisateur() {
        $this->checkAdmin();
        
        $data_page = [
            "page_description" => "Ajouter un réalisateur",
            "page_title" => "Ajouter un réalisateur",
            "css" => ["admin.css"],
            "js" => ["admin.js"],
            "view" => [
                "vues/front/header.php",
                "vues/admin/addRealisateur.php",
                "vues/front/footer.php"
            ],
            "template" => "vues/front/layout.php"
        ];
        
        $this->genererPage($data_page);
    }
    
    /**
     * Traite le formulaire d'ajout de réalisateur
     */
    public function saveRealisateur() {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL . 'admin/realisateurs');
            exit();
        }
        
        // Récupérer les données du formulaire
        $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
        $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
        $dateNaissance = isset($_POST['dateNaissance']) ? $_POST['dateNaissance'] : null;
        $nationalite = isset($_POST['nationalite']) ? trim($_POST['nationalite']) : '';
        
        // Validation des données
        $errors = [];
        if (empty($nom)) {
            $errors[] = "Le nom est obligatoire";
        }
        if (empty($prenom)) {
            $errors[] = "Le prénom est obligatoire";
        }
        if (empty($dateNaissance)) {
            $errors[] = "La date de naissance est obligatoire";
        }
        if (empty($nationalite)) {
            $errors[] = "La nationalité est obligatoire";
        }
        
        // S'il y a des erreurs, rediriger vers le formulaire avec les messages d'erreur
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = [
                'nom' => $nom,
                'prenom' => $prenom,
                'dateNaissance' => $dateNaissance,
                'nationalite' => $nationalite
            ];
            header('Location: ' . URL . 'admin/addRealisateur');
            exit();
        }
        
        // Toutes les validations sont passées, on peut enregistrer le réalisateur
        try {
            $result = $this->realisateurModele->addRealisateur($nom, $prenom, $dateNaissance, $nationalite);
            
            if ($result) {
                $_SESSION['success'] = "Le réalisateur a été ajouté avec succès";
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de l'ajout du réalisateur";
            }
            
            header('Location: ' . URL . 'admin/realisateurs');
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur : " . $e->getMessage();
            header('Location: ' . URL . 'admin/realisateurs');
            exit();
        }
    }
    
    /**
     * Affiche le formulaire de modification d'un réalisateur
     */
    public function editRealisateur($idReal) {
        $this->checkAdmin();
        $realisateur = $this->realisateurModele->getRealisateurById($idReal);
        
        if (!$realisateur) {
            $_SESSION['errors'] = ["Réalisateurs non trouvé"];
            header('Location: ' . URL . 'admin/realisateurs');
            exit();
        }
        
        $data_page = [
            "page_description" => "Modifier un réalisateur",
            "page_title" => "Modifier un réalisateur",
            "realisateur" => $realisateur,
            "css" => ["admin.css"],
            "js" => ["admin.js"],
            "view" => [
                "vues/front/header.php",
                "vues/admin/editRealisateur.php",
                "vues/front/footer.php"
            ],
            "template" => "vues/front/layout.php"
        ];
        
        $this->genererPage($data_page);
    }
    
    /**
     * Traite le formulaire de modification de réalisateur
     */
    public function updateRealisateur() {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL . 'admin/realisateurs');
            exit();
        }
        
        $idReal = isset($_POST['idReal']) ? intval($_POST['idReal']) : 0;
        
        if ($idReal <= 0) {
            $_SESSION['errors'] = ["Réalisateurs non valide"];
            header('Location: ' . URL . 'admin/realisateurs');
            exit();
        }
        
        // Récupérer les données du formulaire
        $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
        $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
        $dateNaissance = isset($_POST['dateNaissance']) ? $_POST['dateNaissance'] : null;
        $nationalite = isset($_POST['nationalite']) ? trim($_POST['nationalite']) : '';
        
        // Validation des données
        $errors = [];
        if (empty($nom)) {
            $errors[] = "Le nom est obligatoire";
        }
        if (empty($prenom)) {
            $errors[] = "Le prénom est obligatoire";
        }
        if (empty($dateNaissance)) {
            $errors[] = "La date de naissance est obligatoire";
        }
        if (empty($nationalite)) {
            $errors[] = "La nationalité est obligatoire";
        }
        
        // Si erreurs, rediriger vers le formulaire avec les erreurs
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST; // Pour repopuler le formulaire
            header('Location: ' . URL . 'admin/editRealisateur/' . $idReal);
            exit();
        }
        
        // Mettre à jour le réalisateur
        $success = $this->realisateurModele->updateRealisateur($idReal, $nom, $prenom, $dateNaissance, $nationalite);
        
        if ($success) {
            $_SESSION['success'] = "Le réalisateur a été mis à jour avec succès";
        } else {
            $_SESSION['errors'] = ["Une erreur est survenue lors de la mise à jour du réalisateur"];
        }
        
        header('Location: ' . URL . 'admin/realisateurs');
        exit();
    }
    
    /**
     * Supprime un réalisateur
     */
    public function deleteRealisateur($idReal = null) {
        $this->checkAdmin();
        
        // Si la méthode est appelée via POST, récupérer l'ID du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idReal'])) {
            $idReal = $_POST['idReal'];
        }
        
        // Vérifier si l'ID est valide
        if (!$idReal) {
            $_SESSION['errors'] = ["Identifiant de réalisateur manquant"];
            header('Location: ' . URL . 'admin/realisateurs');
            exit();
        }
        
        // Vérifier si le réalisateur existe
        $realisateur = $this->realisateurModele->getRealisateurById($idReal);
        
        if (!$realisateur) {
            $_SESSION['errors'] = ["Réalisateurs non trouvé"];
            header('Location: ' . URL . 'admin/realisateurs');
            exit();
        }
        
        try {
            // Supprimer le réalisateur
            $success = $this->realisateurModele->deleteRealisateur($idReal);
            
            if ($success) {
                $_SESSION['success'] = "Le réalisateur a été supprimé avec succès";
            } else {
                $_SESSION['errors'] = ["Une erreur est survenue lors de la suppression du réalisateur"];
            }
        } catch (Exception $e) {
            $_SESSION['errors'] = [$e->getMessage()];
        }
        
        header('Location: ' . URL . 'admin/realisateurs');
        exit();
    }
    
    /**
     * Affiche la liste des genres pour l'administration
     */
    public function genres() {
        $this->checkAdmin();
        try {
            $genres = $this->genreModele->getAllGenres();
            
            $data_page = [
                "page_description" => "Administration des genres",
                "page_title" => "Administration des genres",
                "genres" => $genres,
                "css" => ["admin.css"],
                "js" => ["admin.js", "tables.js"],
                "view" => [
                    "vues/front/header.php",
                    "vues/admin/genres.php",
                    "vues/front/footer.php"
                ],
                "template" => "vues/front/layout.php"
            ];
            
            $this->genererPage($data_page);
        } catch (Exception $e) {
            $_SESSION['errors'] = [$e->getMessage()];
            header('Location: ' . URL . 'admin/dashboard');
            exit();
        }
    }
    
    /**
     * Affiche le formulaire d'ajout de genre
     */
    public function addGenre() {
        $this->checkAdmin();
        
        $data_page = [
            "page_description" => "Ajouter un genre",
            "page_title" => "Ajouter un genre",
            "css" => ["admin.css"],
            "js" => ["admin.js"],
            "view" => [
                "vues/front/header.php",
                "vues/admin/addGenre.php",
                "vues/front/footer.php"
            ],
            "template" => "vues/front/layout.php"
        ];
        
        $this->genererPage($data_page);
    }
    
    /**
     * Traite le formulaire d'ajout de genre
     */
    public function saveGenre() {
        $this->checkAdmin();
        
        // Récupérer les données du formulaire
        $libelle = isset($_POST['libelle']) ? trim($_POST['libelle']) : '';
        
        // Validation des données
        $errors = [];
        
        if (empty($libelle)) {
            $errors[] = "Le libellé est obligatoire";
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST; // Pour repopuler le formulaire
            header('Location: ' . URL . 'admin/addGenre');
            exit();
        }
        
        try {
            // Ajouter le genre
            $idGenre = $this->genreModele->addGenre($libelle);
            
            if ($idGenre) {
                $_SESSION['success'] = "Le genre a été ajouté avec succès";
            } else {
                $_SESSION['errors'] = ["Une erreur est survenue lors de l'ajout du genre"];
            }
        } catch (Exception $e) {
            $_SESSION['errors'] = [$e->getMessage()];
        }
        
        header('Location: ' . URL . 'admin/genres');
        exit();
    }
    
    /**
     * Affiche le formulaire de modification d'un genre
     */
    public function editGenre($idGenre) {
        $this->checkAdmin();
        
        // Vérifier si le genre existe
        $genre = $this->genreModele->getGenreById($idGenre);
        
        if (!$genre) {
            $_SESSION['errors'] = ["Genre non trouvé"];
            header('Location: ' . URL . 'admin/genres');
            exit();
        }
        
        $data_page = [
            "page_description" => "Modifier un genre",
            "page_title" => "Modifier un genre",
            "genre" => $genre,
            "css" => ["admin.css"],
            "js" => ["admin.js"],
            "view" => [
                "vues/front/header.php",
                "vues/admin/editGenre.php",
                "vues/front/footer.php"
            ],
            "template" => "vues/front/layout.php"
        ];
        
        $this->genererPage($data_page);
    }
    
    /**
     * Traite le formulaire de modification de genre
     */
    public function updateGenre() {
        $this->checkAdmin();
        
        // Récupérer les données du formulaire
        $idGenre = isset($_POST['idGenre']) ? (int)$_POST['idGenre'] : 0;
        $libelle = isset($_POST['libelle']) ? trim($_POST['libelle']) : '';
        
        // Validation des données
        $errors = [];
        
        if (empty($idGenre)) {
            $errors[] = "Identifiant de genre manquant";
        }
        
        if (empty($libelle)) {
            $errors[] = "Le libellé est obligatoire";
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST; // Pour repopuler le formulaire
            header('Location: ' . URL . 'admin/editGenre/' . $idGenre);
            exit();
        }
        
        // Mettre à jour le genre
        $success = $this->genreModele->updateGenre($idGenre, $libelle);
        
        if ($success) {
            $_SESSION['success'] = "Le genre a été mis à jour avec succès";
        } else {
            $_SESSION['errors'] = ["Une erreur est survenue lors de la mise à jour du genre"];
        }
        
        header('Location: ' . URL . 'admin/genres');
        exit();
    }
    
    /**
     * Supprime un genre
     */
    public function deleteGenre($idGenre = null) {
        $this->checkAdmin();
        
        // Si la méthode est appelée via POST, récupérer l'ID du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idGenre'])) {
            $idGenre = $_POST['idGenre'];
        }
        
        // Vérifier si l'ID est valide
        if (!$idGenre) {
            $_SESSION['errors'] = ["Identifiant de genre manquant"];
            header('Location: ' . URL . 'admin/genres');
            exit();
        }
        
        // Vérifier si le genre existe
        $genre = $this->genreModele->getGenreById($idGenre);
        
        if (!$genre) {
            $_SESSION['errors'] = ["Genre non trouvé"];
            header('Location: ' . URL . 'admin/genres');
            exit();
        }
        
        try {
            // Supprimer le genre
            $success = $this->genreModele->deleteGenre($idGenre);
            
            if ($success) {
                $_SESSION['success'] = "Le genre a été supprimé avec succès";
            } else {
                $_SESSION['errors'] = ["Une erreur est survenue lors de la suppression du genre"];
            }
        } catch (Exception $e) {
            $_SESSION['errors'] = [$e->getMessage()];
        }
        
        header('Location: ' . URL . 'admin/genres');
        exit();
    }
    
    /**
     * Affiche la liste des acteurs pour l'administration
     */
    public function acteurs() {
        $this->checkAdmin();
        $acteurs = $this->acteurModele->getAllActeurs();
        
        $data_page = [
            "page_description" => "Administration des acteurs",
            "page_title" => "Administration des acteurs",
            "acteurs" => $acteurs,
            "css" => ["admin.css"],
            "js" => ["admin.js"],
            "view" => [
                "vues/front/header.php",
                "vues/acteurs/index.php",
                "vues/front/footer.php"
            ],
            "template" => "vues/front/layout.php"
        ];
        
        $this->genererPage($data_page);
    }
    
    /**
     * Affiche le formulaire d'ajout d'acteur
     */
    public function addActeur() {
        $this->checkAdmin();
        
        $data_page = [
            "page_description" => "Ajouter un acteur",
            "page_title" => "Ajouter un acteur",
            "css" => ["admin.css", "form.css"],
            "js" => ["form-validation.js"],
            "view" => [
                "vues/front/header.php",
                "vues/acteurs/add.php",
                "vues/front/footer.php"
            ],
            "template" => "vues/front/layout.php"
        ];
        
        $this->genererPage($data_page);
    }
    
    /**
     * Traite le formulaire d'ajout d'acteur
     */
    public function saveActeur() {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL . 'admin/acteurs');
            exit();
        }
        
        try {
            // Validation des données
            if (empty($_POST['nom']) || empty($_POST['prenom'])) {
                throw new Exception("Le nom et le prénom sont obligatoires");
            }

            // Traitement de la photo
            $photo = null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = ROOT_PATH . 'ressources/images/acteurs/';
                $fileInfo = pathinfo($_FILES['photo']['name']);
                $extension = strtolower($fileInfo['extension']);
                
                // Vérifier l'extension
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                if (!in_array($extension, $allowedExtensions)) {
                    throw new Exception("Extension de fichier non autorisée. Seuls jpg, jpeg et png sont acceptés.");
                }
                
                // Générer un nom unique
                $photo = uniqid() . '.' . $extension;
                $uploadFile = $uploadDir . $photo;
                
                if (!move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
                    throw new Exception("Erreur lors de l'upload de la photo");
                }
            }

            // Ajout de l'acteur
            $idActeur = $this->acteurModele->addActeur(
                $_POST['nom'],
                $_POST['prenom'],
                !empty($_POST['dateNaissance']) ? $_POST['dateNaissance'] : null,
                !empty($_POST['nationalite']) ? $_POST['nationalite'] : null,
                $photo
            );

            if ($idActeur) {
                $_SESSION['success'] = "L'acteur a été ajouté avec succès";
                header('Location: ' . URL . 'admin/acteurs');
                exit();
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . URL . 'admin/addActeur');
            exit();
        }
    }

    /**
     * Affiche le formulaire de modification d'acteur
     */
    public function editActeur($id) {
        $this->checkAdmin();
        
        try {
            $acteur = $this->acteurModele->getActeurById($id);
            if (!$acteur) {
                throw new Exception("L'acteur demandé n'existe pas");
            }

            $data_page = [
                "page_description" => "Modifier l'acteur",
                "page_title" => "Modifier l'acteur",
                "acteur" => $acteur,
                "css" => ["admin.css", "form.css"],
                "js" => ["form-validation.js"],
                "view" => [
                    "vues/front/header.php",
                    "vues/acteurs/edit.php",
                    "vues/front/footer.php"
                ],
                "template" => "vues/front/layout.php"
            ];
            
            $this->genererPage($data_page);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . URL . 'admin/acteurs');
            exit();
        }
    }

    /**
     * Traite le formulaire de modification d'acteur
     */
    public function updateActeur($id) {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL . 'admin/acteurs');
            exit();
        }
        
        try {
            $acteur = $this->acteurModele->getActeurById($id);
            if (!$acteur) {
                throw new Exception("L'acteur demandé n'existe pas");
            }

            // Validation des données
            if (empty($_POST['nom']) || empty($_POST['prenom'])) {
                throw new Exception("Le nom et le prénom sont obligatoires");
            }

            // Traitement de la photo
            $photo = $acteur['photo']; // Garder l'ancienne photo par défaut
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = ROOT_PATH . 'ressources/images/acteurs/';
                $fileInfo = pathinfo($_FILES['photo']['name']);
                $extension = strtolower($fileInfo['extension']);
                
                // Vérifier l'extension
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                if (!in_array($extension, $allowedExtensions)) {
                    throw new Exception("Extension de fichier non autorisée. Seuls jpg, jpeg et png sont acceptés.");
                }
                
                // Générer un nom unique
                $photo = uniqid() . '.' . $extension;
                $uploadFile = $uploadDir . $photo;
                
                if (!move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
                    throw new Exception("Erreur lors de l'upload de la photo");
                }

                // Supprimer l'ancienne photo si elle existe
                if ($acteur['photo'] && file_exists($uploadDir . $acteur['photo'])) {
                    unlink($uploadDir . $acteur['photo']);
                }
            }

            // Mise à jour de l'acteur
            $success = $this->acteurModele->updateActeur(
                $id,
                $_POST['nom'],
                $_POST['prenom'],
                !empty($_POST['dateNaissance']) ? $_POST['dateNaissance'] : null,
                !empty($_POST['nationalite']) ? $_POST['nationalite'] : null,
                $photo
            );

            if ($success) {
                $_SESSION['success'] = "L'acteur a été modifié avec succès";
                header('Location: ' . URL . 'admin/acteurs');
                exit();
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . URL . 'admin/editActeur/' . $id);
            exit();
        }
    }

    /**
     * Supprime un acteur
     */
    public function deleteActeur($id) {
        $this->checkAdmin();
        
        try {
            if ($this->acteurModele->deleteActeur($id)) {
                $_SESSION['success'] = "L'acteur a été supprimé avec succès";
            } else {
                throw new Exception("Erreur lors de la suppression de l'acteur");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        
        header('Location: ' . URL . 'admin/acteurs');
        exit();
    }

    /**
     * Affiche le tableau de bord d'administration
     */
    public function dashboard() {
        $this->checkAdmin();
        
        $totalFilms = $this->filmModele->countFilms();
        $totalGenres = $this->genreModele->countGenres();
        $totalActeurs = $this->acteurModele->countActeurs();
        $totalRealisateurs = $this->realisateurModele->countRealisateurs();
        $totalAvis = $this->avisModele->countAvis();
        $totalUtilisateurs = $this->utilisateurModele->countUtilisateurs();
        
        // Récupérer les films récemment ajoutés
        $recentFilms = $this->filmModele->getRecentFilms(5);
        
        $data_page = [
            "page_description" => "Tableau de bord d'administration",
            "page_title" => "Administration - Tableau de bord",
            "totalFilms" => $totalFilms,
            "totalGenres" => $totalGenres,
            "totalActeurs" => $totalActeurs,
            "totalRealisateurs" => $totalRealisateurs,
            "totalAvis" => $totalAvis,
            "totalUtilisateurs" => $totalUtilisateurs,
            "recentFilms" => $recentFilms,
            "css" => ["admin.css", "dashboard.css"],
            "js" => ["admin.js"],
            "view" => [
                "vues/front/header.php",
                "vues/admin/dashboard.php",
                "vues/front/footer.php"
            ],
            "template" => "vues/front/layout.php"
        ];
        
        $this->genererPage($data_page);
    }

    /**
     * Génère la page avec le template
     */
    private function genererPage($data) {
        extract($data);
        
        // Générer le contenu des vues
        ob_start();
        foreach ($view as $v) {
            include_once $v;
        }
        $content = ob_get_clean();
        
        // Générer la page complète avec le template
        ob_start();
        include_once $template;
        echo ob_get_clean();
    }
}

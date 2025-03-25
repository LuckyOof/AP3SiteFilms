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
    public function deleteFilm($idFilm) {
        $this->checkAdmin();
        // Vérifier si le film existe
        $film = $this->filmModele->getFilmById($idFilm);
        
        if (!$film) {
            $_SESSION['errors'] = ["Film non trouvé"];
            header('Location: ' . URL . 'admin/films');
            exit();
        }
        
        // Supprimer les relations
        $this->filmModele->deleteGenresFromFilm($idFilm);
        $this->filmModele->deleteActorsFromFilm($idFilm);
        
        // Supprimer les avis associés au film
        $this->avisModele->deleteAvisByFilmId($idFilm);
        
        // Supprimer le film
        $success = $this->filmModele->deleteFilm($idFilm);
        
        if ($success) {
            // Supprimer l'image si elle existe
            if (!empty($film['image'])) {
                $oldFile = 'ressources/images/films/' . $film['image'];
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }
            
            $_SESSION['success'] = "Le film a été supprimé avec succès";
        } else {
            $_SESSION['errors'] = ["Une erreur est survenue lors de la suppression du film"];
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
     * Affiche le tableau de bord d'administration
     */
    public function dashboard() {
        $this->checkAdmin();
        try {
            // Récupérer les statistiques
            $totalFilms = count($this->filmModele->getAllFilms());
            $totalAvis = count($this->avisModele->getAllAvis());
            $totalUtilisateurs = count($this->utilisateurModele->getAllUtilisateurs());
            $totalGenres = count($this->genreModele->getAllGenres());
            $totalActeurs = count($this->acteurModele->getAllActeurs());
            $totalRealisateurs = count($this->realisateurModele->getAllRealisateurs());
            
            // Récupérer les films récemment ajoutés
            $recentFilms = $this->filmModele->getLatestFilms(5);
            
            // Charger la vue
            $data_page = [
                "page_description" => "Tableau de bord d'administration",
                "page_title" => "Tableau de bord d'administration",
                "title" => "Tableau de bord d'administration",
                "description" => "Tableau de bord d'administration du site de films",
                "totalFilms" => $totalFilms,
                "totalAvis" => $totalAvis,
                "totalUtilisateurs" => $totalUtilisateurs,
                "totalGenres" => $totalGenres,
                "totalActeurs" => $totalActeurs,
                "totalRealisateurs" => $totalRealisateurs,
                "recentFilms" => $recentFilms,
                "css" => ["admin.css"],
                "js" => ["admin.js"],
                "view" => [
                    "vues/front/header.php",
                    "vues/admin/dashboard.php",
                    "vues/front/footer.php"
                ],
                "template" => "vues/front/layout.php"
            ];
            
            $this->genererPage($data_page);
        } catch (Exception $e) {
            $_SESSION['message'] = "Erreur lors du chargement du tableau de bord : " . $e->getMessage();
            $_SESSION['message_type'] = "danger";
            header("Location: " . URL);
            exit();
        }
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

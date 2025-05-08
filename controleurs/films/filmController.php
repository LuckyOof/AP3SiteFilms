<?php
require_once MODELES_FILMS_PATH . 'filmModele.php';
require_once MODELES_PATH . 'avis/avisModele.php';

class FilmController {
    private $filmModele;
    private $avisModele;

    public function __construct() {
        $this->filmModele = new FilmModele();
        $this->avisModele = new AvisModele();
    }

    public function index() {
        // Récupérer les derniers films ajoutés
        $latestFilms = $this->filmModele->getLatestFilms(6);
        
        // Récupérer les films les mieux notés
        $topRatedFilms = $this->filmModele->getTopRatedFilms(6);
        
        // Récupérer les genres et les films par genre
        $genres = $this->filmModele->getDistinctValues('genre');
        $filmsByGenre = [];
        
        // Ne récupérer les films que pour les 4 premiers genres
        $selectedGenres = array_slice($genres, 0, 4);
        foreach ($selectedGenres as $genre) {
            $filmsByGenre[$genre] = $this->filmModele->getFilmsByGenre($genre, 4);
        }

        $data_page = [
            "page_description" => "Découvrez les derniers films, les mieux notés et parcourez par genre",
            "page_title" => "Accueil - Site de Films",
            "latest_films" => $latestFilms,
            "top_rated_films" => $topRatedFilms,
            "films_by_genre" => $filmsByGenre,
            "css" => ["home.css"],
            "js" => ["home.js"],
            "view" => [                
                "vues/front/header.php",
                "vues/films/home.php",
                "vues/front/footer.php",
            ],
            "template" => "vues/front/layout.php"
        ];
        $this->genererPage($data_page);
    }

    public function films() {
        // Paramètres de pagination
        $items_per_page = 12;
        $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($current_page - 1) * $items_per_page;

        // Paramètres de tri et filtrage
        $currentGenre = isset($_POST['genre']) ? $_POST['genre'] : '';
        $currentLangue = isset($_POST['langue']) ? $_POST['langue'] : '';
        $currentAnnee = isset($_POST['annee']) ? $_POST['annee'] : '';
        $currentSort = isset($_POST['sort']) ? $_POST['sort'] : 'dateSortie';
        $currentOrder = isset($_POST['order']) ? $_POST['order'] : 'DESC';

        $filters = [
            'genre' => $currentGenre,
            'langue' => $currentLangue,
            'annee' => $currentAnnee
        ];

        // Récupérer les films avec pagination
        $films = $this->filmModele->getFilms($offset, $items_per_page, $currentSort, $currentOrder, $filters);
        $total_films = $this->filmModele->getTotalFilms($currentGenre);
        $total_pages = ceil($total_films / $items_per_page);

        // Récupérer des filtres
        $genres = $this->filmModele->getDistinctValues('genre');
        $langues = $this->filmModele->getDistinctValues('langueVO');
        $annees = $this->filmModele->getDistinctValues('YEAR(dateSortie)');

        // Si l'utilisateur est connecté, marquer les films dans sa watchlist
        if (isset($_SESSION['user'])) {
            foreach ($films as &$film) {
                $film['in_watchlist'] = $this->filmModele->isInWatchlist($_SESSION['user']['id'], $film['idFilm']);
            }
        }

        $data_page = [
            "page_description" => "Explorez notre catalogue complet de films",
            "page_title" => "Films - Site de Films",
            "films" => $films,
            "genres" => $genres,
            "langues" => $langues,
            "annees" => $annees,
            "current_page" => $current_page,
            "currentGenre" => $currentGenre,
            "currentLangue" => $currentLangue,
            "currentAnnee" => $currentAnnee,
            "currentSort" => $currentSort,
            "currentOrder" => $currentOrder,
            "total_pages" => $total_pages,
            "rating_enabled" => true,
            "css" => ["film.css"],
                "view" => [                
                "vues/front/header.php",
                "vues/films/films.php",
                "vues/front/footer.php"
            ],
            "template" => "vues/front/layout.php"
        ];

        $this->genererPage($data_page);
    }

    public function filmInfo($id) {
        $film = $this->filmModele->getFilmById($id);
        
        if (!$film) {
            throw new Exception("Le film demandé n'existe pas");
        }

        // Récupérer les avis et la note moyenne avec le nouveau modèle
        $avis = $this->avisModele->getAvisByFilm($id);
        $moyenne = $this->avisModele->getAverageRating($id);

        // Vérifier si l'utilisateur est connecté
        $isLoggedIn = isset($_SESSION['user']);
        $avisUtilisateur = null;

        // Vérifier si le film est dans la watchlist de l'utilisateur
        $in_watchlist = false;
        if ($isLoggedIn) {
            $in_watchlist = $this->filmModele->isInWatchlist($_SESSION['user']['id'], $id);
            // Récupérer l'avis de l'utilisateur s'il existe
            $avisUtilisateur = $this->avisModele->getAvisUtilisateur($_SESSION['user']['id'], $id);
        }

        $data_page = [
            "page_description" => $film['descri'],
            "page_title" => $film['titre'] . " - Site de Films",
            "film" => $film,
            "avis" => $avis,
            "moyenne" => $moyenne,
            "in_watchlist" => $in_watchlist,
            "isLoggedIn" => $isLoggedIn,
            "avisUtilisateur" => $avisUtilisateur,
            "css" => ["movie-details.css", "avis.css"],
            "js" => ["watchlist.js", "avis.js"],
            "view" => [                
                "vues/front/header.php",
                "vues/films/viewShow.php",
                "vues/front/footer.php"
            ],
            "template" => "vues/front/layout.php"
        ];

        $this->genererPage($data_page);
    }

    public function news() {
        $latestFilms = $this->filmModele->getLatestFilms(10);

        $data_page = [
            "page_description" => "Les dernières sorties cinéma",
            "page_title" => "Actualités - Site de Films",
            "latest_films" => $latestFilms,
            "css" => ["movies.css"],
            "view" => [
                "vues/front/header.php",
                "vues/films/viewNews.php",
                "vues/front/footer.php"
            ],
            "template" => "vues/front/layout.php"
        ];

        $this->genererPage($data_page);
    }

    public function addFilm() {
        // Récupérer les réalisateurs, genres et acteurs pour le formulaire
        $realisateurs = $this->filmModele->getAllRealisateurs();
        $genres = $this->filmModele->getAllGenres();
        $acteurs = $this->filmModele->getAllActeurs();
        
        $message = null;
        $messageClass = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier que les champs obligatoires sont remplis
            if (empty($_POST['titre']) || empty($_POST['descri']) || empty($_POST['duree']) || empty($_POST['dateSortie']) || empty($_POST['idReal'])) {
                $message = "Tous les champs obligatoires doivent être remplis.";
                $messageClass = "error";
            } else {
                // Récupérer les données du formulaire
                $titre = $_POST['titre'];
                $descri = $_POST['descri'];
                $duree = $_POST['duree'];
                $dateSortie = $_POST['dateSortie'];
                $budget = !empty($_POST['budget']) ? $_POST['budget'] : 0;
                $recette = !empty($_POST['recette']) ? $_POST['recette'] : 0;
                $idReal = $_POST['idReal'];
                $bandeAnnonce = !empty($_POST['bandeAnnonce']) ? $_POST['bandeAnnonce'] : '';
                $langueVO = !empty($_POST['langueVO']) ? $_POST['langueVO'] : '';
                
                // Gérer l'upload de l'image
                $imagePath = '';
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = ROOT_PATH . 'ressources/images/films/';
                    $fileInfo = pathinfo($_FILES['image']['name']);
                    $extension = strtolower($fileInfo['extension']);
                    
                    // Vérifier que l'extension est autorisée
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                    if (in_array($extension, $allowedExtensions)) {
                        // Générer un nom de fichier unique
                        $imageName = uniqid() . '.' . $extension;
                        $uploadFile = $uploadDir . $imageName;
                        
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                            $imagePath = $imageName;
                        } else {
                            $message = "Erreur lors de l'upload de l'image.";
                            $messageClass = "error";
                        }
                    } else {
                        $message = "Extension de fichier non autorisée. Seuls jpg, jpeg, png et gif sont acceptés.";
                        $messageClass = "error";
                    }
                }
                
                // Si pas d'erreur, ajouter le film
                if (!$message) {
                    try {
                        // Ajouter le film
                        $idFilm = $this->filmModele->addFilm($titre, $descri, $duree, $dateSortie, $budget, $recette, $imagePath, $idReal, $bandeAnnonce, $langueVO);
                        
                        // Ajouter les genres sélectionnés
                        if ($idFilm && isset($_POST['genres']) && is_array($_POST['genres'])) {
                            foreach ($_POST['genres'] as $idGenre) {
                                $this->filmModele->addGenreToFilm($idFilm, $idGenre);
                            }
                        }
                        
                        // Ajouter les acteurs sélectionnés
                        if ($idFilm && isset($_POST['acteurs']) && is_array($_POST['acteurs'])) {
                            foreach ($_POST['acteurs'] as $idActeur) {
                                $this->filmModele->addActorToFilm($idFilm, $idActeur);
                            }
                        }
                        
                        // Message de succès
                        $message = "Film ajouté avec succès!";
                        $messageClass = "success";
                        
                        // Rediriger vers la liste des films après quelques secondes
                        header("Refresh: 3; URL=" . URL . "admin/films");
                    } catch (Exception $e) {
                        // Gérer les erreurs
                        $message = "Erreur : " . $e->getMessage();
                        $messageClass = "error";
                    }
                }
            }
        }
        
        $data_page = [
            "page_description" => "Ajout d'un film",
            "page_title" => "Ajouter un film",
            "css" => ["addFilm.css"],
            "view" => [
                "vues/front/header.php",
                "vues/films/addFilm.php",
                "vues/front/footer.php"
            ],
            "template" => "vues/front/layout.php",
            "message" => $message,
            "messageClass" => $messageClass,
            "realisateurs" => $realisateurs,
            "genres" => $genres,
            "acteurs" => $acteurs
        ];

        $this->genererPage($data_page);
    }

    public function searchFilms() {
        // Désactiver la mise en mémoire tampon de sortie
        ob_clean();
        
        // S'assurer qu'il n'y a pas d'espace ou de sortie avant l'en-tête
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            if (!isset($_GET['q']) || empty($_GET['q'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Le terme de recherche est requis'
                ], JSON_UNESCAPED_UNICODE);
                exit();
            }

            $query = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS);
            $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT) ?: 10;

            $results = $this->filmModele->searchFilms($query, $limit);

            echo json_encode([
                'success' => true,
                'results' => $results
            ], JSON_UNESCAPED_UNICODE);
            exit();
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Erreur lors de la recherche : ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    public function search() {
        $query = filter_input(INPUT_GET, 'query', FILTER_SANITIZE_SPECIAL_CHARS);
        $isAjax = filter_input(INPUT_GET, 'ajax', FILTER_VALIDATE_BOOLEAN);
        
        if(empty($query)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Requête de recherche vide']);
                exit();
            }
            header('Location: ' . URL);
            exit();
        }

        try {
            $films = $this->filmModele->searchFilms($query);
            $data_page = [
                "page_description" => "Résultats de recherche pour \"" . htmlspecialchars($query) . "\"",
                "page_title" => "Résultats de recherche pour \"" . htmlspecialchars($query) . "\"",
                "films" => $films,
                "query" => $query,
                "css" => ["search.css"],
                "js" => ["search.js"],
                "URL" => URL,
                "CSS_URL" => CSS_URL,
                "JS_URL" => JS_URL,
                "IMAGES_URL" => IMAGES_URL
            ];
            
            // Si c'est une requête AJAX, renvoyer seulement le HTML des résultats
            if ($isAjax) {
                // Préparer les données pour la vue de recherche
                $data = [
                    "films" => $films,
                    "query" => $query,
                    "IMAGES_URL" => IMAGES_URL,
                    "URL" => URL
                ];
                
                // Capturer le contenu de la vue de recherche
                ob_start();
                extract($data);
                include VUES_FILMS_PATH . 'search.php';
                $search_html = ob_get_clean();
                
                // Générer la page complète avec header et footer
                $data_page["view"] = [
                    "vues/front/header.php",
                    "vues/films/search.php",
                    "vues/front/footer.php"
                ];
                $data_page["template"] = "vues/front/layout.php";
                
                // Capturer le contenu HTML complet
                ob_start();
                $this->genererPage($data_page);
                $html = ob_get_clean();
                
                header('Content-Type: application/json');
                echo json_encode([
                    'html' => $html,
                    'title' => $data_page['page_title'],
                    'success' => true
                ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                exit();
            }
            
            // Sinon, afficher la page complète normalement
            $data_page["view"] = [
                "vues/front/header.php",
                "vues/films/search.php",
                "vues/front/footer.php"
            ];
            $data_page["template"] = "vues/front/layout.php";
            
            $this->genererPage($data_page);
            
        } catch (Exception $e) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['error' => $e->getMessage()]);
                exit();
            }
            throw new Exception("Erreur lors de la recherche : " . $e->getMessage());
        }
    }

    /**
     * Affiche la page des films les plus populaires
     */
    public function popular() {
        $popularFilms = $this->filmModele->getPopularFilms(20);

        $data_page = [
            "page_description" => "Les films les plus populaires",
            "page_title" => "Films Populaires - Site de Films",
            "films" => $popularFilms,
            "css" => ["movies.css"],
            "view" => [
                "vues/front/header.php",
                "vues/films/viewPopular.php",
                "vues/front/footer.php"
            ],
            "template" => "vues/front/layout.php"
        ];

        $this->genererPage($data_page);
    }

    /**
     * Affiche la page des films les mieux notés
     */
    public function topRated() {
        $topRatedFilms = $this->filmModele->getTopRatedFilms(20);

        $data_page = [
            "page_description" => "Les films les mieux notés",
            "page_title" => "Films les Mieux Notés - Site de Films",
            "films" => $topRatedFilms,
            "css" => ["movies.css"],
            "view" => [
                "vues/front/header.php",
                "vues/films/viewTopRated.php",
                "vues/front/footer.php"
            ],
            "template" => "vues/front/layout.php"
        ];

        $this->genererPage($data_page);
    }

    /**
     * Affiche la page des films à venir
     */
    public function upcoming() {
        $upcomingFilms = $this->filmModele->getUpcomingFilms(20);

        $data_page = [
            "page_description" => "Les films à venir prochainement",
            "page_title" => "Films à Venir - Site de Films",
            "films" => $upcomingFilms,
            "css" => ["movies.css"],
            "view" => [
                "vues/front/header.php",
                "vues/films/viewUpcoming.php",
                "vues/front/footer.php"
            ],
            "template" => "vues/front/layout.php"
        ];

        $this->genererPage($data_page);
    }

    /**
     * Ajoute ou modifie un avis sur un film
     */
    public function ajouterAvis() {
        // Définir l'en-tête Content-Type dès le début
        header('Content-Type: application/json');
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour laisser un avis']);
            exit();
        }

        // Vérifier si la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit();
        }

        // Récupérer les données du formulaire
        $idFilm = isset($_POST['idFilm']) ? intval($_POST['idFilm']) : 0;
        $note = isset($_POST['note']) ? floatval($_POST['note']) : 0;
        $commentaire = isset($_POST['commentaire']) ? trim($_POST['commentaire']) : '';
        $idAvis = isset($_POST['idAvis']) ? trim($_POST['idAvis']) : null;

        // Valider les données
        if ($idFilm <= 0) {
            echo json_encode(['success' => false, 'message' => 'Film invalide']);
            exit();
        }

        if ($note < 0 || $note > 5) {
            echo json_encode(['success' => false, 'message' => 'La note doit être comprise entre 0 et 5']);
            exit();
        }

        // Ajouter ou modifier l'avis
        $idUtilisateur = $_SESSION['user']['id'];
        
        try {
            if($idAvis){
                $resultat = $this->avisModele->updateAvis($idAvis, $note, $commentaire);
            }
            else{
                $resultat = $this->avisModele->addAvis($idFilm, $idUtilisateur, $note, $commentaire);
            }
            
            if ($resultat) {
                // Récupérer les avis mis à jour
                $avis = $this->avisModele->getAvisByFilm($idFilm);
                $moyenne = $this->avisModele->getAverageRating($idFilm);

                echo json_encode([
                    'success' => true, 
                    'message' => 'Votre avis a été enregistré avec succès',
                    'avis' => $avis,
                    'moyenne' => $moyenne
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Une erreur est survenue lors de l\'enregistrement de votre avis']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    /**
     * Supprime un avis
     */
    public function supprimerAvis() {
        // Définir l'en-tête Content-Type dès le début
        header('Content-Type: application/json');
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour supprimer un avis']);
            exit();
        }

        // Vérifier si la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit();
        }

        // Récupérer les données
        $numAvis = isset($_POST['numAvis']) ? intval($_POST['numAvis']) : 0;
        $idFilm = isset($_POST['idFilm']) ? intval($_POST['idFilm']) : 0;

        // Valider les données
        if ($numAvis <= 0) {
            echo json_encode(['success' => false, 'message' => 'Avis invalide']);
            exit();
        }

        // Vérifier que l'avis appartient bien à l'utilisateur
        $avis = $this->avisModele->getAvisById($numAvis);
        if (!$avis || $avis['idUtilisateur'] != $_SESSION['user']['id']) {
            echo json_encode(['success' => false, 'message' => 'Vous n\'êtes pas autorisé à supprimer cet avis']);
            exit();
        }

        try {
            $resultat = $this->avisModele->deleteAvis($numAvis);
            
            if ($resultat) {
                // Récupérer les avis mis à jour
                $avis = $this->avisModele->getAvisByFilm($idFilm);
                $moyenne = $this->avisModele->getAverageRating($idFilm);

                echo json_encode([
                    'success' => true, 
                    'message' => 'Votre avis a été supprimé avec succès',
                    'avis' => $avis,
                    'moyenne' => $moyenne
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Une erreur est survenue lors de la suppression de votre avis']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit();
    }

    private function genererPage($data) {
        // S'assurer que les constantes URL sont disponibles dans les vues
        if (!isset($data['URL'])) $data['URL'] = URL;
        if (!isset($data['CSS_URL'])) $data['CSS_URL'] = CSS_URL;
        if (!isset($data['JS_URL'])) $data['JS_URL'] = JS_URL;
        if (!isset($data['IMAGES_URL'])) $data['IMAGES_URL'] = IMAGES_URL;
        
        // Extraction des variables pour la vue
        extract($data);

        // Définition des valeurs par défaut pour les métadonnées
        $title = isset($page_title) ? $page_title : "AP3 Films";
        $description = isset($page_description) ? $page_description : "Découvrez notre sélection de films";
        
        // Gestion des ressources CSS et JS
        $css = isset($css) ? $css : [];
        $js = isset($js) ? $js : [];

        // Capture du contenu des vues
        ob_start();
        foreach ($view as $viewFile) {
            require $viewFile;
        }
        $content = ob_get_clean();

        // Rendu du template avec le contenu
        require_once($template);
    }
}

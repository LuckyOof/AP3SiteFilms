<?php
require_once MODELES_FILMS_PATH . 'filmModele.php';
require_once MODELES_PATH . 'avis/avisModele.php';

class ClassementController {
    private $filmModele;
    private $avisModele;

    public function __construct() {
        $this->filmModele = new FilmModele();
        $this->avisModele = new AvisModele();
    }

    public function index() {
        // Récupérer les paramètres de tri et de filtrage
        $sortBy = isset($_POST['sort']) ? $_POST['sort'] : 'note';
        $sortOrder = isset($_POST['order']) ? $_POST['order'] : 'DESC';
        $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
        
        // Filtres
        $filters = [];
        if (isset($_POST['genre']) && !empty($_POST['genre'])) {
            $filters['genre'] = $_POST['genre'];
        }
        if (isset($_POST['annee']) && !empty($_POST['annee'])) {
            $filters['annee'] = intval($_POST['annee']);
        }
        if (isset($_POST['langue']) && !empty($_POST['langue'])) {
            $filters['langue'] = $_POST['langue'];
        }
        
        // Récupérer les films classés selon les critères
        $classement = $this->getClassement($sortBy, $sortOrder, $limit, $filters);
        
        // Récupérer les options de filtrage
        $genres = $this->filmModele->getAllGenres();
        $annees = $this->filmModele->getDistinctValues('YEAR(dateSortie)');
        $langues = $this->filmModele->getDistinctValues('langueVO');
        
        $data_page = [
            "page_description" => "Classement des films",
            "page_title" => "Classement des films",
            "titre" => "Classement des films",
            "classement" => $classement,
            "filter_options" => [
                "genres" => $genres,
                "annees" => $annees,
                "langues" => $langues
            ],
            "current_sort" => [
                "by" => $sortBy,
                "order" => $sortOrder,
                "limit" => $limit
            ],
            "css" => ["classement.css"],
            "js" => ["classement.js"],
            "view" => [
                "vues/front/header.php",
                "vues/films/classement.php",
                "vues/front/footer.php"
            ],
            "template" => "vues/front/layout.php"
        ];
        
        $this->genererPage($data_page);
    }
    
    /**
     * Récupère les films classés selon les critères spécifiés
     * 
     * @param string $sortBy Critère de tri (note, popularite, recents, boxOffice)
     * @param string $sortOrder Ordre de tri (ASC ou DESC)
     * @param int $limit Nombre maximum de films à afficher
     * @param array $filters Filtres additionnels (genre, année, langue)
     * @return array Liste des films classés
     */
    private function getClassement($sortBy = 'note', $sortOrder = 'DESC', $limit = 10, $filters = []) {
        // Convertir les identifiants de genre en valeurs numériques
        if (!empty($filters['genre'])) {
            // Récupérer l'ID du genre à partir de son libellé
            $genreId = $this->filmModele->getGenreIdByName($filters['genre']);
            if ($genreId) {
                $filters['genre'] = $genreId;
            }
        }
        
        switch ($sortBy) {
            case 'note':
                $films = $this->avisModele->getFilmsWithAverageRating($sortOrder, $limit, $filters);
                break;
                
            case 'popularite':
                $films = $this->avisModele->getFilmsByPopularity($sortOrder, $limit, $filters);
                break;
                
            case 'recents':
                $films = $this->filmModele->getFilmsByReleaseDate($sortOrder, $limit, $filters);
                break;
                
            case 'boxOffice':
                $films = $this->filmModele->getFilmsByBoxOffice($sortOrder, $limit, $filters);
                break;
                
            default:
                $films = $this->avisModele->getFilmsWithAverageRating($sortOrder, $limit, $filters);
        }
        
        return $films;
    }

    private function genererPage($data) {
        extract($data);
        ob_start();
        foreach ($view as $v) {
            include $v;
        }
        $content = ob_get_clean();
        include $template;
    }
}

<?php
require_once MODELES_PATH . "PDOModel.php";

/**
 * Classe FilmModele
 * 
 * Gère toutes les opérations liées aux films dans la base de données
 * Hérite de PDOModel pour les fonctionnalités de connexion à la base de données
 */
class FilmModele extends PDOModel {
    /**
     * Formate le chemin d'image d'un film
     * 
     * @param string|null $imageName Nom de l'image à formater
     * @return string Chemin formaté de l'image ou chemin par défaut si vide
     */
    public function formatImagePath($imageName) {
        // Si l'image est vide, retourner l'image par défaut
        if (empty($imageName)) {
            return 'default.jpg';
        }
        
        // Si l'image contient déjà un chemin complet (http:// ou https://), la retourner telle quelle
        if (strpos($imageName, 'http://') === 0 || strpos($imageName, 'https://') === 0) {
            return $imageName;
        }
        
        // Si l'image contient déjà le chemin /ressources/images/films/, extraire juste le nom du fichier
        if (strpos($imageName, '/ressources/images/films/') === 0) {
            $imageName = basename($imageName);
        }
        
        // Si l'image contient l'ancien chemin /ressources/images/affiches/, extraire juste le nom du fichier
        if (strpos($imageName, '/ressources/images/affiches/') === 0) {
            $imageName = basename($imageName);
        }
        
        // Retourner juste le nom de l'image pour que les templates puissent ajouter le préfixe URL
        return $imageName;
    }

    /**
     * Récupère les derniers films ajoutés
     * 
     * @param int $limit Nombre maximum de films à récupérer
     * @return array Liste des derniers films avec leurs informations
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getLatestFilms($limit = 6) {
        try {
            $sql = "SELECT f.*,
                    COALESCE(r.nom, 'Non spécifié') as realisateurNom,
                    COALESCE(r.prenom, '') as realisateurPrenom,
                    GROUP_CONCAT(DISTINCT g.libelle) as genres,
                    COALESCE(AVG(a.note), 0) as moyenne_notes,
                    COUNT(a.note) as nombre_notes
                    FROM Film f 
                    LEFT JOIN Realisateur r ON f.idReal = r.idReal
                    LEFT JOIN AppartenirGenre ag ON f.idFilm = ag.idFilm
                    LEFT JOIN Genre g ON ag.idGenre = g.idGenre
                    LEFT JOIN Avis a ON f.idFilm = a.idFilm
                    GROUP BY f.idFilm, r.nom, r.prenom
                    ORDER BY f.dateSortie DESC 
                    LIMIT :limit";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
            $stmt->execute();
            $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($films as &$film) {
                $film['image'] = $this->formatImagePath($film['image']);
                // S'assurer que la moyenne est un nombre avec une décimale
                $film['moyenne_notes'] = number_format((float)$film['moyenne_notes'], 1, '.', '');
                // Convertir les genres en tableau
                $film['genres'] = $film['genres'] ? explode(',', $film['genres']) : [];
            }
            
            return $films;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des derniers films : " . $e->getMessage());
        }
    }

    /**
     * Récupère les films les mieux notés
     * 
     * @param int $limit Nombre maximum de films à récupérer
     * @return array Liste des films les mieux notés avec leurs informations
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getTopRatedFilms($limit = 6) {
        try {
            $sql = "SELECT f.*,
                    COALESCE(r.nom, 'Non spécifié') as realisateurNom,
                    COALESCE(r.prenom, '') as realisateurPrenom,
                    GROUP_CONCAT(DISTINCT g.libelle) as genres,
                    COALESCE(AVG(a.note), 0) as moyenne_notes,
                    COUNT(a.note) as nombre_notes
                    FROM Film f 
                    LEFT JOIN Realisateur r ON f.idReal = r.idReal
                    LEFT JOIN AppartenirGenre ag ON f.idFilm = ag.idFilm
                    LEFT JOIN Genre g ON ag.idGenre = g.idGenre
                    LEFT JOIN Avis a ON f.idFilm = a.idFilm
                    GROUP BY f.idFilm, r.nom, r.prenom
                    HAVING nombre_notes > 0
                    ORDER BY moyenne_notes DESC, nombre_notes DESC
                    LIMIT :limit";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
            $stmt->execute();
            $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($films as &$film) {
                $film['image'] = $this->formatImagePath($film['image']);
                // S'assurer que la moyenne est un nombre avec une décimale
                $film['moyenne_notes'] = number_format((float)$film['moyenne_notes'], 1, '.', '');
                // Convertir les genres en tableau
                $film['genres'] = $film['genres'] ? explode(',', $film['genres']) : [];
            }
            
            return $films;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des films les mieux notés : " . $e->getMessage());
        }
    }

    /**
     * Récupère une liste de films avec pagination, tri et filtres
     * 
     * @param int $offset Position de départ pour la pagination
     * @param int $limit Nombre maximum de films à récupérer
     * @param string $sort Colonne de tri
     * @param string $order Direction du tri (ASC ou DESC)
     * @param array $filters Filtres à appliquer (genre, année, langue)
     * @return array Liste des films correspondant aux critères
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getFilms($offset = 0, $limit = 12, $sort = 'dateSortie', $order = 'DESC', $filters = []) {
        try {
            $whereClause = '';
            $params = [];
            
            // Construction des filtres
            if (!empty($filters['genre'])) {
                $whereClause .= " AND g.libelle = :genre";
                $params[':genre'] = $filters['genre'];
            }
            if (!empty($filters['annee'])) {
                $whereClause .= " AND YEAR(f.dateSortie) = :annee";
                $params[':annee'] = $filters['annee'];
            }
            if (!empty($filters['langue'])) {
                $whereClause .= " AND f.langueOriginale = :langue";
                $params[':langue'] = $filters['langue'];
            }
            
            // Validation du tri
            $allowedSorts = ['titre', 'dateSortie', 'duree', 'budget', 'recette'];
            $sort = in_array($sort, $allowedSorts) ? $sort : 'dateSortie';
            $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

            $sql = "SELECT f.*,
                    COALESCE(r.nom, 'Non spécifié') as realisateurNom,
                    COALESCE(r.prenom, '') as realisateurPrenom,
                    GROUP_CONCAT(DISTINCT g.libelle) as genres,
                    COALESCE(AVG(a.note), 0) as moyenne_notes,
                    COUNT(a.note) as nombre_notes
                    FROM Film f 
                    LEFT JOIN Realisateur r ON f.idReal = r.idReal
                    LEFT JOIN AppartenirGenre ag ON f.idFilm = ag.idFilm
                    LEFT JOIN Genre g ON ag.idGenre = g.idGenre
                    LEFT JOIN Avis a ON f.idFilm = a.idFilm
                    WHERE 1=1 $whereClause
                    GROUP BY f.idFilm, r.nom, r.prenom
                    ORDER BY f.$sort $order 
                    LIMIT :offset, :limit";

            $stmt = $this->getBdd()->prepare($sql);
            
            // Bind des paramètres de filtres
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
            $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($films as &$film) {
                $film['image'] = $this->formatImagePath($film['image']);
                // S'assurer que la moyenne est un nombre avec une décimale
                $film['moyenne_notes'] = number_format((float)$film['moyenne_notes'], 1, '.', '');
                // Convertir les genres en tableau
                $film['genres'] = $film['genres'] ? explode(',', $film['genres']) : [];
            }
            
            return $films;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des films : " . $e->getMessage());
        }
    }

    /**
     * Récupère les films à venir (date de sortie future)
     * 
     * @param int $limit Nombre maximum de films à récupérer
     * @return array Liste des films à venir avec leurs informations
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getUpcomingFilms($limit = 10) {
        try {
            $sql = "SELECT f.*,
                    COALESCE(r.nom, 'Non spécifié') as realisateurNom,
                    COALESCE(r.prenom, '') as realisateurPrenom,
                    GROUP_CONCAT(DISTINCT g.libelle) as genres,
                    COALESCE(AVG(a.note), 0) as moyenne_notes,
                    COUNT(a.note) as nombre_notes
                    FROM Film f 
                    LEFT JOIN Realisateur r ON f.idReal = r.idReal
                    LEFT JOIN AppartenirGenre ag ON f.idFilm = ag.idFilm
                    LEFT JOIN Genre g ON ag.idGenre = g.idGenre
                    LEFT JOIN Avis a ON f.idFilm = a.idFilm
                    WHERE f.dateSortie > CURDATE()
                    GROUP BY f.idFilm, r.nom, r.prenom
                    ORDER BY f.dateSortie ASC
                    LIMIT :limit";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
            $stmt->execute();
            $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($films as &$film) {
                $film['image'] = $this->formatImagePath($film['image']);
                // S'assurer que la moyenne est un nombre avec une décimale
                $film['moyenne_notes'] = number_format((float)$film['moyenne_notes'], 1, '.', '');
                // Convertir les genres en tableau
                $film['genres'] = $film['genres'] ? explode(',', $film['genres']) : [];
            }
            
            return $films;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des films à venir : " . $e->getMessage());
        }
    }

    /**
     * Récupère les films les plus populaires (basé sur le nombre d'avis)
     * 
     * @param int $limit Nombre maximum de films à récupérer
     * @return array Liste des films les plus populaires avec leurs informations
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getPopularFilms($limit = 10) {
        try {
            $sql = "SELECT f.*,
                    COALESCE(r.nom, 'Non spécifié') as realisateurNom,
                    COALESCE(r.prenom, '') as realisateurPrenom,
                    GROUP_CONCAT(DISTINCT g.libelle) as genres,
                    COALESCE(AVG(a.note), 0) as moyenne_notes,
                    COUNT(a.note) as nombre_notes
                    FROM Film f 
                    LEFT JOIN Realisateur r ON f.idReal = r.idReal
                    LEFT JOIN AppartenirGenre ag ON f.idFilm = ag.idFilm
                    LEFT JOIN Genre g ON ag.idGenre = g.idGenre
                    LEFT JOIN Avis a ON f.idFilm = a.idFilm
                    GROUP BY f.idFilm, r.nom, r.prenom
                    ORDER BY nombre_notes DESC, moyenne_notes DESC
                    LIMIT :limit";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
            $stmt->execute();
            $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($films as &$film) {
                $film['image'] = $this->formatImagePath($film['image']);
                // S'assurer que la moyenne est un nombre avec une décimale
                $film['moyenne_notes'] = number_format((float)$film['moyenne_notes'], 1, '.', '');
                // Convertir les genres en tableau
                $film['genres'] = $film['genres'] ? explode(',', $film['genres']) : [];
            }
            
            return $films;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des films populaires : " . $e->getMessage());
        }
    }

    /**
     * Récupère les films triés par date de sortie
     * 
     * @param string $sortOrder Ordre de tri (ASC ou DESC)
     * @param int $limit Nombre maximum de films à récupérer
     * @param array $filters Filtres à appliquer (genre, année, langue)
     * @return array Liste des films triés par date de sortie
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getFilmsByReleaseDate($sortOrder = 'DESC', $limit = 10, $filters = []) {
        try {
            $whereClause = "";
            $joinClause = "";
            $params = [];
            
            // Appliquer les filtres
            if (!empty($filters)) {
                if (isset($filters['genre']) && !empty($filters['genre'])) {
                    $joinClause .= " LEFT JOIN AppartenirGenre ag ON f.idFilm = ag.idFilm";
                    $whereClause .= " AND ag.idGenre = :idGenre";
                    $params[':idGenre'] = $filters['genre'];
                }
                
                if (isset($filters['annee']) && !empty($filters['annee'])) {
                    $whereClause .= " AND YEAR(f.dateSortie) = :annee";
                    $params[':annee'] = $filters['annee'];
                }
                
                if (isset($filters['langue']) && !empty($filters['langue'])) {
                    $whereClause .= " AND f.langueVO = :langue";
                    $params[':langue'] = $filters['langue'];
                }
            }
            
            $sql = "SELECT f.*,
                    COALESCE(r.nom, 'Non spécifié') as realisateurNom,
                    COALESCE(r.prenom, '') as realisateurPrenom,
                    GROUP_CONCAT(DISTINCT g2.libelle) as genres,
                    COALESCE(AVG(a.note), 0) as note_moyenne,
                    COUNT(a.note) as nombre_avis
                    FROM Film f 
                    LEFT JOIN Realisateur r ON f.idReal = r.idReal
                    LEFT JOIN AppartenirGenre ag2 ON f.idFilm = ag2.idFilm
                    LEFT JOIN Genre g2 ON ag2.idGenre = g2.idGenre
                    LEFT JOIN Avis a ON f.idFilm = a.idFilm
                    $joinClause
                    WHERE 1=1 $whereClause
                    GROUP BY f.idFilm, r.nom, r.prenom
                    ORDER BY f.dateSortie $sortOrder
                    LIMIT :limit";
            
            $stmt = $this->getBdd()->prepare($sql);
            
            // Bind des paramètres de filtrage
            foreach ($params as $param => $value) {
                if (strpos($param, 'annee') !== false) {
                    $stmt->bindValue($param, $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($param, $value, PDO::PARAM_STR);
                }
            }
            
            $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
            $stmt->execute();
            $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($films as &$film) {
                $film['image'] = $this->formatImagePath($film['image']);
                // S'assurer que la moyenne est un nombre avec une décimale
                $film['note_moyenne'] = number_format((float)$film['note_moyenne'], 1, '.', '');
                // Convertir les genres en tableau
                $film['genres'] = $film['genres'] ? explode(',', $film['genres']) : [];
            }
            
            return $films;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des films par date de sortie : " . $e->getMessage());
        }
    }

    /**
     * Récupère les films triés par box-office
     * 
     * @param string $sortOrder Ordre de tri (ASC ou DESC)
     * @param int $limit Nombre maximum de films à récupérer
     * @param array $filters Filtres à appliquer (genre, année, langue)
     * @return array Liste des films triés par box-office
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getFilmsByBoxOffice($sortOrder = 'DESC', $limit = 10, $filters = []) {
        try {
            $whereClause = "";
            $joinClause = "";
            $params = [];
            
            // Appliquer les filtres
            if (!empty($filters)) {
                if (isset($filters['genre']) && !empty($filters['genre'])) {
                    $joinClause .= " LEFT JOIN AppartenirGenre ag ON f.idFilm = ag.idFilm";
                    $whereClause .= " AND ag.idGenre = :idGenre";
                    $params[':idGenre'] = $filters['genre'];
                }
                
                if (isset($filters['annee']) && !empty($filters['annee'])) {
                    $whereClause .= " AND YEAR(f.dateSortie) = :annee";
                    $params[':annee'] = $filters['annee'];
                }
                
                if (isset($filters['langue']) && !empty($filters['langue'])) {
                    $whereClause .= " AND f.langueVO = :langue";
                    $params[':langue'] = $filters['langue'];
                }
            }
            
            $sql = "SELECT f.*,
                    COALESCE(r.nom, 'Non spécifié') as realisateurNom,
                    COALESCE(r.prenom, '') as realisateurPrenom,
                    GROUP_CONCAT(DISTINCT g2.libelle) as genres,
                    COALESCE(AVG(a.note), 0) as note_moyenne,
                    COUNT(a.note) as nombre_avis,
                    f.recettes as recettes
                    FROM Film f 
                    LEFT JOIN Realisateur r ON f.idReal = r.idReal
                    LEFT JOIN AppartenirGenre ag2 ON f.idFilm = ag2.idFilm
                    LEFT JOIN Genre g2 ON ag2.idGenre = g2.idGenre
                    LEFT JOIN Avis a ON f.idFilm = a.idFilm
                    $joinClause
                    WHERE f.recettes IS NOT NULL AND f.recettes > 0 $whereClause
                    GROUP BY f.idFilm, r.nom, r.prenom
                    ORDER BY f.recettes $sortOrder
                    LIMIT :limit";
            
            $stmt = $this->getBdd()->prepare($sql);
            
            // Bind des paramètres de filtrage
            foreach ($params as $param => $value) {
                if (strpos($param, 'annee') !== false) {
                    $stmt->bindValue($param, $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($param, $value, PDO::PARAM_STR);
                }
            }
            
            $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
            $stmt->execute();
            $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($films as &$film) {
                $film['image'] = $this->formatImagePath($film['image']);
                // S'assurer que la moyenne est un nombre avec une décimale
                $film['note_moyenne'] = number_format((float)$film['note_moyenne'], 1, '.', '');
                // Convertir les genres en tableau
                $film['genres'] = $film['genres'] ? explode(',', $film['genres']) : [];
            }
            
            return $films;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des films par box-office : " . $e->getMessage());
        }
    }

    /**
     * Récupère tous les genres disponibles
     * 
     * @return array Liste des genres
     */
    public function getAllGenres() {
        try {
            $sql = "SELECT libelle FROM Genre ORDER BY libelle ASC";
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->execute();
            $genres = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return $genres;
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération des genres : ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère tous les films pour l'administration
     * 
     * @return array Liste de tous les films avec leurs informations
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getAllFilms() {
        try {
            $sql = "SELECT f.*, 
                   r.nom as nomRealisateur, r.prenom as prenomRealisateur
                   FROM Film f
                   LEFT JOIN Realisateur r ON f.idReal = r.idReal
                   ORDER BY f.titre ASC";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->execute();
            
            $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Formater les chemins d'images pour chaque film
            foreach ($films as &$film) {
                if (isset($film['image'])) {
                    $film['urlAffiche'] = $this->formatImagePath($film['image']);
                }
            }
            
            return $films;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération de tous les films : " . $e->getMessage());
        }
    }
    
    /**
     * Récupère tous les réalisateurs
     * 
     * @return array Liste de tous les réalisateurs
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getAllRealisateurs() {
        try {
            $sql = "SELECT * FROM Realisateur ORDER BY nom, prenom";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des réalisateurs : " . $e->getMessage());
        }
    }
    
    /**
     * Récupère tous les acteurs
     * 
     * @return array Liste de tous les acteurs
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getAllActeurs() {
        try {
            $sql = "SELECT * FROM Acteur ORDER BY nom, prenom";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des acteurs : " . $e->getMessage());
        }
    }
    
    /**
     * Récupère les genres d'un film
     * 
     * @param int $idFilm Identifiant du film
     * @return array Liste des genres du film
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getGenresByFilmId($idFilm) {
        try {
            $sql = "SELECT g.* 
                   FROM Genre g
                   JOIN AppartenirGenre ag ON g.idGenre = ag.idGenre
                   WHERE ag.idFilm = :idFilm";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idFilm', $idFilm, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des genres du film : " . $e->getMessage());
        }
    }
    
    /**
     * Récupère les acteurs d'un film
     * 
     * @param int $idFilm Identifiant du film
     * @return array Liste des acteurs du film
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getActorsByFilmId($idFilm) {
        try {
            $sql = "SELECT a.* 
                   FROM Acteur a
                   JOIN Jouer j ON a.idActeur = j.idActeur
                   WHERE j.idFilm = :idFilm";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idFilm', $idFilm, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des acteurs du film : " . $e->getMessage());
        }
    }
    
    /**
     * Ajoute un nouveau film dans la base de données avec tous ses détails
     * 
     * @param string $titre Titre du film
     * @param string $description Description du film
     * @param int $duree Durée du film en minutes
     * @param string $dateSortie Date de sortie au format YYYY-MM-DD
     * @param float $coutTotal Budget du film
     * @param float $boxOffice Recette du film
     * @param string $urlAffiche URL de l'affiche du film
     * @param int $idReal Identifiant du réalisateur
     * @param string $urlBandeAnnonce URL de la bande-annonce
     * @param string $langueOriginale Langue originale du film
     * @return int|false Identifiant du film ajouté ou false en cas d'échec
     * @throws Exception En cas d'erreur lors de l'ajout
     */
    public function addFilm($titre, $description, $duree, $dateSortie, $coutTotal, $boxOffice, $urlAffiche, $idReal, $urlBandeAnnonce = '', $langueOriginale = '') {
        try {
            // Vérifier d'abord si un film avec ce titre existe déjà
            if ($this->filmExistsByTitle($titre)) {
                throw new Exception("Un film avec ce titre existe déjà");
            }
            
            $this->getBdd()->beginTransaction();
            
            $sql = "INSERT INTO Film (titre, descri, duree, dateSortie, coutTotal, boxOffice, image, idReal, trailer, langueVO) 
                   VALUES (:titre, :description, :duree, :dateSortie, :coutTotal, :boxOffice, :urlAffiche, :idReal, :urlBandeAnnonce, :langueOriginale)";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':titre', $titre, PDO::PARAM_STR);
            $stmt->bindValue(':description', $description, PDO::PARAM_STR);
            $stmt->bindValue(':duree', $duree, PDO::PARAM_INT);
            $stmt->bindValue(':dateSortie', $dateSortie, PDO::PARAM_STR);
            $stmt->bindValue(':coutTotal', $coutTotal, PDO::PARAM_STR);
            $stmt->bindValue(':boxOffice', $boxOffice, PDO::PARAM_STR);
            $stmt->bindValue(':urlAffiche', $urlAffiche, PDO::PARAM_STR);
            $stmt->bindValue(':idReal', $idReal, PDO::PARAM_INT);
            $stmt->bindValue(':urlBandeAnnonce', $urlBandeAnnonce, PDO::PARAM_STR);
            $stmt->bindValue(':langueOriginale', $langueOriginale, PDO::PARAM_STR);
            
            $stmt->execute();
            
            $idFilm = $this->getBdd()->lastInsertId();
            
            $this->getBdd()->commit();
            
            return $idFilm;
        } catch (PDOException $e) {
            if ($this->getBdd()->inTransaction()) {
                $this->getBdd()->rollBack();
            }
            throw new Exception("Erreur lors de l'ajout du film : " . $e->getMessage());
        }
    }
    
    /**
     * Met à jour un film existant dans la base de données
     * 
     * @param int $idFilm Identifiant du film à mettre à jour
     * @param string $titre Titre du film
     * @param string $description Description du film
     * @param int $duree Durée du film en minutes
     * @param string $dateSortie Date de sortie au format YYYY-MM-DD
     * @param float $coutTotal Budget du film
     * @param float $boxOffice Recette du film
     * @param string $urlAffiche URL de l'affiche du film
     * @param int $idReal Identifiant du réalisateur
     * @param string $urlBandeAnnonce URL de la bande-annonce
     * @param string $langueOriginale Langue originale du film
     * @return bool True si la mise à jour a réussi, false sinon
     * @throws Exception En cas d'erreur lors de la mise à jour
     */
    public function updateFilm($idFilm, $titre, $description, $duree, $dateSortie, $coutTotal, $boxOffice, $urlAffiche, $idReal, $urlBandeAnnonce = '', $langueOriginale = '') {
        try {
            // Vérifier d'abord si un film avec ce titre existe déjà (en excluant le film en cours de modification)
            if ($this->filmExistsByTitle($titre, $idFilm)) {
                throw new Exception("Un film avec ce titre existe déjà");
            }
            
            $this->getBdd()->beginTransaction();
            
            $sql = "UPDATE Film SET 
                   titre = :titre, 
                   descri = :description, 
                   duree = :duree, 
                   dateSortie = :dateSortie, 
                   coutTotal = :coutTotal, 
                   boxOffice = :boxOffice, 
                   image = :urlAffiche, 
                   idReal = :idReal, 
                   trailer = :urlBandeAnnonce, 
                   langueVO = :langueOriginale 
                   WHERE idFilm = :idFilm";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idFilm', $idFilm, PDO::PARAM_INT);
            $stmt->bindValue(':titre', $titre, PDO::PARAM_STR);
            $stmt->bindValue(':description', $description, PDO::PARAM_STR);
            $stmt->bindValue(':duree', $duree, PDO::PARAM_INT);
            $stmt->bindValue(':dateSortie', $dateSortie, PDO::PARAM_STR);
            $stmt->bindValue(':coutTotal', $coutTotal, PDO::PARAM_STR);
            $stmt->bindValue(':boxOffice', $boxOffice, PDO::PARAM_STR);
            $stmt->bindValue(':urlAffiche', $urlAffiche, PDO::PARAM_STR);
            $stmt->bindValue(':idReal', $idReal, PDO::PARAM_INT);
            $stmt->bindValue(':urlBandeAnnonce', $urlBandeAnnonce, PDO::PARAM_STR);
            $stmt->bindValue(':langueOriginale', $langueOriginale, PDO::PARAM_STR);
            
            $stmt->execute();
            $result = $stmt->rowCount() > 0;
            
            $this->getBdd()->commit();
            
            return $result;
        } catch (PDOException $e) {
            if ($this->getBdd()->inTransaction()) {
                $this->getBdd()->rollBack();
            }
            throw new Exception("Erreur lors de la mise à jour du film : " . $e->getMessage());
        }
    }
    
    /**
     * Supprime un film de la base de données
     * 
     * @param int $idFilm Identifiant du film à supprimer
     * @return bool True si la suppression a réussi, false sinon
     * @throws Exception En cas d'erreur lors de la suppression
     */
    public function deleteFilm($idFilm) {
        try {
            $sql = "DELETE FROM Film WHERE idFilm = :idFilm";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idFilm', $idFilm, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression du film : " . $e->getMessage());
        }
    }
    
    /**
     * Ajoute un genre à un film
     * 
     * @param int $idFilm Identifiant du film
     * @param int $idGenre Identifiant du genre
     * @return bool True si l'ajout a réussi, false sinon
     * @throws Exception En cas d'erreur lors de l'ajout
     */
    public function addGenreToFilm($idFilm, $idGenre) {
        try {
            $sql = "INSERT INTO AppartenirGenre (idFilm, idGenre) VALUES (:idFilm, :idGenre)";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idFilm', $idFilm, PDO::PARAM_INT);
            $stmt->bindValue(':idGenre', $idGenre, PDO::PARAM_INT);
            $stmt->execute();
            
            return true;
        } catch (PDOException $e) {
            // Si l'erreur est due à une violation de contrainte d'unicité, ce n'est pas grave
            if ($e->getCode() == '23000') {
                return true;
            }
            throw new Exception("Erreur lors de l'ajout du genre au film : " . $e->getMessage());
        }
    }
    
    /**
     * Supprime tous les genres d'un film
     * 
     * @param int $idFilm Identifiant du film
     * @return bool True si la suppression a réussi, false sinon
     * @throws Exception En cas d'erreur lors de la suppression
     */
    public function deleteGenresFromFilm($idFilm) {
        try {
            $sql = "DELETE FROM AppartenirGenre WHERE idFilm = :idFilm";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idFilm', $idFilm, PDO::PARAM_INT);
            $stmt->execute();
            
            return true;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression des genres du film : " . $e->getMessage());
        }
    }
    
    /**
     * Ajoute un acteur à un film
     * 
     * @param int $idFilm Identifiant du film
     * @param int $idActeur Identifiant de l'acteur
     * @return bool True si l'ajout a réussi, false sinon
     * @throws Exception En cas d'erreur lors de l'ajout
     */
    public function addActorToFilm($idFilm, $idActeur) {
        try {
            $sql = "INSERT INTO Jouer (idFilm, idActeur) VALUES (:idFilm, :idActeur)";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idFilm', $idFilm, PDO::PARAM_INT);
            $stmt->bindValue(':idActeur', $idActeur, PDO::PARAM_INT);
            $stmt->execute();
            
            return true;
        } catch (PDOException $e) {
            // Si l'erreur est due à une violation de contrainte d'unicité, ce n'est pas grave
            if ($e->getCode() == '23000') {
                return true;
            }
            throw new Exception("Erreur lors de l'ajout de l'acteur au film : " . $e->getMessage());
        }
    }
    
    /**
     * Supprime tous les acteurs d'un film
     * 
     * @param int $idFilm Identifiant du film
     * @return bool True si la suppression a réussi, false sinon
     * @throws Exception En cas d'erreur lors de la suppression
     */
    public function deleteActorsFromFilm($idFilm) {
        try {
            $sql = "DELETE FROM Jouer WHERE idFilm = :idFilm";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idFilm', $idFilm, PDO::PARAM_INT);
            $stmt->execute();
            
            return true;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression des acteurs du film : " . $e->getMessage());
        }
    }

    /**
     * Récupère l'identifiant d'un genre à partir de son libellé
     * 
     * @param string $libelle Libellé du genre
     * @return int|false Identifiant du genre ou false si non trouvé
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getGenreIdByName($libelle) {
        try {
            $sql = "SELECT idGenre FROM Genre WHERE libelle = :libelle";
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':libelle', $libelle, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('Erreur lors de la récupération de l\'ID du genre : ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère les valeurs distinctes d'une colonne
     * 
     * @param string $column Nom de la colonne (genre, langueVO, etc.)
     * @return array Liste des valeurs distinctes
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getDistinctValues($column) {
        try {
            if ($column === 'genre') {
                $sql = "SELECT DISTINCT g.libelle FROM Genre g 
                        INNER JOIN AppartenirGenre ag ON g.idGenre = ag.idGenre 
                        INNER JOIN Film f ON ag.idFilm = f.idFilm 
                        ORDER BY g.libelle";
                $stmt = $this->getBdd()->prepare($sql);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_COLUMN);
            }

            $validColumns = ['langueVO', 'YEAR(dateSortie)', 'titre'];
            if (!in_array($column, $validColumns)) {
                throw new Exception("Colonne non autorisée");
            }

            $sql = "SELECT DISTINCT $column FROM Film WHERE $column IS NOT NULL AND $column != '' ORDER BY $column";
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des valeurs distinctes : " . $e->getMessage());
        }
    }

    /**
     * Récupère les films par genre
     * 
     * @param string $genre Libellé du genre
     * @param int $limit Nombre maximum de films à récupérer
     * @return array Liste des films du genre spécifié
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getFilmsByGenre($genre, $limit = 10) {
        try {
            // Récupérer l'ID du genre à partir de son libellé
            $idGenre = $this->getGenreIdByName($genre);
            
            if (!$idGenre) {
                return [];
            }
            
            $sql = "SELECT f.*,
                    COALESCE(r.nom, 'Non spécifié') as realisateurNom,
                    COALESCE(r.prenom, '') as realisateurPrenom,
                    GROUP_CONCAT(DISTINCT g.libelle) as genres,
                    COALESCE(AVG(a.note), 0) as moyenne_notes,
                    COUNT(a.note) as nombre_notes
                    FROM Film f 
                    INNER JOIN AppartenirGenre ag ON f.idFilm = ag.idFilm
                    LEFT JOIN Realisateur r ON f.idReal = r.idReal
                    LEFT JOIN Genre g ON ag.idGenre = g.idGenre
                    LEFT JOIN Avis a ON f.idFilm = a.idFilm
                    WHERE ag.idGenre = :idGenre
                    GROUP BY f.idFilm, r.nom, r.prenom
                    ORDER BY f.dateSortie DESC
                    LIMIT :limit";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idGenre', $idGenre, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Traitement des résultats pour formater les données
            foreach ($films as &$film) {
                // Formater l'URL de l'image
                $film['image'] = $this->formatImagePath($film['image']);
                
                // Récupérer les genres du film
                $film['genres'] = $this->getGenresByFilmId($film['idFilm']);
            }
            
            return $films;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des films par genre : " . $e->getMessage());
        }
    }

    /**
     * Récupère un film par son ID
     * 
     * @param int $idFilm Identifiant du film à récupérer
     * @return array|false Informations du film ou false si non trouvé
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getFilmById($idFilm) {
        try {
            $sql = "SELECT f.*,
                    COALESCE(r.nom, 'Non spécifié') as realisateurNom,
                    COALESCE(r.prenom, '') as realisateurPrenom,
                    GROUP_CONCAT(DISTINCT g.libelle) as genres,
                    COALESCE(AVG(a.note), 0) as moyenne_notes,
                    COUNT(a.note) as nombre_notes
                FROM film f
                LEFT JOIN realisateur r ON f.idReal = r.idReal
                LEFT JOIN AppartenirGenre ag ON f.idFilm = ag.idFilm
                LEFT JOIN Genre g ON ag.idGenre = g.idGenre
                LEFT JOIN avis a ON f.idFilm = a.idFilm
                WHERE f.idFilm = :idFilm
                GROUP BY f.idFilm";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindParam(':idFilm', $idFilm, PDO::PARAM_INT);
            $stmt->execute();
            
            $film = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($film) {
                // Formater l'URL de l'image (vérifier si elle existe d'abord)
                if (isset($film['urlAffiche'])) {
                    $film['urlAffiche'] = $this->formatImagePath($film['urlAffiche']);
                } else {
                    $film['urlAffiche'] = 'default.jpg';
                }
                
                // Récupérer les genres du film
                $film['genres_array'] = $this->getGenresByFilmId($idFilm);
                
                // Récupérer les acteurs du film
                $film['acteurs'] = $this->getActorsByFilmId($idFilm);
            }
            
            return $film;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la récupération du film : " . $e->getMessage());
        }
    }

    /**
     * Vérifie si un film est dans la watchlist d'un utilisateur
     * 
     * @param int $idUtilisateur Identifiant de l'utilisateur
     * @param int $idFilm Identifiant du film
     * @return bool True si le film est dans la watchlist, false sinon
     * @throws Exception En cas d'erreur lors de la vérification
     */
    public function isInWatchlist($idUtilisateur, $idFilm) {
        try {
            $sql = "SELECT COUNT(*) as count 
                   FROM Watchlist 
                   WHERE idUtilisateur = :idUtilisateur AND idFilm = :idFilm";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
            $stmt->bindValue(':idFilm', $idFilm, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return ($result['count'] > 0);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la vérification de la watchlist : " . $e->getMessage());
        }
    }

    /**
     * Vérifie si un film avec le même titre existe déjà
     * 
     * @param string $titre Titre du film à vérifier
     * @param int $idFilm ID du film à exclure de la vérification (utile pour l'update)
     * @return bool True si un film avec ce titre existe déjà, false sinon
     * @throws Exception En cas d'erreur lors de la vérification
     */
    public function filmExistsByTitle($titre, $idFilm = 0) {
        try {
            $sql = "SELECT COUNT(*) FROM Film WHERE titre = :titre";
            
            // Si on a un ID de film, on l'exclut de la recherche (pour les mises à jour)
            if ($idFilm > 0) {
                $sql .= " AND idFilm != :idFilm";
            }
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':titre', $titre, PDO::PARAM_STR);
            
            if ($idFilm > 0) {
                $stmt->bindValue(':idFilm', $idFilm, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            
            return (int)$stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la vérification de l'existence du film : " . $e->getMessage());
        }
    }

    /**
     * Compte le nombre total de films
     * 
     * @return int Nombre total de films
     * @throws Exception En cas d'erreur lors du comptage
     */
    public function countFilms() {
        try {
            $sql = "SELECT COUNT(*) FROM Film";
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->execute();
            
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du comptage des films : " . $e->getMessage());
        }
    }

    /**
     * Récupère les films récemment ajoutés
     * 
     * @param int $limit Nombre de films à récupérer
     * @return array Liste des films récemment ajoutés
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getRecentFilms($limit = 5) {
        try {
            $sql = "SELECT f.*, r.nom as realisateurNom, r.prenom as realisateurPrenom 
                   FROM Film f 
                   LEFT JOIN Realisateur r ON f.idReal = r.idReal 
                   ORDER BY f.dateSortie DESC 
                   LIMIT :limit";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des films récents : " . $e->getMessage());
        }
    }

    public function searchFilms($query) {
        try {
            $searchTerm = '%' . $query . '%';
            $limit = 20;
            $sql = "SELECT f.*,
                    COALESCE(r.nom, 'Non spécifié') as realisateurNom,
                    COALESCE(r.prenom, '') as realisateurPrenom,
                    GROUP_CONCAT(DISTINCT g.libelle) as genres,
                    COALESCE(AVG(a.note), 0) as moyenne_notes,
                    COUNT(a.note) as nombre_notes
                    FROM Film f 
                    LEFT JOIN Realisateur r ON f.idReal = r.idReal
                    LEFT JOIN AppartenirGenre ag ON f.idFilm = ag.idFilm
                    LEFT JOIN Genre g ON ag.idGenre = g.idGenre
                    LEFT JOIN Avis a ON f.idFilm = a.idFilm
                    WHERE f.titre LIKE :searchTerm 
                    OR f.descri LIKE :searchTerm 
                    OR g.libelle = :searchTerm
                    OR CONCAT(r.nom, ' ', r.prenom) LIKE :searchTerm
                    OR g.libelle = :searchTerm
                    GROUP BY f.idFilm, r.nom, r.prenom
                    ORDER BY f.dateSortie DESC
                    LIMIT :limit";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':searchTerm', $searchTerm, PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($films as &$film) {
                if(is_array($film['genres'] )){
                    foreach($film['genres'] as $genre){
                        $film['genres'] .= $genre;
                    }
                }
            }
            
            return $films;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la recherche de films : " . $e->getMessage());
        }
    }
}

<?php
require_once MODELES_PATH . "PDOModel.php";

/**
 * Classe AvisModele
 * 
 * Gère toutes les opérations liées aux avis sur les films dans la base de données
 * Hérite de PDOModel pour les fonctionnalités de connexion à la base de données
 */
class AvisModele extends PDOModel {
    
    /**
     * Récupère tous les avis
     * 
     * @param int $limit Nombre maximum d'avis à récupérer
     * @param int $offset Position de départ pour la pagination
     * @return array Liste de tous les avis
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getAllAvis($limit = 50, $offset = 0) {
        try {
            $sql = "SELECT a.*, f.titre as filmTitre, u.pseudo as utilisateurPseudo 
                   FROM Avis a 
                   JOIN Film f ON a.idFilm = f.idFilm 
                   JOIN Utilisateur u ON a.idUtilisateur = u.idUtilisateur 
                   ORDER BY a.datePublication DESC 
                   LIMIT :offset, :limit";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des avis : " . $e->getMessage());
        }
    }
    
    /**
     * Récupère un avis par son identifiant
     * 
     * @param int $idAvis Identifiant de l'avis
     * @return array|false Détails de l'avis ou false si non trouvé
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getAvisById($idAvis) {
        try {
            $sql = "SELECT a.*, f.titre as filmTitre, u.pseudo as utilisateurPseudo 
                   FROM Avis a 
                   JOIN Film f ON a.idFilm = f.idFilm 
                   JOIN Utilisateur u ON a.idUtilisateur = u.idUtilisateur 
                   WHERE a.numAvis = :idAvis";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idAvis', $idAvis, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération de l'avis : " . $e->getMessage());
        }
    }
    
    /**
     * Récupère les avis d'un film
     * 
     * @param int $idFilm Identifiant du film
     * @param int $limit Nombre maximum d'avis à récupérer
     * @return array Liste des avis du film
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getAvisByFilm($idFilm, $limit = 10) {
        try {
            $sql = "SELECT a.*, u.pseudo as utilisateurPseudo 
                   FROM Avis a 
                   JOIN Utilisateur u ON a.idUtilisateur = u.idUtilisateur 
                   WHERE a.idFilm = :idFilm 
                   ORDER BY a.datePublication DESC 
                   LIMIT :limit";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idFilm', $idFilm, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des avis du film : " . $e->getMessage());
        }
    }
    
    /**
     * Récupère les avis d'un utilisateur
     * 
     * @param int $idUtilisateur Identifiant de l'utilisateur
     * @param int $limit Nombre maximum d'avis à récupérer
     * @return array Liste des avis de l'utilisateur
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getAvisByUtilisateur($idUtilisateur, $limit = 10) {
        try {
            $sql = "SELECT a.*, f.titre as filmTitre 
                   FROM Avis a 
                   JOIN Film f ON a.idFilm = f.idFilm 
                   WHERE a.idUtilisateur = :idUtilisateur 
                   ORDER BY a.dateAvis DESC 
                   LIMIT :limit";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des avis de l'utilisateur : " . $e->getMessage());
        }
    }
    
    /**
     * Récupère l'avis d'un utilisateur pour un film spécifique
     * 
     * @param int $idUtilisateur Identifiant de l'utilisateur
     * @param int $idFilm Identifiant du film
     * @return array|false Détails de l'avis ou false si non trouvé
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getAvisUtilisateur($idUtilisateur, $idFilm) {
        try {
            $sql = "SELECT a.* 
                   FROM Avis a 
                   WHERE a.idUtilisateur = :idUtilisateur AND a.idFilm = :idFilm";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
            $stmt->bindValue(':idFilm', $idFilm, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération de l'avis de l'utilisateur : " . $e->getMessage());
        }
    }
    
    /**
     * Ajoute un nouvel avis
     * 
     * @param int $idFilm Identifiant du film
     * @param int $idUtilisateur Identifiant de l'utilisateur
     * @param int $note Note attribuée (entre 1 et 5)
     * @param string $commentaire Commentaire de l'avis
     * @return int|false Identifiant de l'avis ajouté ou false en cas d'échec
     * @throws Exception En cas d'erreur lors de l'ajout
     */
    public function addAvis($idFilm, $idUtilisateur, $note, $commentaire) {
        try {
            // Vérifier si l'utilisateur a déjà donné son avis sur ce film
            $sql = "SELECT COUNT(*) FROM Avis WHERE idFilm = :idFilm AND idUtilisateur = :idUtilisateur";
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idFilm', $idFilm, PDO::PARAM_INT);
            $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Vous avez déjà donné votre avis sur ce film.");
            }
            
            // Ajouter l'avis
            $sql = "INSERT INTO Avis (idFilm, idUtilisateur, note, commentaire, datePublication) 
                   VALUES (:idFilm, :idUtilisateur, :note, :commentaire, NOW())";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idFilm', $idFilm, PDO::PARAM_INT);
            $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
            $stmt->bindValue(':note', $note, PDO::PARAM_INT);
            $stmt->bindValue(':commentaire', $commentaire, PDO::PARAM_STR);
            $stmt->execute();
            
            return $this->getBdd()->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'ajout de l'avis : " . $e->getMessage());
        }
    }
    
    /**
     * Met à jour un avis existant
     * 
     * @param int $idAvis Identifiant de l'avis
     * @param int $note Nouvelle note attribuée (entre 1 et 5)
     * @param string $commentaire Nouveau commentaire de l'avis
     * @return bool True si la mise à jour a réussi, false sinon
     * @throws Exception En cas d'erreur lors de la mise à jour
     */
    public function updateAvis($idAvis, $note, $commentaire) {
        try {
            $sql = "UPDATE Avis 
                   SET note = :note, commentaire = :commentaire, datePublication = NOW() 
                   WHERE numAvis = :idAvis";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idAvis', $idAvis, PDO::PARAM_INT);
            $stmt->bindValue(':note', $note, PDO::PARAM_INT);
            $stmt->bindValue(':commentaire', $commentaire, PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour de l'avis : " . $e->getMessage());
        }
    }
    
    /**
     * Supprime un avis
     * 
     * @param int $idAvis Identifiant de l'avis à supprimer
     * @return bool True si la suppression a réussi, false sinon
     * @throws Exception En cas d'erreur lors de la suppression
     */
    public function deleteAvis($idAvis) {
        try {
            $sql = "DELETE FROM Avis WHERE numAvis = :idAvis";
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idAvis', $idAvis, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression de l'avis : " . $e->getMessage());
        }
    }
    
    /**
     * Supprime tous les avis associés à un film
     * 
     * @param int $idFilm Identifiant du film dont les avis doivent être supprimés
     * @return bool True si la suppression a réussi, false sinon
     * @throws Exception En cas d'erreur lors de la suppression
     */
    public function deleteAvisByFilmId($idFilm) {
        try {
            $sql = "DELETE FROM Avis WHERE idFilm = :idFilm";
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idFilm', $idFilm, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression des avis du film : " . $e->getMessage());
        }
    }
    
    /**
     * Compte le nombre total d'avis
     * 
     * @return int Nombre total d'avis
     * @throws Exception En cas d'erreur lors du comptage
     */
    public function countAvis() {
        try {
            $sql = "SELECT COUNT(*) FROM Avis";
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->execute();
            
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du comptage des avis : " . $e->getMessage());
        }
    }
    
    /**
     * Calcule la note moyenne d'un film
     * 
     * @param int $idFilm Identifiant du film
     * @return array Tableau contenant la moyenne et le nombre d'avis
     * @throws Exception En cas d'erreur lors du calcul
     */
    public function getAverageRating($idFilm) {
        try {
            $sql = "SELECT AVG(note) as moyenne, COUNT(*) as nombre 
                   FROM Avis 
                   WHERE idFilm = :idFilm";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idFilm', $idFilm, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            // Vérifier si la moyenne est NULL avant d'appliquer round()
            $result['moyenne'] = $result['moyenne'] !== null ? round($result['moyenne'], 1) : 0;
            
            return $result;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du calcul de la note moyenne : " . $e->getMessage());
        }
    }
    
    /**
     * Récupère les derniers avis ajoutés
     * 
     * @param int $limit Nombre maximum d'avis à récupérer
     * @return array Liste des derniers avis
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getLatestAvis($limit = 5) {
        try {
            $sql = "SELECT a.*, f.titre as filmTitre, u.pseudo as utilisateurPseudo 
                   FROM Avis a 
                   JOIN Film f ON a.idFilm = f.idFilm 
                   JOIN Utilisateur u ON a.idUtilisateur = u.idUtilisateur 
                   ORDER BY a.dateAvis DESC 
                   LIMIT :limit";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des derniers avis : " . $e->getMessage());
        }
    }

    /**
     * Récupère les films avec leur note moyenne, triés par note
     * 
     * @param string $sortOrder Ordre de tri (ASC ou DESC)
     * @param int $limit Nombre maximum de films à récupérer
     * @param array $filters Filtres additionnels (genre, année, etc.)
     * @return array Liste des films avec leur note moyenne
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getFilmsWithAverageRating($sortOrder = 'DESC', $limit = 10, $filters = []) {
        try {
            $sql = "SELECT f.*, 
                   AVG(a.note) as note_moyenne, 
                   COUNT(a.numAvis) as nombre_avis,
                   r.nom as realisateurNom, r.prenom as realisateurPrenom,
                   GROUP_CONCAT(DISTINCT g.libelle) as genres
                   FROM Film f 
                   LEFT JOIN Avis a ON f.idFilm = a.idFilm 
                   LEFT JOIN Realisateur r ON f.idReal = r.idReal
                   LEFT JOIN AppartenirGenre ag ON f.idFilm = ag.idFilm
                   LEFT JOIN Genre g ON ag.idGenre = g.idGenre";
            
            // Ajouter les conditions de filtrage
            $whereConditions = [];
            $params = [];
            
            if (!empty($filters['genre'])) {
                $whereConditions[] = "ag.idGenre = :idGenre";
                $params[':idGenre'] = $filters['genre'];
            }
            
            if (!empty($filters['annee'])) {
                $whereConditions[] = "YEAR(f.dateSortie) = :annee";
                $params[':annee'] = $filters['annee'];
            }

            if (!empty($filters['langue'])) {
                $whereConditions[] = "f.langueVO = :langue";
                $params[':langue'] = $filters['langue'];
            }
            
            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            
            $sql .= " GROUP BY f.idFilm 
                   ORDER BY note_moyenne " . ($sortOrder === 'ASC' ? 'ASC' : 'DESC') . " 
                   LIMIT :limit";
            
            $stmt = $this->getBdd()->prepare($sql);
            
            // Binder les paramètres de filtrage
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }
            
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Traitement des résultats pour formater les données
            foreach ($films as &$film) {
                // Formater l'URL de l'image en utilisant la méthode du FilmModele
                $filmModele = new FilmModele();
                $film['image'] = $filmModele->formatImagePath($film['image']);
                
                // S'assurer que la note moyenne est formatée avec une décimale
                $film['note_moyenne'] = number_format((float)$film['note_moyenne'], 1, '.', '');
                
                // Convertir la chaîne de genres en tableau
                $film['genres'] = !empty($film['genres']) ? explode(',', $film['genres']) : [];
            }
            
            return $films;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des films avec leur note moyenne : " . $e->getMessage());
        }
    }
    
    /**
     * Récupère les films triés par popularité (nombre d'avis)
     * 
     * @param string $sortOrder Ordre de tri (ASC ou DESC)
     * @param int $limit Nombre maximum de films à récupérer
     * @param array $filters Filtres additionnels (genre, année, etc.)
     * @return array Liste des films triés par popularité
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getFilmsByPopularity($sortOrder = 'DESC', $limit = 10, $filters = []) {
        try {
            $sql = "SELECT f.*, 
                   COUNT(a.numAvis) as nombre_avis, 
                   AVG(a.note) as note_moyenne,
                   r.nom as realisateurNom, r.prenom as realisateurPrenom,
                   GROUP_CONCAT(DISTINCT g.libelle) as genres
                   FROM Film f 
                   LEFT JOIN Avis a ON f.idFilm = a.idFilm 
                   LEFT JOIN Realisateur r ON f.idReal = r.idReal
                   LEFT JOIN AppartenirGenre ag ON f.idFilm = ag.idFilm
                   LEFT JOIN Genre g ON ag.idGenre = g.idGenre";
            
            // Ajouter les conditions de filtrage
            $whereConditions = [];
            $params = [];
            
            if (!empty($filters['genre'])) {
                $whereConditions[] = "ag.idGenre = :idGenre";
                $params[':idGenre'] = $filters['genre'];
            }
            
            if (!empty($filters['annee'])) {
                $whereConditions[] = "YEAR(f.dateSortie) = :annee";
                $params[':annee'] = $filters['annee'];
            }

            if (!empty($filters['langue'])) {
                $whereConditions[] = "f.langueVO = :langue";
                $params[':langue'] = $filters['langue'];
            }
            
            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            
            $sql .= " GROUP BY f.idFilm 
                   ORDER BY nombre_avis " . ($sortOrder === 'ASC' ? 'ASC' : 'DESC') . " 
                   LIMIT :limit";
            
            $stmt = $this->getBdd()->prepare($sql);
            
            // Binder les paramètres de filtrage
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }
            
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $films = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Traitement des résultats pour formater les données
            foreach ($films as &$film) {
                // Formater l'URL de l'image en utilisant la méthode du FilmModele
                $filmModele = new FilmModele();
                $film['image'] = $filmModele->formatImagePath($film['image']);
                
                // S'assurer que la note moyenne est formatée avec une décimale
                $film['note_moyenne'] = number_format((float)$film['note_moyenne'], 1, '.', '');
                
                // Convertir la chaîne de genres en tableau
                $film['genres'] = !empty($film['genres']) ? explode(',', $film['genres']) : [];
            }
            
            return $films;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des films par popularité : " . $e->getMessage());
        }
    }
}

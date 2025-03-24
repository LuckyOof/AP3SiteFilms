<?php
require_once MODELES_PATH . "PDOModel.php";

/**
 * Classe GenreModele
 * 
 * Gère toutes les opérations liées aux genres de films dans la base de données
 * Hérite de PDOModel pour les fonctionnalités de connexion à la base de données
 */
class GenreModele extends PDOModel {
    
    /**
     * Récupère tous les genres
     * 
     * @return array Liste de tous les genres
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getAllGenres() {
        try {
            $sql = "SELECT * FROM Genre ORDER BY libelle";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des genres : " . $e->getMessage());
        }
    }
    
    /**
     * Récupère un genre par son identifiant
     * 
     * @param int $idGenre Identifiant du genre
     * @return array|false Détails du genre ou false si non trouvé
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getGenreById($idGenre) {
        try {
            $sql = "SELECT * FROM Genre WHERE idGenre = :idGenre";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idGenre', $idGenre, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération du genre : " . $e->getMessage());
        }
    }
    
    /**
     * Ajoute un nouveau genre
     * 
     * @param string $libelle Libellé du genre
     * @return int|false Identifiant du genre ajouté ou false en cas d'échec
     * @throws Exception En cas d'erreur lors de l'ajout
     */
    public function addGenre($libelle) {
        try {
            $sql = "INSERT INTO Genre (libelle) VALUES (:libelle)";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':libelle', $libelle, PDO::PARAM_STR);
            $stmt->execute();
            
            return $this->getBdd()->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'ajout du genre : " . $e->getMessage());
        }
    }
    
    /**
     * Met à jour un genre existant
     * 
     * @param int $idGenre Identifiant du genre
     * @param string $libelle Nouveau libellé du genre
     * @return bool True si la mise à jour a réussi, false sinon
     * @throws Exception En cas d'erreur lors de la mise à jour
     */
    public function updateGenre($idGenre, $libelle) {
        try {
            $sql = "UPDATE Genre SET libelle = :libelle WHERE idGenre = :idGenre";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idGenre', $idGenre, PDO::PARAM_INT);
            $stmt->bindValue(':libelle', $libelle, PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour du genre : " . $e->getMessage());
        }
    }
    
    /**
     * Supprime un genre
     * 
     * @param int $idGenre Identifiant du genre à supprimer
     * @return bool True si la suppression a réussi, false sinon
     * @throws Exception En cas d'erreur lors de la suppression
     */
    public function deleteGenre($idGenre) {
        try {
            // Vérifier si le genre est utilisé par des films
            $sql = "SELECT COUNT(*) FROM AppartenirGenre WHERE idGenre = :idGenre";
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idGenre', $idGenre, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Impossible de supprimer ce genre car il est associé à un ou plusieurs films.");
            }
            
            // Supprimer le genre
            $sql = "DELETE FROM Genre WHERE idGenre = :idGenre";
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idGenre', $idGenre, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression du genre : " . $e->getMessage());
        }
    }
    
    /**
     * Compte le nombre total de genres
     * 
     * @return int Nombre total de genres
     * @throws Exception En cas d'erreur lors du comptage
     */
    public function countGenres() {
        try {
            $sql = "SELECT COUNT(*) FROM Genre";
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->execute();
            
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du comptage des genres : " . $e->getMessage());
        }
    }
    
    /**
     * Récupère les films associés à un genre
     * 
     * @param int $idGenre Identifiant du genre
     * @return array Liste des films associés au genre
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getFilmsByGenre($idGenre) {
        try {
            $sql = "SELECT f.* FROM Film f 
                    JOIN AppartenirGenre ag ON f.idFilm = ag.idFilm 
                    WHERE ag.idGenre = :idGenre";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idGenre', $idGenre, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des films par genre : " . $e->getMessage());
        }
    }
}

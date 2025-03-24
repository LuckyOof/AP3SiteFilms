<?php
require_once MODELES_PATH . "PDOModel.php";

/**
 * Classe ActeurModele
 * 
 * Gu00e8re toutes les opu00e9rations liu00e9es aux acteurs de films dans la base de donnu00e9es
 * Hu00e9rite de PDOModel pour les fonctionnalitu00e9s de connexion u00e0 la base de donnu00e9es
 */
class ActeurModele extends PDOModel {
    
    /**
     * Ru00e9cupu00e8re tous les acteurs
     * 
     * @return array Liste de tous les acteurs
     * @throws Exception En cas d'erreur lors de la ru00e9cupu00e9ration
     */
    public function getAllActeurs() {
        try {
            $sql = "SELECT * FROM Acteur ORDER BY nom, prenom";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la ru00e9cupu00e9ration des acteurs : " . $e->getMessage());
        }
    }
    
    /**
     * Ru00e9cupu00e8re un acteur par son identifiant
     * 
     * @param int $idActeur Identifiant de l'acteur
     * @return array|false Du00e9tails de l'acteur ou false si non trouvu00e9
     * @throws Exception En cas d'erreur lors de la ru00e9cupu00e9ration
     */
    public function getActeurById($idActeur) {
        try {
            $sql = "SELECT * FROM Acteur WHERE idActeur = :idActeur";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idActeur', $idActeur, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la ru00e9cupu00e9ration de l'acteur : " . $e->getMessage());
        }
    }
    
    /**
     * Ajoute un nouvel acteur
     * 
     * @param string $nom Nom de l'acteur
     * @param string $prenom Pru00e9nom de l'acteur
     * @param string $dateNaissance Date de naissance de l'acteur (format YYYY-MM-DD)
     * @param string $nationalite Nationalitu00e9 de l'acteur
     * @param string $photo URL de la photo de l'acteur
     * @return int|false Identifiant de l'acteur ajoutu00e9 ou false en cas d'u00e9chec
     * @throws Exception En cas d'erreur lors de l'ajout
     */
    public function addActeur($nom, $prenom, $dateNaissance = null, $nationalite = null, $photo = null) {
        try {
            $sql = "INSERT INTO Acteur (nom, prenom, dateNaissance, nationalite, photo) 
                   VALUES (:nom, :prenom, :dateNaissance, :nationalite, :photo)";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindValue(':prenom', $prenom, PDO::PARAM_STR);
            $stmt->bindValue(':dateNaissance', $dateNaissance, PDO::PARAM_STR);
            $stmt->bindValue(':nationalite', $nationalite, PDO::PARAM_STR);
            $stmt->bindValue(':photo', $photo, PDO::PARAM_STR);
            $stmt->execute();
            
            return $this->getBdd()->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'ajout de l'acteur : " . $e->getMessage());
        }
    }
    
    /**
     * Met u00e0 jour un acteur existant
     * 
     * @param int $idActeur Identifiant de l'acteur
     * @param string $nom Nom de l'acteur
     * @param string $prenom Pru00e9nom de l'acteur
     * @param string $dateNaissance Date de naissance de l'acteur (format YYYY-MM-DD)
     * @param string $nationalite Nationalitu00e9 de l'acteur
     * @param string $photo URL de la photo de l'acteur
     * @return bool True si la mise u00e0 jour a ru00e9ussi, false sinon
     * @throws Exception En cas d'erreur lors de la mise u00e0 jour
     */
    public function updateActeur($idActeur, $nom, $prenom, $dateNaissance = null, $nationalite = null, $photo = null) {
        try {
            $sql = "UPDATE Acteur 
                   SET nom = :nom, prenom = :prenom, dateNaissance = :dateNaissance, 
                       nationalite = :nationalite, photo = :photo 
                   WHERE idActeur = :idActeur";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idActeur', $idActeur, PDO::PARAM_INT);
            $stmt->bindValue(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindValue(':prenom', $prenom, PDO::PARAM_STR);
            $stmt->bindValue(':dateNaissance', $dateNaissance, PDO::PARAM_STR);
            $stmt->bindValue(':nationalite', $nationalite, PDO::PARAM_STR);
            $stmt->bindValue(':photo', $photo, PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise u00e0 jour de l'acteur : " . $e->getMessage());
        }
    }
    
    /**
     * Supprime un acteur
     * 
     * @param int $idActeur Identifiant de l'acteur u00e0 supprimer
     * @return bool True si la suppression a ru00e9ussi, false sinon
     * @throws Exception En cas d'erreur lors de la suppression
     */
    public function deleteActeur($idActeur) {
        try {
            // Vu00e9rifier si l'acteur joue dans des films
            $sql = "SELECT COUNT(*) FROM Jouer WHERE idActeur = :idActeur";
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idActeur', $idActeur, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Impossible de supprimer cet acteur car il est associu00e9 u00e0 un ou plusieurs films.");
            }
            
            // Supprimer l'acteur
            $sql = "DELETE FROM Acteur WHERE idActeur = :idActeur";
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idActeur', $idActeur, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression de l'acteur : " . $e->getMessage());
        }
    }
    
    /**
     * Compte le nombre total d'acteurs
     * 
     * @return int Nombre total d'acteurs
     * @throws Exception En cas d'erreur lors du comptage
     */
    public function countActeurs() {
        try {
            $sql = "SELECT COUNT(*) FROM Acteur";
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->execute();
            
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du comptage des acteurs : " . $e->getMessage());
        }
    }
    
    /**
     * Ru00e9cupu00e8re les films dans lesquels joue un acteur
     * 
     * @param int $idActeur Identifiant de l'acteur
     * @return array Liste des films dans lesquels joue l'acteur
     * @throws Exception En cas d'erreur lors de la ru00e9cupu00e9ration
     */
    public function getFilmsByActeur($idActeur) {
        try {
            $sql = "SELECT f.* FROM Film f 
                    JOIN Jouer j ON f.idFilm = j.idFilm 
                    WHERE j.idActeur = :idActeur";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idActeur', $idActeur, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la ru00e9cupu00e9ration des films par acteur : " . $e->getMessage());
        }
    }
}

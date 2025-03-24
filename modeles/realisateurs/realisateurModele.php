<?php
require_once MODELES_PATH . "PDOModel.php";

/**
 * Classe RealisateurModele
 * 
 * Gu00e8re toutes les opu00e9rations liu00e9es aux ru00e9alisateurs de films dans la base de donnu00e9es
 * Hu00e9rite de PDOModel pour les fonctionnalitu00e9s de connexion u00e0 la base de donnu00e9es
 */
class RealisateurModele extends PDOModel {
    
    /**
     * Ru00e9cupu00e8re tous les ru00e9alisateurs
     * 
     * @return array Liste de tous les ru00e9alisateurs
     * @throws Exception En cas d'erreur lors de la ru00e9cupu00e9ration
     */
    public function getAllRealisateurs() {
        try {
            $sql = "SELECT * FROM Realisateur ORDER BY nom, prenom";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la ru00e9cupu00e9ration des ru00e9alisateurs : " . $e->getMessage());
        }
    }
    
    /**
     * Ru00e9cupu00e8re un ru00e9alisateur par son identifiant
     * 
     * @param int $idReal Identifiant du ru00e9alisateur u00e0 ru00e9cupu00e9rer
     * @return array|false Du00e9tails du ru00e9alisateur ou false si non trouvu00e9
     * @throws Exception En cas d'erreur lors de la ru00e9cupu00e9ration
     */
    public function getRealisateurById($idReal) {
        try {
            $sql = "SELECT * FROM Realisateur WHERE idReal = :idReal";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idReal', $idReal, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la ru00e9cupu00e9ration du ru00e9alisateur : " . $e->getMessage());
        }
    }
    
    /**
     * Ajoute un nouveau ru00e9alisateur dans la base de donnu00e9es
     * 
     * @param string $nom Nom du ru00e9alisateur
     * @param string $prenom Pru00e9nom du ru00e9alisateur
     * @param string $dateNaissance Date de naissance au format YYYY-MM-DD
     * @param string $nationalite Nationalitu00e9 du ru00e9alisateur
     * @param string $biographie Biographie du ru00e9alisateur
     * @param string $urlPhoto URL de la photo du ru00e9alisateur
     * @return int|false Identifiant du ru00e9alisateur ajoute ou false en cas d'u00e9chec
     * @throws Exception En cas d'erreur lors de l'ajout
     */
    public function addRealisateur($nom, $prenom, $dateNaissance, $nationalite, $biographie = '', $urlPhoto = '') {
        try {
            $sql = "INSERT INTO Realisateur (nom, prenom, dateNaissance, nationalite, biographie, urlPhoto) 
                   VALUES (:nom, :prenom, :dateNaissance, :nationalite, :biographie, :urlPhoto)";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindValue(':prenom', $prenom, PDO::PARAM_STR);
            $stmt->bindValue(':dateNaissance', $dateNaissance, PDO::PARAM_STR);
            $stmt->bindValue(':nationalite', $nationalite, PDO::PARAM_STR);
            $stmt->bindValue(':biographie', $biographie, PDO::PARAM_STR);
            $stmt->bindValue(':urlPhoto', $urlPhoto, PDO::PARAM_STR);
            $stmt->execute();
            
            return $this->getBdd()->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'ajout du ru00e9alisateur : " . $e->getMessage());
        }
    }
    
    /**
     * Met u00e0 jour un ru00e9alisateur existant
     * 
     * @param int $idReal Identifiant du ru00e9alisateur
     * @param string $nom Nom du ru00e9alisateur
     * @param string $prenom Pru00e9nom du ru00e9alisateur
     * @param string $dateNaissance Date de naissance du ru00e9alisateur (format YYYY-MM-DD)
     * @param string $nationalite Nationalitu00e9 du ru00e9alisateur
     * @param string $biographie Biographie du ru00e9alisateur
     * @param string $urlPhoto URL de la photo du ru00e9alisateur
     * @return bool True si la mise u00e0 jour a ru00e9ussi, false sinon
     * @throws Exception En cas d'erreur lors de la mise u00e0 jour
     */
    public function updateRealisateur($idReal, $nom, $prenom, $dateNaissance = null, $nationalite = null, $biographie = null, $urlPhoto = null) {
        try {
            $sql = "UPDATE Realisateur 
                   SET nom = :nom, prenom = :prenom, dateNaissance = :dateNaissance, 
                       nationalite = :nationalite, biographie = :biographie, urlPhoto = :urlPhoto 
                   WHERE idReal = :idReal";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idReal', $idReal, PDO::PARAM_INT);
            $stmt->bindValue(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindValue(':prenom', $prenom, PDO::PARAM_STR);
            $stmt->bindValue(':dateNaissance', $dateNaissance, PDO::PARAM_STR);
            $stmt->bindValue(':nationalite', $nationalite, PDO::PARAM_STR);
            $stmt->bindValue(':biographie', $biographie, PDO::PARAM_STR);
            $stmt->bindValue(':urlPhoto', $urlPhoto, PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise u00e0 jour du ru00e9alisateur : " . $e->getMessage());
        }
    }
    
    /**
     * Supprime un ru00e9alisateur
     * 
     * @param int $idReal Identifiant du ru00e9alisateur u00e0 supprimer
     * @return bool True si la suppression a ru00e9ussi, false sinon
     * @throws Exception En cas d'erreur lors de la suppression
     */
    public function deleteRealisateur($idReal) {
        try {
            // Vu00e9rifier si le ru00e9alisateur a ru00e9alisu00e9 des films
            $sql = "SELECT COUNT(*) FROM Film WHERE idReal = :idReal";
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idReal', $idReal, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Impossible de supprimer ce ru00e9alisateur car il est associu00e9 u00e0 un ou plusieurs films.");
            }
            
            // Supprimer le ru00e9alisateur
            $sql = "DELETE FROM Realisateur WHERE idReal = :idReal";
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idReal', $idReal, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression du ru00e9alisateur : " . $e->getMessage());
        }
    }
    
    /**
     * Compte le nombre total de ru00e9alisateurs
     * 
     * @return int Nombre total de ru00e9alisateurs
     * @throws Exception En cas d'erreur lors du comptage
     */
    public function countRealisateurs() {
        try {
            $sql = "SELECT COUNT(*) FROM Realisateur";
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->execute();
            
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du comptage des ru00e9alisateurs : " . $e->getMessage());
        }
    }
    
    /**
     * Ru00e9cupu00e8re les films ru00e9alisu00e9s par un ru00e9alisateur
     * 
     * @param int $idReal Identifiant du ru00e9alisateur
     * @return array Liste des films ru00e9alisu00e9s par le ru00e9alisateur
     * @throws Exception En cas d'erreur lors de la ru00e9cupu00e9ration
     */
    public function getFilmsByRealisateur($idReal) {
        try {
            $sql = "SELECT * FROM Film WHERE idReal = :idReal";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idReal', $idReal, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la ru00e9cupu00e9ration des films par ru00e9alisateur : " . $e->getMessage());
        }
    }
}

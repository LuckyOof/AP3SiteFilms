<?php
require_once MODELES_PATH . "PDOModel.php";

/**
 * Classe RealisateurModele
 * 
 * Gère toutes les opérations liées aux réalisateurs de films dans la base de données
 * Hérite de PDOModel pour les fonctionnalités de connexion à la base de données
 */
class RealisateurModele extends PDOModel {
    
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
     * Récupère un réalisateur par son identifiant
     * 
     * @param int $idReal Identifiant du réalisateur à récupérer
     * @return array|false Details du réalisateur ou false si non trouvé
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getRealisateurById($idReal) {
        try {
            $sql = "SELECT * FROM Realisateur WHERE idReal = :idReal";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idReal', $idReal, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération du réalisateur : " . $e->getMessage());
        }
    }
    
    /**
     * Ajoute un nouveau réalisateur dans la base de données
     * 
     * @param string $nom Nom du réalisateur
     * @param string $prenom Prénom du réalisateur
     * @param string $dateNaissance Date de naissance au format YYYY-MM-DD
     * @param string $nationalite Nationalité du réalisateur
     * @return int|false Identifiant du réalisateur ajouté ou false en cas d'échec
     * @throws Exception En cas d'erreur lors de l'ajout
     */
    public function addRealisateur($nom, $prenom, $dateNaissance, $nationalite) {
        try {
            $sql = "INSERT INTO Realisateur (nom, prenom, dateNaissance, nationalite) 
                   VALUES (:nom, :prenom, :dateNaissance, :nationalite)";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindValue(':prenom', $prenom, PDO::PARAM_STR);
            $stmt->bindValue(':dateNaissance', $dateNaissance, PDO::PARAM_STR);
            $stmt->bindValue(':nationalite', $nationalite, PDO::PARAM_STR);
            $stmt->execute();
            
            return $this->getBdd()->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de l'ajout du réalisateur : " . $e->getMessage());
        }
    }
    
    /**
     * Met à jour un réalisateur existant
     * 
     * @param int $idReal Identifiant du réalisateur
     * @param string $nom Nom du réalisateur
     * @param string $prenom Prénom du réalisateur
     * @param string $dateNaissance Date de naissance du réalisateur (format YYYY-MM-DD)
     * @param string $nationalite Nationalité du réalisateur
     * @return bool True si la mise à jour a réussi, false sinon
     * @throws Exception En cas d'erreur lors de la mise à jour
     */
    public function updateRealisateur($idReal, $nom, $prenom, $dateNaissance = null, $nationalite = null) {
        try {
            $sql = "UPDATE Realisateur 
                   SET nom = :nom, prenom = :prenom, dateNaissance = :dateNaissance, 
                       nationalite = :nationalite 
                   WHERE idReal = :idReal";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idReal', $idReal, PDO::PARAM_INT);
            $stmt->bindValue(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindValue(':prenom', $prenom, PDO::PARAM_STR);
            $stmt->bindValue(':dateNaissance', $dateNaissance, PDO::PARAM_STR);
            $stmt->bindValue(':nationalite', $nationalite, PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour du réalisateur : " . $e->getMessage());
        }
    }
    
    /**
     * Supprime un réalisateur
     * 
     * @param int $idReal Identifiant du réalisateur à supprimer
     * @return bool True si la suppression a réussi, false sinon
     * @throws Exception En cas d'erreur lors de la suppression
     */
    public function deleteRealisateur($idReal) {
        try {
            // Vérifier si le réalisateur a réalisé des films
            $sql = "SELECT COUNT(*) FROM Film WHERE idReal = :idReal";
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idReal', $idReal, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Impossible de supprimer ce réalisateur car il est associé à un ou plusieurs films.");
            }
            
            // Supprimer le réalisateur
            $sql = "DELETE FROM Realisateur WHERE idReal = :idReal";
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idReal', $idReal, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la suppression du réalisateur : " . $e->getMessage());
        }
    }
    
    /**
     * Compte le nombre total de réalisateurs
     * 
     * @return int Nombre total de réalisateurs
     * @throws Exception En cas d'erreur lors du comptage
     */
    public function countRealisateurs() {
        try {
            $sql = "SELECT COUNT(*) FROM Realisateur";
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->execute();
            
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du comptage des réalisateurs : " . $e->getMessage());
        }
    }
    
    /**
     * Récupère les films réalisés par un réalisateur
     * 
     * @param int $idReal Identifiant du réalisateur
     * @return array Liste des films réalisés par le réalisateur
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getFilmsByRealisateur($idReal) {
        try {
            $sql = "SELECT * FROM Film WHERE idReal = :idReal";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(':idReal', $idReal, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des films par réalisateur : " . $e->getMessage());
        }
    }
}

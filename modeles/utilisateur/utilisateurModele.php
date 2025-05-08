<?php
require_once MODELES_PATH . "PDOModel.php";

class UtilisateurModele extends PDOModel {
    /**
     * Récupère tous les utilisateurs
     * 
     * @return array Liste de tous les utilisateurs
     * @throws Exception En cas d'erreur lors de la récupération
     */
    public function getAllUtilisateurs() {
        try {
            $sql = "SELECT * FROM utilisateur ORDER BY nom, prenom";
            
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération des utilisateurs : " . $e->getMessage());
        }
    }

    public function getUserByEmail($email) {
        $sql = "SELECT * FROM utilisateur WHERE email = :email";
        $req = $this->getBdd()->prepare($sql);
        $req->bindValue(":email", $email, PDO::PARAM_STR);
        $req->execute();
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserById($id) {
        $sql = "SELECT * FROM utilisateur WHERE idUtilisateur = :id";
        $req = $this->getBdd()->prepare($sql);
        $req->bindValue(":id", $id, PDO::PARAM_INT);
        $req->execute();
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    public function createUser($nom, $prenom, $pseudo, $email, $motDePasse, $estAdmin = true) {
        // Hachage du mot de passe avec bcrypt
        // $motDePasseHash = password_hash($motDePasse, PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO utilisateur (nom, prenom, pseudo, email, motDePasse, estAdmin) 
                VALUES (:nom, :prenom, :pseudo, :email, :motDePasse, false)";
        $req = $this->getBdd()->prepare($sql);
        $req->bindValue(":nom", $nom, PDO::PARAM_STR);
        $req->bindValue(":prenom", $prenom, PDO::PARAM_STR);
        $req->bindValue(":pseudo", $pseudo, PDO::PARAM_STR);
        $req->bindValue(":email", $email, PDO::PARAM_STR);
        $req->bindValue(":motDePasse", $motDePasse, PDO::PARAM_STR);
        return $req->execute();
    }

    public function updateUser($id, $nom, $prenom, $pseudo, $email) {
        $sql = "UPDATE utilisateur 
                SET nom = :nom, prenom = :prenom, pseudo = :pseudo, email = :email 
                WHERE idUtilisateur = :id";
        $req = $this->getBdd()->prepare($sql);
        $req->bindValue(":id", $id, PDO::PARAM_INT);
        $req->bindValue(":nom", $nom, PDO::PARAM_STR);
        $req->bindValue(":prenom", $prenom, PDO::PARAM_STR);
        $req->bindValue(":pseudo", $pseudo, PDO::PARAM_STR);
        $req->bindValue(":email", $email, PDO::PARAM_STR);
        return $req->execute();
    }

    public function updatePassword($id, $newPassword) {
        // Hachage du nouveau mot de passe avec bcrypt
        // $motDePasseHash = password_hash($newPassword, PASSWORD_BCRYPT);
        
        $sql = "UPDATE utilisateur SET motDePasse = :motDePasse WHERE idUtilisateur = :id";
        $req = $this->getBdd()->prepare($sql);
        $req->bindValue(":id", $id, PDO::PARAM_INT);
        $req->bindValue(":motDePasse", $newPassword, PDO::PARAM_STR);
        return $req->execute();
    }

    public function verifyPassword($email, $password) {
        $user = $this->getUserByEmail($email);
        if (!$user) {
            return false;
        }
        
        return $password === $user['motDePasse'];
    }

    public function getUserReviews($userId) {
        $sql = "SELECT a.*, f.titre as film_titre, f.image as film_image 
                FROM Avis a 
                JOIN Film f ON a.idFilm = f.idFilm 
                WHERE a.idUtilisateur = :userId 
                ORDER BY a.datePublication DESC";
        $req = $this->getBdd()->prepare($sql);
        $req->bindValue(":userId", $userId, PDO::PARAM_INT);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getWatchlist($userId, $sortBy = 'dateAjout', $sortOrder = 'DESC', $filters = []) {
        try {
            $sql = "SELECT f.*, w.dateAjout as dateAjoutWatchlist 
                    FROM Film f 
                    INNER JOIN Watchlist w ON f.idFilm = w.idFilm 
                    WHERE w.idUtilisateur = :userId";

            // Appliquer les filtres
            if (!empty($filters)) {
                if (isset($filters['genre']) && !empty($filters['genre'])) {
                    $sql .= " AND f.genre = :genre";
                }
                if (isset($filters['dureeMin'])) {
                    $sql .= " AND f.duree >= :dureeMin";
                }
                if (isset($filters['dureeMax'])) {
                    $sql .= " AND f.duree <= :dureeMax";
                }
                if (isset($filters['annee'])) {
                    $sql .= " AND YEAR(f.dateSortie) = :annee";
                }
                if (isset($filters['langue']) && !empty($filters['langue'])) {
                    $sql .= " AND f.langueOriginale = :langue";
                }
            }

            // Appliquer le tri
            $validSortColumns = ['titre', 'dateSortie', 'duree', 'dateAjoutWatchlist'];
            $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'dateAjoutWatchlist';
            $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
            
            $sql .= " ORDER BY " . ($sortBy === 'dateAjoutWatchlist' ? 'w.dateAjout' : "f.$sortBy") . " $sortOrder";

            $stmt = $this->getBdd()->prepare($sql);
            $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);

            // Bind des valeurs de filtres
            if (!empty($filters)) {
                if (isset($filters['genre']) && !empty($filters['genre'])) {
                    $stmt->bindValue(":genre", $filters['genre'], PDO::PARAM_STR);
                }
                if (isset($filters['dureeMin'])) {
                    $stmt->bindValue(":dureeMin", $filters['dureeMin'], PDO::PARAM_INT);
                }
                if (isset($filters['dureeMax'])) {
                    $stmt->bindValue(":dureeMax", $filters['dureeMax'], PDO::PARAM_INT);
                }
                if (isset($filters['annee'])) {
                    $stmt->bindValue(":annee", $filters['annee'], PDO::PARAM_INT);
                }
                if (isset($filters['langue']) && !empty($filters['langue'])) {
                    $stmt->bindValue(":langue", $filters['langue'], PDO::PARAM_STR);
                }
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la récupération de la watchlist : " . $e->getMessage());
        }
    }

    public function addToWatchlist($userId, $filmId) {
        try {
            $sql = "INSERT INTO Watchlist (idUtilisateur, idFilm, dateAjout) VALUES (:userId, :filmId, CURDATE())";
            $req = $this->getBdd()->prepare($sql);
            $req->bindValue(":userId", $userId, PDO::PARAM_INT);
            $req->bindValue(":filmId", $filmId, PDO::PARAM_INT);
            return $req->execute();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Code d'erreur pour violation de clé unique
                return false; // Film déjà dans la watchlist
            }
            throw $e;
        }
    }

    public function removeFromWatchlist($userId, $filmId) {
        $sql = "DELETE FROM Watchlist WHERE idUtilisateur = :userId AND idFilm = :filmId";
        $req = $this->getBdd()->prepare($sql);
        $req->bindValue(":userId", $userId, PDO::PARAM_INT);
        $req->bindValue(":filmId", $filmId, PDO::PARAM_INT);
        return $req->execute();
    }

    public function isInWatchlist($userId, $filmId) {
        $sql = "SELECT COUNT(*) FROM Watchlist WHERE idUtilisateur = :userId AND idFilm = :filmId";
        $req = $this->getBdd()->prepare($sql);
        $req->bindValue(":userId", $userId, PDO::PARAM_INT);
        $req->bindValue(":filmId", $filmId, PDO::PARAM_INT);
        $req->execute();
        return (int)$req->fetchColumn() > 0;
    }

    /**
     * Compte le nombre total d'utilisateurs
     * 
     * @return int Nombre total d'utilisateurs
     * @throws Exception En cas d'erreur lors du comptage
     */
    public function countUtilisateurs() {
        try {
            $sql = "SELECT COUNT(*) FROM utilisateur";
            $stmt = $this->getBdd()->prepare($sql);
            $stmt->execute();
            
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception("Erreur lors du comptage des utilisateurs : " . $e->getMessage());
        }
    }
}

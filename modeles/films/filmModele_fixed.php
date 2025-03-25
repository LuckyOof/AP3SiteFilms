<?php
require_once 'modeles/Modele.php';

class FilmModele extends Modele {
    // Autres méthodes existantes...
    
    /**
     * Ajoute un nouveau film dans la base de données avec tous ses détails
     * 
     * @param string $titre Titre du film
     * @param string $description Description du film
     * @param int $duree Duru00e9e du film en minutes
     * @param string $dateSortie Date de sortie au format YYYY-MM-DD
     * @param float $coutTotal Budget du film
     * @param float $boxOffice Recette du film
     * @param string $urlAffiche URL de l'affiche du film
     * @param int $idReal Identifiant du ru00e9alisateur
     * @param string $urlBandeAnnonce URL de la bande-annonce
     * @param string $langueOriginale Langue originale du film
     * @return int|false Identifiant du film ajoutu00e9 ou false en cas d'u00e9chec
     * @throws Exception En cas d'erreur lors de l'ajout
     */
    public function addFilm($titre, $description, $duree, $dateSortie, $coutTotal, $boxOffice, $urlAffiche, $idReal, $urlBandeAnnonce = '', $langueOriginale = '') {
        try {
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
            
            return $this->getBdd()->lastInsertId();
        } catch (PDOException $e) {
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
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour du film : " . $e->getMessage());
        }
    }
    
}

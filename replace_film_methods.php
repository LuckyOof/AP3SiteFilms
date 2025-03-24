<?php
// Script pour remplacer complu00e8tement les mu00e9thodes addFilm et updateFilm

// Chemin vers le fichier u00e0 modifier
$filePath = __DIR__ . '/modeles/films/filmModele.php';

// Faire une sauvegarde du fichier original
file_put_contents($filePath . '.backup_' . date('Y-m-d-H-i-s'), file_get_contents($filePath));

// Contenu de remplacement pour la mu00e9thode updateFilm
$updateFilmMethod = <<<'EOD'
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
EOD;

// Contenu de remplacement pour la mu00e9thode addFilm
$addFilmMethod = <<<'EOD'
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
EOD;

// Lire le contenu du fichier
$content = file_get_contents($filePath);

// Remplacer la mu00e9thode updateFilm
$pattern = '/public function updateFilm\([^{]*\{[^}]*\}/s';
$content = preg_replace($pattern, trim($updateFilmMethod), $content);

// Remplacer la mu00e9thode addFilm
$pattern = '/public function addFilm\([^{]*\{[^}]*\}/s';
$content = preg_replace($pattern, trim($addFilmMethod), $content);

// u00c9crire le contenu modifiu00e9 dans le fichier
file_put_contents($filePath, $content);

echo "<h1>Remplacement des mu00e9thodes dans filmModele.php</h1>";
echo "<p>Les mu00e9thodes addFilm et updateFilm ont u00e9tu00e9 remplaci00e9es avec succu00e8s.</p>";
echo "<p>Les noms de champs ont u00e9tu00e9 corrigu00e9s pour correspondre u00e0 la structure de la base de donnu00e9es :</p>";
echo "<ul>";
echo "<li>'description' → 'descri'</li>";
echo "<li>'budget' → 'coutTotal'</li>";
echo "<li>'recette' → 'boxOffice'</li>";
echo "<li>'urlAffiche' → 'image'</li>";
echo "<li>'urlBandeAnnonce' → 'trailer'</li>";
echo "<li>'langueOriginale' → 'langueVO'</li>";
echo "</ul>";
echo "<p><a href='admin/films'>Retour u00e0 la liste des films</a></p>";

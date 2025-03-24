<?php
// Script pour corriger automatiquement les problu00e8mes dans filmModele.php

// Chemin vers le fichier u00e0 corriger
$filePath = __DIR__ . '/modeles/films/filmModele.php';

// Lire le contenu du fichier
$content = file_get_contents($filePath);

// Faire une sauvegarde du fichier original
$backupPath = $filePath . '.backup.' . date('Y-m-d-H-i-s');
file_put_contents($backupPath, $content);
echo "Sauvegarde cru00e9u00e9e: $backupPath<br>";

// Remplacer les noms de colonnes incorrects dans la mu00e9thode updateFilm
$content = preg_replace(
    '/\$sql = "UPDATE Film SET[^"]*titre = :titre,[^"]*description = :description,[^"]*duree = :duree,[^"]*dateSortie = :dateSortie,[^"]*budget = :coutTotal,[^"]*recette = :boxOffice,[^"]*image = :urlAffiche,[^"]*idReal = :idReal,[^"]*trailer = :urlBandeAnnonce,[^"]*langueVO = :langueOriginale[^"]*WHERE idFilm = :idFilm";/s',
    '$sql = "UPDATE Film SET \n                   titre = :titre, \n                   descri = :description, \n                   duree = :duree, \n                   dateSortie = :dateSortie, \n                   coutTotal = :coutTotal, \n                   boxOffice = :boxOffice, \n                   image = :urlAffiche, \n                   idReal = :idReal, \n                   trailer = :urlBandeAnnonce, \n                   langueVO = :langueOriginale \n                   WHERE idFilm = :idFilm";',
    $content
);

// Remplacer les noms de colonnes incorrects dans la mu00e9thode addFilm
$content = preg_replace(
    '/\$sql = "INSERT INTO Film \(titre, description, duree, dateSortie, budget, recette, urlAffiche, idReal, urlBandeAnnonce, langueOriginale\)[^"]*VALUES/s',
    '$sql = "INSERT INTO Film (titre, descri, duree, dateSortie, coutTotal, boxOffice, image, idReal, trailer, langueVO) \n                   VALUES',
    $content
);

// Mettre u00e0 jour les paramu00e8tres dans la mu00e9thode addFilm
$content = preg_replace(
    '/public function addFilm\(\$titre, \$description, \$duree, \$dateSortie, \$budget, \$recette, \$urlAffiche, \$idReal, \$urlBandeAnnonce = \'\', \$langueOriginale = \'\'/s',
    'public function addFilm($titre, $description, $duree, $dateSortie, $coutTotal, $boxOffice, $urlAffiche, $idReal, $urlBandeAnnonce = \'\', $langueOriginale = \'\',',
    $content
);

// Mettre u00e0 jour les bindValue dans addFilm
$content = preg_replace(
    '/\$stmt->bindValue\(\'\:budget\', \$budget, PDO::PARAM_STR\);[^\n]*\n[^\n]*\$stmt->bindValue\(\'\:recette\', \$recette, PDO::PARAM_STR\);/s',
    '$stmt->bindValue(\':coutTotal\', $coutTotal, PDO::PARAM_STR);\n            $stmt->bindValue(\':boxOffice\', $boxOffice, PDO::PARAM_STR);',
    $content
);

// u00c9crire le contenu modifiu00e9 dans le fichier
file_put_contents($filePath, $content);

echo "Les corrections ont u00e9tu00e9 appliquu00e9es avec succu00e8s au fichier filmModele.php.<br>";
echo "<a href='admin/films'>Retour u00e0 la liste des films</a>";

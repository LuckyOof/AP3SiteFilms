<?php
// Script pour corriger les noms de champs dans filmModele.php

// Chemin vers le fichier u00e0 modifier
$filePath = __DIR__ . '/modeles/films/filmModele.php';

// Lire le contenu du fichier
$content = file_get_contents($filePath);

// Faire une sauvegarde du fichier original
file_put_contents($filePath . '.backup', $content);

// Remplacer les noms de colonnes incorrects dans la mu00e9thode updateFilm
$content = str_replace(
    "description = :description", 
    "descri = :description", 
    $content
);

$content = str_replace(
    "budget = :coutTotal", 
    "coutTotal = :coutTotal", 
    $content
);

$content = str_replace(
    "recette = :boxOffice", 
    "boxOffice = :boxOffice", 
    $content
);

// Remplacer les noms de colonnes incorrects dans la mu00e9thode addFilm
$content = str_replace(
    "INSERT INTO Film (titre, description, duree, dateSortie, budget, recette, urlAffiche, idReal, urlBandeAnnonce, langueOriginale)", 
    "INSERT INTO Film (titre, descri, duree, dateSortie, coutTotal, boxOffice, image, idReal, trailer, langueVO)", 
    $content
);

// Mettre u00e0 jour les paramu00e8tres dans la mu00e9thode addFilm
$content = str_replace(
    "public function addFilm($titre, $description, $duree, $dateSortie, $budget, $recette, $urlAffiche, $idReal, $urlBandeAnnonce = '', $langueOriginale = '')", 
    "public function addFilm($titre, $description, $duree, $dateSortie, $coutTotal, $boxOffice, $urlAffiche, $idReal, $urlBandeAnnonce = '', $langueOriginale = '')", 
    $content
);

// Mettre u00e0 jour les bindValue dans addFilm
$content = str_replace(
    "$stmt->bindValue(':budget', $budget, PDO::PARAM_STR);", 
    "$stmt->bindValue(':coutTotal', $coutTotal, PDO::PARAM_STR);", 
    $content
);

$content = str_replace(
    "$stmt->bindValue(':recette', $recette, PDO::PARAM_STR);", 
    "$stmt->bindValue(':boxOffice', $boxOffice, PDO::PARAM_STR);", 
    $content
);

// u00c9crire le contenu modifiu00e9 dans le fichier
file_put_contents($filePath, $content);

echo "<h1>Correction du fichier filmModele.php</h1>";
echo "<p>Les corrections ont u00e9tu00e9 appliquu00e9es avec succu00e8s.</p>";
echo "<p>Les changements suivants ont u00e9tu00e9 effectuu00e9s :</p>";
echo "<ul>";
echo "<li>'description' u2192 'descri'</li>";
echo "<li>'budget' u2192 'coutTotal'</li>";
echo "<li>'recette' u2192 'boxOffice'</li>";
echo "<li>'urlAffiche' u2192 'image'</li>";
echo "<li>'urlBandeAnnonce' u2192 'trailer'</li>";
echo "<li>'langueOriginale' u2192 'langueVO'</li>";
echo "</ul>";
echo "<p><a href='admin/films'>Retour u00e0 la liste des films</a></p>";

<?php
// Chemin vers le fichier u00e0 corriger
$filePath = __DIR__ . '/modeles/films/filmModele.php';

// Lire le contenu du fichier
$content = file_get_contents($filePath);

// Faire une sauvegarde du fichier original
file_put_contents($filePath . '.backup', $content);

// Remplacer les noms de colonnes incorrects dans la mu00e9thode updateFilm
$updatePattern = '/description = :description/m';
$updateReplacement = 'descri = :description';
$content = preg_replace($updatePattern, $updateReplacement, $content);

$updatePattern = '/budget = :coutTotal/m';
$updateReplacement = 'coutTotal = :coutTotal';
$content = preg_replace($updatePattern, $updateReplacement, $content);

$updatePattern = '/recette = :boxOffice/m';
$updateReplacement = 'boxOffice = :boxOffice';
$content = preg_replace($updatePattern, $updateReplacement, $content);

// u00c9crire le contenu modifiu00e9 dans le fichier
file_put_contents($filePath, $content);

echo "<h1>Correction du fichier filmModele.php</h1>";
echo "<p>Les corrections ont u00e9tu00e9 appliquu00e9es avec succu00e8s.</p>";
echo "<p><a href='admin/films'>Retour u00e0 la liste des films</a></p>";

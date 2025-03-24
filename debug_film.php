<?php
require_once 'modeles/films/filmModele.php';

// Créer une instance du modèle
$filmModele = new FilmModele();

// Récupérer un film par son ID
$film = $filmModele->getFilmById(46); // ID du film que vous essayez de modifier

// Afficher la structure du film
echo "<pre>";
print_r($film);
echo "</pre>";

// Afficher les noms des colonnes dans la table Film
echo "<h2>Colonnes de la table Film</h2>";
echo "<pre>";
print_r(array_keys($film));
echo "</pre>";

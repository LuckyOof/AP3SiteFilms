<?php
// Connexion u00e0 la base de donnu00e9es
try {
    $pdo = new PDO('mysql:host=localhost;dbname=bdfilms;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Ru00e9cupu00e9rer la structure de la table Film
    $stmt = $pdo->query("DESCRIBE Film");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h1>Structure de la table Film</h1>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        foreach ($column as $key => $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Ru00e9cupu00e9rer un exemple de film
    $stmt = $pdo->query("SELECT * FROM Film WHERE idFilm = 46 LIMIT 1");
    $film = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h1>Exemple de donnu00e9es pour le film ID 46</h1>";
    echo "<pre>";
    print_r($film);
    echo "</pre>";
    
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

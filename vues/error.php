<?php
$titre = "Erreur";
?>

<div class="error-container">
    <h1>Une erreur est survenue</h1>
    <div class="error-message">
        <?= $error_message ?? "Une erreur inattendue s'est produite." ?>
    </div>
    <a href="<?= URL ?>" class="btn-home">Retourner à l'accueil</a>
</div>

<style>
.error-container {
    padding: 2rem;
    max-width: 600px;
    margin: 2rem auto;
    text-align: center;
    background: #2d2d2d;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.error-container h1 {
    color: #fff;
    margin-bottom: 1rem;
}

.error-message {
    color: #dc3545;
    margin-bottom: 2rem;
    padding: 1rem;
    background: rgba(220, 53, 69, 0.1);
    border-radius: 5px;
}

.btn-home {
    display: inline-block;
    padding: 10px 20px;
    background: #6200ee;
    color: #fff;
    text-decoration: none;
    border-radius: 20px;
    transition: background-color 0.3s;
}

.btn-home:hover {
    background: #7722ff;
}
</style>

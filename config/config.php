<?php
// Chemins de l'application
$racine = dirname(__DIR__);
define('ROOT_PATH', $racine);
define('URL', '/AP3SiteFilms/');

// Chemins des dossiers MVC
define('CONTROLEURS_PATH', ROOT_PATH . '/controleurs/');
define('MODELES_PATH', ROOT_PATH . '/modeles/');
define('VUES_PATH', ROOT_PATH . '/vues/');

// Sous-dossiers
define('CONTROLEURS_FILMS_PATH', CONTROLEURS_PATH . 'films/');
define('CONTROLEURS_UTILISATEUR_PATH', CONTROLEURS_PATH . 'utilisateur/');
define('MODELES_FILMS_PATH', MODELES_PATH . 'films/');
define('MODELES_UTILISATEUR_PATH', MODELES_PATH . 'utilisateur/');
define('VUES_FILMS_PATH', VUES_PATH . 'films/');
define('VUES_UTILISATEUR_PATH', VUES_PATH . 'utilisateur/');

// Chemins des ressources
define('IMAGES_PATH', ROOT_PATH . '/ressources/images/');
define('UPLOADS_PATH', ROOT_PATH . '/ressources/uploads/');
define('CSS_URL', URL . 'ressources/css/');
define('JS_URL', URL . 'ressources/js/');
define('IMAGES_URL', URL . 'ressources/images/');

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'bdFilms');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuration des sessions
ini_set('session.cookie_lifetime', 60 * 60 * 24 * 7); // 7 jours
ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 7); // 7 jours
session_start();

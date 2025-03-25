<?php
    require_once 'config/config.php';

    // Gestion des erreurs en développement
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Chargement des contrôleurs
    require_once CONTROLEURS_FILMS_PATH . 'filmController.php';
    require_once CONTROLEURS_UTILISATEUR_PATH . 'userController.php';
    require_once CONTROLEURS_FILMS_PATH . 'classementController.php';
    require_once CONTROLEURS_PATH . 'admin/adminController.php';

    $filmController = new FilmController();
    $userController = new UserController();
    $classementController = new ClassementController();
    $adminController = new AdminController();

    try {
        if (empty($_GET['page'])) {
            $filmController->index();  
        }

        else{
            $url = explode("/", filter_var($_GET['page'], FILTER_SANITIZE_URL));

            switch($url[0]){
                case 'films':
                    if(empty($url[1])) {
                        $filmController->films();
                    }
                    else if($url[1] === 'search') {
                        $filmController->search();
                    }
                    else if($url[1] === 'show') {
                        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
                        if (!$id) {
                            throw new Exception("ID de film invalide");
                        }
                        $filmController->filmInfo($id);
                    }
                    else if($url[1] === 'popular') {
                        $filmController->popular();
                    }
                    else if($url[1] === 'topRated') {
                        $filmController->topRated();
                    }
                    else if($url[1] === 'ajouterAvis') {
                        // Vérifier si l'utilisateur est connecté
                        if(!isset($_SESSION["user"])) {
                            header('Content-Type: application/json');
                            echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour laisser un avis']);
                            exit();
                        }
                        $filmController->ajouterAvis();
                    }
                    else if($url[1] === 'supprimerAvis') {
                        // Vérifier si l'utilisateur est connecté
                        if(!isset($_SESSION["user"])) {
                            header('Content-Type: application/json');
                            echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour supprimer un avis']);
                            exit();
                        }
                        $filmController->supprimerAvis();
                    }
                    else {
                        throw new Exception("La page n'existe pas");
                    }
                    break;

                case 'news':
                    $filmController->news();  
                    break;

                case 'classement':
                    $classementController->index();
                    break;

                case 'login':
                    $userController->login();  
                    break;

                case 'register':
                    $userController->register();  
                    break;

                case 'profile':
                    if(!isset($_SESSION["user"])) {
                        header("Location: " . URL . "login");
                        exit();
                    }
                    $userController->profile();  
                    break;

                case 'logout':
                    $userController->logout();  
                    break;

                case 'toggleWatchlist':
                    if(!isset($_SESSION["user"])) {
                        header("Location: " . URL . "login");
                        exit();
                    }
                    $userController->toggleWatchlist();
                    break;

                case 'updateProfile':
                    if(!isset($_SESSION["user"])) {
                        header("Location: " . URL . "login");
                        exit();
                    }
                    $userController->updateProfile();
                    break;

                case 'addFilm':
                    $filmController->addFilm();
                    break;

                case 'admin':
                    // Vérifier si l'utilisateur est connecté et est admin
                    if(!isset($_SESSION["user"]) || $_SESSION["user"]['estAdmin'] != 1) {
                        $_SESSION['message'] = "Vous n'avez pas les droits d'accès à cette section.";
                        $_SESSION['message_type'] = "danger";
                        header("Location: " . URL);
                        exit();
                    }
                    
                    if(empty($url[1])) {
                        $adminController->dashboard();
                    }
                    else if($url[1] === 'films') {
                        $adminController->films();
                    }
                    else if($url[1] === 'addFilm') {
                        $adminController->addFilm();
                    }
                    else if($url[1] === 'saveFilm') {
                        $adminController->saveFilm();
                    }
                    else if($url[1] === 'editFilm' && isset($url[2])) {
                        $adminController->editFilm($url[2]);
                    }
                    else if($url[1] === 'updateFilm') {
                        $adminController->updateFilm();
                    }
                    else if($url[1] === 'deleteFilm') {
                        // Si c'est une requête POST ou si un ID est fourni dans l'URL
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($url[2])) {
                            $adminController->deleteFilm(isset($url[2]) ? $url[2] : null);
                        } else {
                            throw new Exception("La page d'administration n'existe pas");
                        }
                    }
                    else if($url[1] === 'realisateurs') {
                        $adminController->realisateurs();
                    }
                    else if($url[1] === 'addRealisateur') {
                        $adminController->addRealisateur();
                    }
                    else if($url[1] === 'saveRealisateur') {
                        $adminController->saveRealisateur();
                    }
                    else if($url[1] === 'editRealisateur' && isset($url[2])) {
                        $adminController->editRealisateur($url[2]);
                    }
                    else if($url[1] === 'updateRealisateur') {
                        $adminController->updateRealisateur();
                    }
                    else if($url[1] === 'deleteRealisateur') {
                        // Si c'est une requête POST ou si un ID est fourni dans l'URL
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($url[2])) {
                            $adminController->deleteRealisateur(isset($url[2]) ? $url[2] : null);
                        } else {
                            throw new Exception("La page d'administration n'existe pas");
                        }
                    }
                    else if($url[1] === 'genres') {
                        $adminController->genres();
                    }
                    else if($url[1] === 'addGenre') {
                        $adminController->addGenre();
                    }
                    else if($url[1] === 'saveGenre') {
                        $adminController->saveGenre();
                    }
                    else if($url[1] === 'editGenre' && isset($url[2])) {
                        $adminController->editGenre($url[2]);
                    }
                    else if($url[1] === 'updateGenre') {
                        $adminController->updateGenre();
                    }
                    else if($url[1] === 'deleteGenre') {
                        // Si c'est une requête POST ou si un ID est fourni dans l'URL
                        if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($url[2])) {
                            $adminController->deleteGenre(isset($url[2]) ? $url[2] : null);
                        } else {
                            throw new Exception("La page d'administration n'existe pas");
                        }
                    }
                    else if($url[1] === 'acteurs') {
                        $adminController->acteurs();
                    }
                    else if($url[1] === 'addActeur') {
                        $adminController->addActeur();
                    }
                    else if($url[1] === 'saveActeur') {
                        $adminController->saveActeur();
                    }
                    else if($url[1] === 'editActeur' && isset($url[2])) {
                        $adminController->editActeur($url[2]);
                    }
                    else if($url[1] === 'updateActeur' && isset($url[2])) {
                        $adminController->updateActeur($url[2]);
                    }
                    else if($url[1] === 'deleteActeur' && isset($url[2])) {
                        $adminController->deleteActeur($url[2]);
                    }
                    else {
                        throw new Exception("La page d'administration n'existe pas");
                    }
                    break;

                default:
                    throw new Exception("La page n'existe pas");
            }
        }
    }
    catch(Exception $e){
        $error_message = $e->getMessage();
        require_once VUES_PATH . "error.php";
    }
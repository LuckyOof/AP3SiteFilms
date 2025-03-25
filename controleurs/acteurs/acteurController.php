<?php
require_once MODELES_ACTEURS_PATH . 'acteurModele.php';

class ActeurController {
    private $acteurModele;

    public function __construct() {
        $this->acteurModele = new ActeurModele();
    }

    /**
     * Affiche la liste des acteurs
     */
    public function index() {
        try {
            $acteurs = $this->acteurModele->getAllActeurs();
            
            $data_page = [
                "page_description" => "Liste des acteurs",
                "page_title" => "Acteurs - Site de Films",
                "acteurs" => $acteurs,
                "css" => ["acteurs.css"],
                "view" => [
                    "vues/front/header.php",
                    "vues/acteurs/index.php",
                    "vues/front/footer.php"
                ],
                "template" => "vues/front/layout.php"
            ];
            
            $this->genererPage($data_page);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la récupération des acteurs : " . $e->getMessage());
        }
    }

    /**
     * Affiche les détails d'un acteur
     */
    public function show($id) {
        try {
            $acteur = $this->acteurModele->getActeurById($id);
            if (!$acteur) {
                throw new Exception("L'acteur demandé n'existe pas");
            }

            // Récupérer les films de l'acteur
            $films = $this->acteurModele->getFilmsByActeur($id);
            
            $data_page = [
                "page_description" => "Détails de l'acteur " . $acteur['prenom'] . " " . $acteur['nom'],
                "page_title" => $acteur['prenom'] . " " . $acteur['nom'] . " - Site de Films",
                "acteur" => $acteur,
                "films" => $films,
                "css" => ["acteur-details.css"],
                "view" => [
                    "vues/front/header.php",
                    "vues/acteurs/show.php",
                    "vues/front/footer.php"
                ],
                "template" => "vues/front/layout.php"
            ];
            
            $this->genererPage($data_page);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la récupération des détails de l'acteur : " . $e->getMessage());
        }
    }

    /**
     * Affiche le formulaire d'ajout d'un acteur
     */
    public function add() {
        // Vérifier si l'utilisateur est admin
        if (!isset($_SESSION['user']) || !$_SESSION['user']['estAdmin']) {
            header('Location: ' . URL);
            exit();
        }

        $message = null;
        $messageClass = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validation des données
                if (empty($_POST['nom']) || empty($_POST['prenom'])) {
                    throw new Exception("Le nom et le prénom sont obligatoires");
                }

                // Traitement de la photo si elle est fournie
                $photo = null;
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = ROOT_PATH . 'ressources/images/acteurs/';
                    $fileInfo = pathinfo($_FILES['photo']['name']);
                    $extension = strtolower($fileInfo['extension']);
                    
                    // Vérifier l'extension
                    $allowedExtensions = ['jpg', 'jpeg', 'png'];
                    if (!in_array($extension, $allowedExtensions)) {
                        throw new Exception("Extension de fichier non autorisée. Seuls jpg, jpeg et png sont acceptés.");
                    }
                    
                    // Générer un nom unique
                    $photo = uniqid() . '.' . $extension;
                    $uploadFile = $uploadDir . $photo;
                    
                    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
                        throw new Exception("Erreur lors de l'upload de la photo");
                    }
                }

                // Ajout de l'acteur
                $idActeur = $this->acteurModele->addActeur(
                    $_POST['nom'],
                    $_POST['prenom'],
                    !empty($_POST['dateNaissance']) ? $_POST['dateNaissance'] : null,
                    !empty($_POST['nationalite']) ? $_POST['nationalite'] : null,
                    $photo
                );

                if ($idActeur) {
                    $_SESSION['success'] = "L'acteur a été ajouté avec succès";
                    header('Location: ' . URL . 'admin/acteurs');
                    exit();
                }
            } catch (Exception $e) {
                $message = $e->getMessage();
                $messageClass = "error";
            }
        }

        $data_page = [
            "page_description" => "Ajouter un nouvel acteur",
            "page_title" => "Ajouter un acteur - Site de Films",
            "message" => $message,
            "messageClass" => $messageClass,
            "css" => ["form.css"],
            "js" => ["form-validation.js"],
            "view" => [
                "vues/front/header.php",
                "vues/acteurs/add.php",
                "vues/front/footer.php"
            ],
            "template" => "vues/front/layout.php"
        ];

        $this->genererPage($data_page);
    }

    /**
     * Affiche le formulaire de modification d'un acteur
     */
    public function edit($id) {
        // Vérifier si l'utilisateur est admin
        if (!isset($_SESSION['user']) || !$_SESSION['user']['estAdmin']) {
            header('Location: ' . URL);
            exit();
        }

        try {
            $acteur = $this->acteurModele->getActeurById($id);
            if (!$acteur) {
                throw new Exception("L'acteur demandé n'existe pas");
            }

            $message = null;
            $messageClass = null;

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try {
                    // Validation des données
                    if (empty($_POST['nom']) || empty($_POST['prenom'])) {
                        throw new Exception("Le nom et le prénom sont obligatoires");
                    }

                    // Traitement de la photo si une nouvelle est fournie
                    $photo = $acteur['photo']; // Garder l'ancienne photo par défaut
                    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                        $uploadDir = ROOT_PATH . 'ressources/images/acteurs/';
                        $fileInfo = pathinfo($_FILES['photo']['name']);
                        $extension = strtolower($fileInfo['extension']);
                        
                        // Vérifier l'extension
                        $allowedExtensions = ['jpg', 'jpeg', 'png'];
                        if (!in_array($extension, $allowedExtensions)) {
                            throw new Exception("Extension de fichier non autorisée. Seuls jpg, jpeg et png sont acceptés.");
                        }
                        
                        // Générer un nom unique
                        $photo = uniqid() . '.' . $extension;
                        $uploadFile = $uploadDir . $photo;
                        
                        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
                            throw new Exception("Erreur lors de l'upload de la photo");
                        }

                        // Supprimer l'ancienne photo si elle existe
                        if ($acteur['photo'] && file_exists($uploadDir . $acteur['photo'])) {
                            unlink($uploadDir . $acteur['photo']);
                        }
                    }

                    // Mise à jour de l'acteur
                    $success = $this->acteurModele->updateActeur(
                        $id,
                        $_POST['nom'],
                        $_POST['prenom'],
                        !empty($_POST['dateNaissance']) ? $_POST['dateNaissance'] : null,
                        !empty($_POST['nationalite']) ? $_POST['nationalite'] : null,
                        $photo
                    );

                    if ($success) {
                        $_SESSION['success'] = "L'acteur a été modifié avec succès";
                        header('Location: ' . URL . 'admin/acteurs');
                        exit();
                    }
                } catch (Exception $e) {
                    $message = $e->getMessage();
                    $messageClass = "error";
                }
            }

            $data_page = [
                "page_description" => "Modifier l'acteur " . $acteur['prenom'] . " " . $acteur['nom'],
                "page_title" => "Modifier un acteur - Site de Films",
                "acteur" => $acteur,
                "message" => $message,
                "messageClass" => $messageClass,
                "css" => ["form.css"],
                "js" => ["form-validation.js"],
                "view" => [
                    "vues/front/header.php",
                    "vues/acteurs/edit.php",
                    "vues/front/footer.php"
                ],
                "template" => "vues/front/layout.php"
            ];

            $this->genererPage($data_page);
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la modification de l'acteur : " . $e->getMessage());
        }
    }

    /**
     * Supprime un acteur
     */
    public function delete($id) {
        // Vérifier si l'utilisateur est admin
        if (!isset($_SESSION['user']) || !$_SESSION['user']['estAdmin']) {
            header('Location: ' . URL);
            exit();
        }

        try {
            if ($this->acteurModele->deleteActeur($id)) {
                $_SESSION['success'] = "L'acteur a été supprimé avec succès";
            } else {
                $_SESSION['error'] = "Erreur lors de la suppression de l'acteur";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: ' . URL . 'admin/acteurs');
        exit();
    }

    /**
     * Génère la page avec le template
     */
    private function genererPage($data) {
        // S'assurer que les constantes URL sont disponibles dans les vues
        if (!isset($data['URL'])) $data['URL'] = URL;
        if (!isset($data['CSS_URL'])) $data['CSS_URL'] = CSS_URL;
        if (!isset($data['JS_URL'])) $data['JS_URL'] = JS_URL;
        if (!isset($data['IMAGES_URL'])) $data['IMAGES_URL'] = IMAGES_URL;
        
        // Extraction des variables pour la vue
        extract($data);

        // Définition des valeurs par défaut pour les métadonnées
        $title = isset($page_title) ? $page_title : "AP3 Films";
        $description = isset($page_description) ? $page_description : "Découvrez notre sélection de films";
        
        // Gestion des ressources CSS et JS
        $css = isset($css) ? $css : [];
        $js = isset($js) ? $js : [];

        // Capture du contenu des vues
        ob_start();
        foreach ($view as $viewFile) {
            require $viewFile;
        }
        $content = ob_get_clean();

        // Rendu du template avec le contenu
        require_once($template);
    }
}

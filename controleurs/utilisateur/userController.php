<?php
require_once MODELES_UTILISATEUR_PATH . 'utilisateurModele.php';
require_once MODELES_FILMS_PATH . 'filmModele.php';

class UserController {
    private $utilisateurModele;
    private $filmModele;

    public function __construct() {
        $this->utilisateurModele = new UtilisateurModele();
        $this->filmModele = new FilmModele();
    }

    public function login() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
            $password = $_POST["password"];

            if ($this->utilisateurModele->verifyPassword($email, $password)) {
                $user = $this->utilisateurModele->getUserByEmail($email);
                $_SESSION["user"] = [
                    "id" => $user["idUtilisateur"],
                    "nom" => $user["nom"],
                    "prenom" => $user["prenom"],
                    "email" => $user["email"],
                    "pseudo" => $user["pseudo"],
                    "estAdmin" => $user["estAdmin"]
                ];
                header("Location: " . URL . "profile");
                exit();
            } else {
                $_SESSION["error"] = "Email ou mot de passe incorrect";
                header("Location: " . URL . "login"); // Redirect to login page
                exit();
            }
        }

        $data_page = [
            "page_description" => "Page profil",
            "page_title" => "Page profil",
            "css" => ["auth.css"],
            "view" => [
                "vues/front/header.php",
                "vues/utilisateur/login.php",
                "vues/front/footer.php"
            ],
            "template" => "vues/front/layout.php"
        ];
        
        $this->genererPage($data_page);
    }

    public function register() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nom = filter_input(INPUT_POST, "nom", FILTER_SANITIZE_SPECIAL_CHARS);
            $prenom = filter_input(INPUT_POST, "prenom", FILTER_SANITIZE_SPECIAL_CHARS);
            $pseudo = filter_input(INPUT_POST, "pseudo", FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
            $password = $_POST["password"];
            $confirm_password = $_POST["confirm_password"];

            if ($password !== $confirm_password) {
                $_SESSION["error"] = "Les mots de passe ne correspondent pas";
                header("Location: " . URL . "register");
                exit();
            }

            $passwordErrors = $this->validatePassword($password);
            
            if (!empty($passwordErrors)) {
                $_SESSION["error"] = $passwordErrors[0]; // Afficher la première erreur
                header("Location: " . URL . "register");
                exit();
            }

            if ($this->utilisateurModele->getUserByEmail($email)) {
                $_SESSION["error"] = "Cet email est déjà utilisé";
                header("Location: " . URL . "register");
                exit();
            }

            if ($this->utilisateurModele->createUser($nom, $prenom, $pseudo, $email, $password)) {
                $_SESSION["success"] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                header("Location: " . URL . "login");
                exit();
            } else {
                $_SESSION["error"] = "Erreur lors de l'inscription";
                header("Location: " . URL . "register");
                exit();
            }
        }

        $data_page = [
            "page_description" => "Page profil",
            "page_title" => "Page profil",
            "css" => ["auth.css"],
            "view" => [
                "vues/front/header.php",
                "vues/utilisateur/register.php",
                "vues/front/footer.php"
            ],
            "template" => "vues/front/layout.php"
        ];
        
        $this->genererPage($data_page);
    }

    private function validatePassword($password) {
        $errors = [];
        
        if (strlen($password) < 12) {
            $errors[] = "Le mot de passe doit contenir au moins 12 caractères";
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une lettre majuscule";
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un chiffre";
        }
        
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un caractère spécial";
        }
        
        return $errors;
    }

    public function profile() {
        if (!isset($_SESSION["user"])) {
            header("Location: " . URL . "login");
            exit();
        }

        $userId = $_SESSION["user"]["id"];
        
        $sortBy = isset($_POST['sort']) ? $_POST['sort'] : 'dateAjout';
        $sortOrder = isset($_POST['order']) ? $_POST['order'] : 'DESC';
        
        $filters = [];
        if (isset($_POST['genre']) && !empty($_POST['genre'])) {
            $filters['genre'] = $_POST['genre'];
        }
        if (isset($_POST['langue']) && !empty($_POST['langue'])) {
            $filters['langue'] = $_POST['langue'];
        }
        if (isset($_POST['annee']) && !empty($_POST['annee'])) {
            $filters['annee'] = intval($_POST['annee']);
        }
        if (isset($_POST['dureeMin']) && is_numeric($_POST['dureeMin'])) {
            $filters['dureeMin'] = intval($_POST['dureeMin']);
        }
        if (isset($_POST['dureeMax']) && is_numeric($_POST['dureeMax'])) {
            $filters['dureeMax'] = intval($_POST['dureeMax']); 
        }

        $watchlist = $this->utilisateurModele->getWatchlist($userId, $sortBy, $sortOrder, $filters);

        $genres = $this->filmModele->getDistinctValues('genre');
        $langues = $this->filmModele->getDistinctValues('langueVO');
        $annees = $this->filmModele->getDistinctValues('YEAR(dateSortie)');

        $reviews = $this->utilisateurModele->getUserReviews($userId);

        $data_page = [
            "page_description" => "Page profil",
            "page_title" => "Page profil",
            "user" => $_SESSION['user'],
            "watchlist" => $watchlist,
            "reviews" => $reviews,
            "filter_options" => [
                "genres" => $genres,
                "langues" => $langues,
                "annees" => $annees
            ],
            "css" => ["profile.css"],
            "js" => ["profile.js"],
            "view" => [
                "vues/front/header.php",
                "vues/utilisateur/profile.php",
                "vues/front/footer.php"
            ],
            "template" => "vues/front/layout.php"
        ];
        
        $this->genererPage($data_page);
    }

    public function updateProfile() {
        if(!isset($_SESSION["user"])) {
            header("Location: " . URL . "login");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $userId = $_SESSION["user"]["id"];
            $nom = filter_input(INPUT_POST, "nom", FILTER_SANITIZE_SPECIAL_CHARS);
            $prenom = filter_input(INPUT_POST, "prenom", FILTER_SANITIZE_SPECIAL_CHARS);
            $pseudo = filter_input(INPUT_POST, "pseudo", FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
            $currentPassword = $_POST["current_password"] ?? null;
            $newPassword = $_POST["new_password"] ?? null;
            $confirmPassword = $_POST["confirm_password"] ?? null;

            $success = true;
            $error = "";

            if (!$this->utilisateurModele->updateUser($userId, $nom, $prenom, $pseudo, $email)) {
                $success = false;
                $error = "Erreur lors de la mise à jour du profil";
            }

            if ($currentPassword && $newPassword && $confirmPassword) {
                if ($newPassword !== $confirmPassword) {
                    $success = false;
                    $error = "Les nouveaux mots de passe ne correspondent pas";
                } else {
                    $user = $this->utilisateurModele->getUserById($userId);
                    if ($currentPassword === $user["motDePasse"]) {
                        $passwordErrors = $this->validatePassword($newPassword);
                        
                        if (!empty($passwordErrors)) {
                            $success = false;
                            $error = $passwordErrors[0]; // Afficher la première erreur
                        } else {
                            if (!$this->utilisateurModele->updatePassword($userId, $newPassword)) {
                                $success = false;
                                $error = "Erreur lors de la mise à jour du mot de passe";
                            }
                        }
                    } else {
                        $success = false;
                        $error = "Mot de passe actuel incorrect";
                    }
                }
            }

            if ($success) {
                $_SESSION["user"]["nom"] = $nom;
                $_SESSION["user"]["prenom"] = $prenom;
                $_SESSION["user"]["pseudo"] = $pseudo;
                $_SESSION["user"]["email"] = $email;
                $_SESSION["success"] = "Profil mis à jour avec succès";
                header("Location: " . URL . "profile");
                exit();
            } else {
                $_SESSION["error"] = $error;
                header("Location: " . URL . "profile");
                exit();
            }
        }

        header("Location: " . URL . "profile");
        exit();
    }

    public function toggleWatchlist() {
        if(!isset($_SESSION["user"])) {
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "error" => "Non authentifié"]);
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $userId = $_SESSION["user"]["id"];
            $filmId = filter_input(INPUT_POST, "filmId", FILTER_SANITIZE_NUMBER_INT);
            $action = filter_input(INPUT_POST, "action", FILTER_SANITIZE_SPECIAL_CHARS);

            $result = false;
            $message = "";

            if ($action === "add") {
                $result = $this->utilisateurModele->addToWatchlist($userId, $filmId);
                $message = $result ? "Film ajouté à votre watchlist" : "Film déjà dans votre watchlist";
            } elseif ($action === "remove") {
                $result = $this->utilisateurModele->removeFromWatchlist($userId, $filmId);
                $message = $result ? "Film retiré de votre watchlist" : "Erreur lors du retrait du film";
            }

            header('Content-Type: application/json');
            echo json_encode(["success" => $result, "message" => $message]);
            exit();
        }

        header('Content-Type: application/json');
        echo json_encode(["success" => false, "error" => "Méthode non autorisée"]);
        exit();
    }

    public function logout() {
        unset($_SESSION["user"]);
        session_destroy();
        header("Location: " . URL);
        exit();
    }

    private function genererPage($data) {
        extract($data);
        ob_start();
        foreach ($view as $v) {
            include $v;
        }
        $content = ob_get_clean();
        include $template;
    }
}

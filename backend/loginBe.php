<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 'On');
ini_set('error_log', '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/php-error.log');
$pdo = new PDO("sqlite:/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/ecoride.db");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_POST['submit'])) {


    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        $email = htmlspecialchars($_POST['email']);
        $password = $_POST['password'];



        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $count = $stmt->fetchColumn(); // Récupère la première colonne du résultat (le count)

            if ($count > 0) {
                $stmt = $pdo->prepare("SELECT password FROM utilisateurs WHERE email = :email");
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                $motDePasseHacheBDD = $stmt->fetchColumn(); // Récupère le mot de passe hashé



                if ($motDePasseHacheBDD) {
                    // Maintenant, vous avez le mot de passe hashé de la base de données dans $motDePasseHacheBDD
                    // Vous pouvez maintenant vérifier le mot de passe entré par l'utilisateur :
                    if (isset($_POST['password'])) {
                        $motDePasseEntre = $_POST['password'];
                        if (password_verify($motDePasseEntre, $motDePasseHacheBDD)) {


                            $_SESSION['loggedin'] = true;
                            $stmt = $pdo->prepare("SELECT user_id FROM utilisateurs WHERE email = :email");
                            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                            $stmt->execute();


                            $userIdFromDatabase = $stmt->fetchColumn();
                            $_SESSION['user_id'] = $userIdFromDatabase; // Stockez l'ID en session


                            if ($userIdFromDatabase !== false) {
                                $_SESSION['user_id'] = $userIdFromDatabase; // Stockez l'ID en session
                            } else {
                                // Gérer le cas où l'ID n'est pas trouvé (devrait rarement arriver si l'email existe)
                                echo "Erreur : Impossible de récupérer l'ID de l'utilisateur.";

                                // Vous pourriez envisager de déconnecter l'utilisateur ou d'afficher un message d'erreur.
                            }
                            try {
                                $stmt = $pdo->prepare('SELECT role FROM utilisateurs WHERE user_id = :user_id');
                                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                                $stmt->execute();
                                $role = $stmt->fetchColumn();
                                if ($role == 'employee') {
                                    $_SESSION['role'] = 'employee';
                                } elseif ($role == 'admin') {
                                    $_SESSION['role'] = 'admin';
                                } elseif ($role == 'user') {
                                    $_SESSION['role'] = 'user';
                                }
                            } catch (PDOException $e) {
                                echo "Erreur lors de la récupération du rôle : " . $e->getMessage();
                            }
                            header("Location: http://localhost:4000/public/index.php");
                            exit();
                            // Ici, vous pouvez connecter l'utilisateur (démarrer une session, etc.)
                        } else {
                            $error = "Mot de passe incorrect";
                        }
                    } else {
                        $error = "Veuillez entrer un mot de passe";
                    }
                } else {
                    $error = "Aucun mot de passe trouvé pour cet utilisateur";
                }
            } else {
                $error = "Aucun utilisateur trouvé avec cet email";
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}

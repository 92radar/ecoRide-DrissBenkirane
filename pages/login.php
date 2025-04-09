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
                            header("Location: http://localhost:4000/pages/home.php");
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
require '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/header.php';
?>
<link rel="stylesheet" href="../styles/login.css">
<?php if (isset($error)) : ?>
    <div class="alert alert-danger" role="alert">
        <?php echo $error; ?>
    </div>
<?php endif; ?>
<section class="vh-100">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6 text-black">

                <div class="px-5 ms-xl-4">
                    <i class="fas fa-crow fa-2x me-3 pt-5 mt-xl-4" style="color: #709085;"></i>
                    <span class="h1 fw-bold mb-0">ECORIDE</span>

                </div>

                <div class="d-flex align-items-center h-custom-2 px-5 ms-xl-4 mt-5 pt-5 pt-xl-0 mt-xl-n5">

                    <form method="post" action="" style="width: 23rem;">

                        <h3 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Log in</h3>

                        <div data-mdb-input-init class="form-outline mb-4">
                            <input type="email" name="email" class="form-control form-control-lg" />
                            <label class="form-label" for="">Email address</label>
                        </div>

                        <div data-mdb-input-init class="form-outline mb-4">
                            <input type="password" name="password" class="form-control form-control-lg" />
                            <label class="form-label" for="">Password</label>
                        </div>

                        <div class="pt-1 mb-4">
                            <button data-mdb-button-init data-mdb-ripple-init class="btn btn-info btn-lg btn-block"
                                type="submit" name="submit">Login</button>
                        </div>

                        <p class="small mb-5 pb-lg-2"><a class="text-muted" href="#!">Mot de passe oublié ?</a></p>
                        <p>Vous n'avez pas de compte ? <a href="/pages/register.php" class="link-info">Inscription
                                ici</a></p>

                    </form>

                </div>

            </div>
            <div class="col-sm-6 px-0 d-none d-sm-block">
                <img src="../images/80815.jpg" alt="Login image" class="w-100 vh-100"
                    style="object-fit: cover; object-position: left;">
            </div>
        </div>
    </div>
</section>
<?php
require_once '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/footer.php';
?>
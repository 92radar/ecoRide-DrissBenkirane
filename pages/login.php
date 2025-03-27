<link rel="stylesheet" href="../styles/login.css">

<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/header.php';







$pdo = new PDO("sqlite:/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/ecorideDatabase.db");
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
                            header("Location: http://localhost:4000/"); // Remplacez par l'URL de votre page d'accueil
                            exit();
                            // Ici, vous pouvez connecter l'utilisateur (démarrer une session, etc.)
                        } else {
                            echo "Mot de passe incorrect.";
                        }
                    } else {
                        echo "Le champ mot de passe n'a pas été soumis.";
                    }
                } else {
                    echo "Erreur : Impossible de récupérer le mot de passe hashé.";
                }
            } else {
                echo "Email non trouvé";
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}















?>


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
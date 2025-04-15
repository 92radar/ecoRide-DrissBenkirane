<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 'On');
ini_set('error_log', '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/php-error.log');
if (isset($_POST['logout'])) {
    // Détruire toutes les variables de session
    $_SESSION = array();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();

    // Rediriger vers la page de connexion ou la page actuelle (pour rafraîchir l'affichage)
    header("Location: http://localhost:4000/public/login.php"); // Redirige vers la page home
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>site de co-voiturage ecologique</title>
    <link rel="stylesheet" href="../styles/font.css">
    <link rel="stylesheet" href="../styles/header.css">
    <link rel="stylesheet" href="../styles/register.css">
    <link rel="stylesheet" href="../styles/second_header.css">

    <link rel="stylesheet" href="../styles/footer.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js"
        integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous">
    </script>


</head>

<header>
    <div>
        <nav class="mobile-nav">
            <a href="/public/home.php"> </br>
                <img src="/images/home-icon2.png" alt="logo" class="logo"></a>

            <a href="/public/covoiturage.php"></br><img src="/images/vecteezy_location-pointer-pin-icon_22220318.png"
                    class="logo">
            </a>
            <a href="/public/account.php"></br><img src="/images/vecteezy_simple-user-default-icon_24983914.png"
                    class="logo">
            </a>

        </nav>
    </div>

    <nav class="navbar navbar-expand-lg navbar-light bg-light" id="section1">
        <a class="navbar-brand" href="#">ECORIDE</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="/public/index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/public/covoiturage.php">Co-voiturage</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Espace utilisateurs
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <?php
                        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] = true) {
                            echo '<a class="dropdown-item" href="/public/account.php">Profil</a>';

                            echo '<form method="post"><button class="dropdown-item" name="logout">Déconnexion</button></form>';
                        } else {
                            // L'utilisateur n'est pas connecté, afficher les liens de connexion/inscription
                            echo '<a class="dropdown-item" href="/public/login.php">Connexion</a>';
                            echo '<a class="dropdown-item" href="/public/register.php">S\'inscrire</a>';
                        }
                        ?>
                </li>

            </ul>

    </nav>



    <div class="hero-scene text-center text-white">
        <div></br>
            <h1>Soyez green, voyagez et engagez vous pour la planete.</h1></br>


        </div>



</header>
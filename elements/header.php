<?php
session_start();
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
    header("Location: http://localhost:4000"); // Redirige vers la page home
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
    <link rel="stylesheet" href="../styles/research.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js"
        integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous">
    </script>


</head>

<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light" id="section1">
        <a class="navbar-brand" href="#">ECORIDE</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/covoiturage">Co-voiturage</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Espace utilisateurs
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <?php
                        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                            echo '<a class="dropdown-item" href="/account">Profil</a>';
                            echo '<a class="dropdown-item" href="/account">Publier un trajet</a>';
                            echo '<form method="post"><button class="dropdown-item" name="logout">Déconnexion</button></form>';
                        } else {
                            // L'utilisateur n'est pas connecté, afficher les liens de connexion/inscription
                            echo '<a class="dropdown-item" href="/pages/login.php">Connexion</a>';
                            echo '<a class="dropdown-item" href="/pages/register.php">S\'inscrire</a>';
                        }
                        ?>
                </li>

            </ul>

    </nav>



    <div class="hero-scene text-center text-white">
        <div></br>
            <h1>Soyez green, voyagez et engagez vous pour la planete.</h1>


        </div>

        <div class=" hero-scene-content " id="sectionRecherche">
            <form action="" method="">
                <div class="recherche-multicriteres text-black">
                    <label for="depart"></label>
                    <img class="icon" src="/images/location_16138523.png" alt="map" class="map-icon">
                    <input type="text" id="depart" name="depart" placeholder="Ville de départ">

                    <label for="arrivee"></label>
                    <input type="text" id="arrivee" name="arrivee" placeholder="Ville d'arrivée">

                    <label for="date"></label>
                    <img class="icon" src="/images/calendar_6057403-2.png" alt="calendar" class="calendar-icon">
                    <input type="date" id="date" name="date">

                    <button type="submit" aria-label="Rechercher">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-search" viewBox="0 0 16 16">
                            <path
                                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                        </svg>
                    </button>
                </div>
            </form>



</header>
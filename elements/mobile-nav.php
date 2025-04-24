<?php

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

if (isset($_POST['search'])) {
    $depart = $_POST['depart'];
    $arrivee = $_POST['arrivee'];
    $date = $_POST['date'];
    header("Location: http://localhost:4000/public/covoiturage.php?depart=$depart&arrivee=$arrivee&date=$date");
    exit();
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>site de co-voiturage ecologique</title>
    <link rel="stylesheet" href="../styles/font.css">

    <link rel="stylesheet" href="../styles/covoiturage.css">


    <link rel="stylesheet" href="../styles/home.css">
    <link rel="stylesheet" href="../styles/header.css">
    <link rel="stylesheet" href="../styles/research.css">
    <link rel="stylesheet" href="../styles/footer.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js"
        integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous">
    </script>


</head>

<div>
    <nav class="mobile-nav">
        <a href="../public/index.php"> </br>
            <img src="/images/home-icon2.png" alt="logo" class="logo"></a>

        <a href="../public/covoiturage.php"></br><img src="/images/vecteezy_location-pointer-pin-icon_22220318.png"
                class="logo">
        </a>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'employee') {
            echo '<a href="../public/employee.php"></br><img src="/images/vecteezy_simple-user-default-icon_24983914.png"
                        class="logo">

                </a>';
        } elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'user') {

            echo '<a href="../public/account.php"></br><img src="/images/vecteezy_simple-user-default-icon_24983914.png"
                    class="logo">

            </a>';
        } elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            echo '<a href="../public/admin.php"></br><img src="/images/vecteezy_simple-user-default-icon_24983914.png"
                    class="logo">
            </a>';
        } else {
            echo '<a href="../public/login.php"></br><img src="/images/vecteezy_simple-user-default-icon_24983914.png"
                    class="logo">
            </a>';
        }
        ?>


    </nav>
</div>
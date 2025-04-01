<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 'On');
ini_set('error_log', '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/php-error.log');



// Start the session to access session variables

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

$pdo = new PDO("sqlite:/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/ecorideDatabase.db");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$userInfos = []; // Initialize $userInfos as an empty array

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE user_id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    $userInfos = $stmt->fetchAll(PDO::FETCH_OBJ);
    // var_dump($userInfos); // Keep this for debugging if needed
} else {
    // Redirect to login page if not logged in
    header("Location: http://localhost:4000/pages/login.php"); // Adjust the path to your login page
    exit();
}

require_once '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/header.php';
?>
<link rel="stylesheet" href="styles/account.css">

<div style="display: flex;">
    <div class="sidebar">
        <p>Navigation</p> </br>
        <div class="menu">
            <ul>
                <li><a href="#">Informations personnelles</a></li>
                <li><a href="#">Publier un trajet</a></li>
                <li><a href="#">Devenir chauffeur</a></li>
            </ul>
        </div>
        <form method="post">
            <button type="submit" name="logout" class="logout-btn">Se déconnecter</button>
        </form>
    </div>

    <div style="flex-grow: 1;">
        <div class="ligne-horizontale"></div></br>
        <h1>Profil</h1>
        <div class="profil">
            <div class="profil-details">
                <?php if (!empty($userInfos)): ?>
                    <?php foreach ($userInfos as $userInfo): ?>
                        <h3>Informations personnelles</h3>
                        <p>
                            <strong>Nom :</strong> <?= htmlspecialchars($userInfo->nom) ?></br>
                            <strong>Prénom :</strong> <?= htmlspecialchars($userInfo->prenom) ?></br>
                            <strong>Email :</strong> <?= htmlspecialchars($userInfo->email) ?></br>
                            <strong>Adresse :</strong> <?= htmlspecialchars($userInfo->adresse) ?></br>
                            <strong>Ville :</strong> <?= htmlspecialchars($userInfo->ville) ?></br>
                            <strong>Numéro de téléphone :</strong> <?= htmlspecialchars($userInfo->telephone) ?></br>
                        </p>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucune information utilisateur trouvée.</p>
                <?php endif; ?>
            </div>
            <div class="profil-actions">
                <button class="modifier-btn">Modifier</button>
            </div>
        </div>
        <div class="ligne-horizontale"></div></br>
        <h1>Devenir chauffeur</h1> </br>
        <div class="devenir-chauffeur">
            <div class="devenir-chauffeur-details">
                <form action="" method="post">
                    <legend>Informations</legend>
                    <label for="energie">Type de voiture</label>
                    <select name="energie" id="energie">
                        <option value="essence">Essence</option>
                        <option value="diesel">Diesel</option>
                        <option value="electrique">Electrique</option>
                    </select>
                    <div class="devenir-chauffeur-actions">
                        <button class="devenir-chauffeur-btn">Devenir chauffeur</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="ligne-horizontale">
        </div></br>
    </div>
</div>
</body>

</html>
<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 'On');
ini_set('error_log', '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/php-error.log');

$pdo = new PDO("sqlite:/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/ecorideDatabase.db");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$userInfos = []; // Initialisation de $userInfos comme un tableau vide

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE user_id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $userInfos = $stmt->fetchAll(PDO::FETCH_OBJ);
        // var_dump($userInfos); // Garder pour le débogage si nécessaire
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
    }
} else {
    header("Location: http://localhost:4000/pages/login.php");
    exit();
}

require_once '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/header.php';

// Démarrer la session pour accéder aux variables de session (déjà fait en haut)

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

    // Rediriger vers la page de connexion ou la page d'accueil
    header("Location: http://localhost:4000"); // Redirige vers la page home
    exit();
}

if (isset($_POST['modifier'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $date_naissance = $_POST['date_naissance'];
    $email = $_POST['email'];
    $adresse = $_POST['adresse'];
    $ville = $_POST['ville'];
    $telephone = $_POST['telephone'];

    try {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = :nom, prenom = :prenom, date_naissance = :date_naissance, email = :email, adresse = :adresse, ville = :ville, telephone = :telephone WHERE user_id = :id");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':date_naissance', $date_naissance);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':ville', $ville);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        echo '<div style="color: green;">Informations mises à jour avec succès!</div>';
        // Récupérer les informations utilisateur mises à jour
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE user_id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $userInfos = $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        echo '<div style="color: red;">Erreur lors de la mise à jour des informations : ' . $e->getMessage() . '</div>';
    }
}
if (isset($_POST['devenir_chauffeur'])) {
    $modele = $_POST['modele'];
    $couleur = $_POST['couleur'];
    $immatriculation = $_POST['immatriculation'];
    $date_immatriculation = $_POST['date_immatriculation'];
    $energie = $_POST['energie'];

    try {
        $stmt = $pdo->prepare("INSERT INTO voitures (modele, couleur, immatriculation, date_premiere_immatriculation, energie, user_id) VALUES (:modele, :couleur, :immatriculation, :date_premiere_immatriculation, :energie, :user_id)");
        $stmt->bindParam(':modele', $modele);
        $stmt->bindParam(':couleur', $couleur);
        $stmt->bindParam(':immatriculation', $immatriculation);
        $stmt->bindParam(':date_premiere_immatriculation', $date_immatriculation);
        $stmt->bindParam(':energie', $energie);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $success = "Vous êtes désormais chauffeur!";
        // Récupérer les informations utilisateur mises à jour

    } catch (PDOException $e) {
        $error_chauffeur = "Erreur lors de l'ajout des informations véhicule : " . $e->getMessage();
    }
}
if (isset($_POST['publier_trajet'])) {
    $depart = $_POST['lieu_depart'];
    $arrivee = $_POST['lieu_arrivee'];
    $date = $_POST['date_depart'];
    $heure = $_POST['heure_depart'];
    $date_arrivee = $_POST['date_arrivee'];
    $heure_arrivee = $_POST['heure_arrivee'];
    $prix = $_POST['prix_personne'];
    $places = $_POST['nb_place'];
    $commentaire = $_POST['commentaire'];
    $timeStamp = date('Y-m-d H:i:s');


    try {
        $stmt = $pdo->prepare("INSERT INTO covoiturages(lieu_depart, lieu_arrivee, date_depart, heure_depart, date_arrivee, heure_arrivee, prix_personne, nb_place, commentaire, user_id, created_at) VALUES (:lieu_depart, :lieu_arrivee, :date_depart, :heure_depart, :date_arrivee, :heure_arrivee, :prix_personne, :nb_place, :commentaire, :user_id, :created_at)");
        $stmt->bindParam(':lieu_depart', $depart);
        $stmt->bindParam(':lieu_arrivee', $arrivee);
        $stmt->bindParam(':date_depart', $date);
        $stmt->bindParam(':heure_depart', $heure);
        $stmt->bindParam(':date_arrivee', $date_arrivee);
        $stmt->bindParam(':heure_arrivee', $heure_arrivee);
        $stmt->bindParam(':prix_personne', $prix);
        $stmt->bindParam(':nb_place', $places);
        $stmt->bindParam(':commentaire', $commentaire);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':created_at', $timeStamp);
        $stmt->execute();
        $success_trajet = "Trajet publié avec succès!";
        // Récupérer les informations utilisateur mises à jour


    } catch (PDOException $e) {
        $error_trajet = "Erreur lors de la publication du trajet : " . $e->getMessage();
    }
}


?>
<link rel="stylesheet" href="styles/account.css">

<div style="display: flex;">
    <div class="sidebar">
        <p>Navigation</p> </br>
        <div class="menu">
            <ul>
                <li><a href="#section1">Informations personnelles</a></li>
                <li><a href="#section2">Publier un trajet</a></li>
                <li><a href="#section3">Devenir chauffeur</a></li>
                <li><a href="#section4">Historique des trajets</a></li>
                <li><a href="#section5">Co-voiturage en cours</a></li>
            </ul>
        </div>
        <form method="post">
            <button type="submit" name="logout" class="logout-btn">Se déconnecter</button>
        </form>
    </div>

    <div style="flex-grow: 1;">
        <div class="ligne-horizontale"></div></br>
        <?php if (isset($success)) : ?>
            <div class="alert alert-success container" role="alert">
                <?= $success ?></br>
            </div>
        <?php endif; ?>
        <?php if (isset($error_chauffeur)) : ?>
            <div class="alert alert-danger container" role="alert">
                <?= $error_chauffeur ?></br>
            </div>
        <?php endif; ?>
        <?php if (isset($success_trajet)) : ?>
            <div class="alert alert-success container" role="alert">
                <?= $success_trajet ?></br>
            </div>
        <?php endif; ?>
        <?php if (isset($error_trajet)) : ?>
            <div class="alert alert-danger container" role="alert">
                <?= $error_trajet ?></br>
            </div>
        <?php endif; ?>
        <h1>Profil</h1>
        <div class="profil">
            <div class="profil-details">
                <div class="profil-photo">
                    <img src="" alt="Photo de profil" class="photo-profil">
                </div>
                <?php if (!empty($userInfos)): ?>
                    <?php foreach ($userInfos as $userInfo): ?>
                        <h3>Informations personnelles</h3>
                        <div class="profil-info" id="section1">
                            <form action="" method="post">
                                <strong>Nom :</strong><input type="text" name="nom"
                                    value="<?= htmlspecialchars($userInfo->nom) ?>"></br>
                                <strong>Prénom :</strong><input type="text" name="prenom"
                                    value="<?= htmlspecialchars($userInfo->prenom) ?>"></br>
                                <strong>Date de naissance :</strong><input type="date" name="date_naissance"
                                    value="<?= htmlspecialchars($userInfo->date_naissance) ?>"></br>

                                <strong>Email :</strong><input type="email" name="email"
                                    value="<?= htmlspecialchars($userInfo->email) ?>"></br>
                                <strong>Adresse :</strong><input type="text" name="adresse"
                                    value="<?= htmlspecialchars($userInfo->adresse) ?>"></br>
                                <strong>
                                    Ville :</strong><input type="text" name="ville"
                                    value="<?= htmlspecialchars($userInfo->ville) ?>"></br>
                                <strong>Numéro de téléphone :</strong><input type="text" name="telephone"
                                    value="<?= htmlspecialchars($userInfo->telephone) ?>"></br>
                                </p>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Aucune information utilisateur trouvée.</p>
                        <?php endif; ?>
                        </br>

                        <div class=" profil-actions">
                            <button type="submit" name="modifier" class="profil-btn">Modifier</button>

                        </div>
                            </form>


                        </div>
                        <div class="ligne-horizontale"></div></br>


                        <h1>Devenir chauffeur</h1> </br>
                        <div class="devenir-chauffeur" id="section3">
                            <div class="devenir-chauffeur-details">
                                <form action="" method="post">
                                    <legend>Informations</legend>
                                    <label for="vehicule">Véhicule :</label>
                                    <input type="text" name="modele" id="modele_vehicule"
                                        placeholder="Modèle de la voiture"></br>
                                    <label for="couleur">Couleur :</label>
                                    <input type="text" name="couleur" id="couleur" placeholder="Couleur de la voiture"></br>
                                    <label for="immatriculation">Immatriculation :</label>
                                    <input type="text" name="immatriculation" id="immatriculation"
                                        placeholder="Numéro d'immatriculation"></br>
                                    <label for="date_premiere_immatriculation">Date d'immatriculation :
                                    </label>
                                    <input for="date_immatriculation" type="date" name="date_immatriculation"
                                        id="date_immatriculation" placeholder="Date d'immatriculation"></br>

                                    <label for="energie">Type de voiture :</label>
                                    <select name="energie" id="energie">
                                        <option value="essence">Essence</option>
                                        <option value="diesel">Diesel</option>
                                        <option value="electrique">Electrique</option>
                                    </select>
                                    <div class="devenir-chauffeur-actions">
                                        <button class="devenir-chauffeur-btn" type="submit" name="devenir_chauffeur">Devenir
                                            chauffeur</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="ligne-horizontale"></div></br>
                        <h1>Publier un trajet</h1> </br>
                        <div class="publier-trajet" id="section2">
                            <div class="publier-trajet-details">
                                <form action="" method="post">
                                    <legend>Informations</legend>
                                    <label for="depart">Ville de départ :</label>
                                    <input type="text" name="lieu_depart" id="depart" placeholder="Ville de départ"></br>
                                    <label for="arrivee">Ville d'arrivée :</label>
                                    <input type="text" name="lieu_arrivee" id="arrivee" placeholder="Ville d'arrivée"></br>
                                    <label for="date">Date de départ :</label>
                                    <input type="date" name="date_depart" id="date_depart" placeholder="Date de départ"></br>
                                    <label for="heure">Heure de départ :</label>
                                    <input type="time" name="heure_depart" id="heure_depart" placeholder="Heure de départ"></br>
                                    <label for="date_arrivee">Date d'arrivée :</label>
                                    <input type="date" name="date_arrivee" id="date_arrivee" placeholder="Date d'arrivée"></br>
                                    <label for="heure_arrivee">Heure d'arrivée :</label>
                                    <input type="time" name="heure_arrivee" id="heure_arrivee"
                                        placeholder="Heure d'arrivée"></br>
                                    <label for="prix">Prix par personne :</label>

                                    <input type="number" name="prix_personne" id="prix" placeholder="Prix"></br>
                                    <label for="places">Nombre de places :</label>
                                    <input type="number" name="nb_place" id="places" placeholder="Nombre de places"></br>
                                    <label for="commentaire">Commentaire :</label>
                                    <textarea name="commentaire" id="commentaire" placeholder="Commentaire"></textarea></br>
                                    <div class="publier-trajet-actions">
                                        <button class="publier-trajet-btn" type="submit" name="publier_trajet">Publier</button>
                                    </div></br>
                                </form>
                            </div>



                        </div></br>
                        <div class="ligne-horizontale"></div></br>
                        <h1 id="section4">Historique des trajets</h1>
                        <p>Section pour afficher l'historique des trajets.</p>
                        <div class="ligne-horizontale"></div></br>
                        <h1 id="section5">Co-voiturage en cours</h1>
                        <p>Section pour afficher les co-voiturages en cours.</p>
            </div>
        </div>
    </div>
</div>
</body>

</html>
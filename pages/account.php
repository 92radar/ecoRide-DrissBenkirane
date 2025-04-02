<?php

session_start();
// Démarrer la session pour accéder aux variables de session et les modifier
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '256M');
ini_set('log_errors', 'On');
ini_set('error_log', '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/php-error.log');

$pdo = new PDO("sqlite:/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/ecorideDatabase.db");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$userInfos = []; // Initialisation de $userInfos comme un tableau vide
$error = null;
$error_chauffeur = null;
$error_trajet = null;
$success = null;
$voitureId = null;


if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];


    try {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE user_id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $userInfos = $stmt->fetchAll(PDO::FETCH_OBJ);
        $success = "Bienvenue," . $userInfos[0]->prenom . " " . $userInfos[0]->nom;
        // var_dump($userInfos); // Garder pour le débogage si nécessaire
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
    }
    try {
        $stmt = $pdo->prepare('SELECT * FROM voitures WHERE user_id = :id');
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $voitureInfos = $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        $error = "vous n'avez pas de voiture enregistrée";
    }
} else {
    header("Location: http://localhost:4000/pages/login.php");
    exit();
}

require_once '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/header.php';



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

if (isset($_FILES["photo_profil"]) && $_FILES["photo_profil"]["error"] == 0) {
    $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
    $filename = $_FILES["photo_profil"]["name"];
    $filetype = $_FILES["photo_profil"]["type"];
    $filesize = $_FILES["photo_profil"]["size"];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    var_dump($ext);


    if (array_key_exists($ext, $allowed) && in_array($filetype, $allowed) && $filesize <= (5 * 1024 * 1024)) { // Exemple de validation
        $new_filename = uniqid() . "." . $ext;

        $upload_dir = "uploads/"; // Assurez-vous que ce dossier existe et est accessible en écriture
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $photo_profil_path = $upload_dir . $new_filename;

        if (!move_uploaded_file($_FILES["photo_profil"]["tmp_name"], $photo_profil_path)) {
            $error = "Erreur lors de l'upload de la photo.";
        } else {
            // Mettre à jour le chemin de la photo de profil dans la base de données
            try {
                $stmt = $pdo->prepare("UPDATE utilisateurs SET photo = :photo_profil WHERE user_id = :id");
                $stmt->bindParam(':photo_profil', $photo_profil_path);
                $stmt->bindParam(':id', $userId);
                $stmt->execute();
                $success = "Informations et photo de profil mises à jour avec succès!";
                $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE user_id = :id");
                $stmt->bindParam(':id', $userId);
                $stmt->execute();
                $userInfos = $stmt->fetchAll(PDO::FETCH_OBJ);
                var_dump($userInfos);
            } catch (PDOException $e) {
                $error = "Erreur lors de la mise à jour du chemin de la photo de profil : " . $e->getMessage();
            }
        }
    } else {
        $error = "Format ou taille de fichier non autorisé pour la photo.";
    }
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
        $success = "Informations mises à jour avec succès!";
        // Récupérer les informations utilisateur mises à jour
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE user_id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $userInfos = $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        $error = "Erreur lors de la mise à jour des informations : " . $e->getMessage();
    }
}


if (isset($_POST['ajouter_vehicule'])) {
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
        $success = "Véhicule ajouté avec succès!";
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
    $voitureId = $_POST['voiture_id'];
}
try {
    $stmt = $pdo->prepare('SELECT voiture_id FROM voitures WHERE user_id = :id');
    $stmt->bindParam(':id', $userId);
    $stmt->execute();

    $voitureId = $stmt->fetchColumn();
} catch (PDOException $e) {
    $error = "vous n'avez pas de voiture enregistrée";
}
if ($voitureId) {
    try {
        $stmt = $pdo->prepare("INSERT INTO covoiturages(lieu_depart, lieu_arrivee, date_depart, heure_depart, date_arrivee, heure_arrivee, prix_personne, nb_place, commentaire, user_id, created_at, voiture_id) VALUES (:lieu_depart, :lieu_arrivee, :date_depart, :heure_depart, :date_arrivee, :heure_arrivee, :prix_personne, :nb_place, :commentaire, :user_id, :created_at, :voiture_id)");
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
        $stmt->bindParam(':voiture_id', $voitureId);
        $stmt->execute();
        $success = "Trajet publié avec succès!";
    } catch (PDOException $e) {
        $error_trajet = "Erreur lors de la publication du trajet : " . $e->getMessage();
    }
}


// Récupérer les informations utilisateur mises à jou


?>
<link rel="stylesheet" href="styles/account.css">

<div style="display: flex;">
    <div class="sidebar">
        <p>Navigation</p> </br>
        <div class="menu">
            <?php if (!empty($userInfos)): ?>
            <?php foreach ($userInfos as $userInfo): ?>
            <img src="<?= $userInfo->photo ?>" alt="Photo de profil" class="photo-utilisateur" width="100"
                height="100"></br>
            <?php endforeach; ?>
            <?php endif; ?>

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


        <?php if (isset($error)) : ?>
        <div class="alert alert-danger container" role="alert">
            <?= $error ?></br>
        </div>
        <?php endif; ?>
        <h1>Profil</h1>
        <div class="profil">
            <?php if (!empty($userInfos)): ?>
            <?php foreach ($userInfos as $userInfo): ?>
            <h3>Informations personnelles</h3>
            <div class="profil-info" id="">

                <form action="" method="post">
                    <div class="form-group">
                        <div class="profil-details">


                        </div>
                        <strong>Nom :</strong><input type="text" name="nom"
                            value="<?= htmlspecialchars($userInfo->nom) ?>" required></br>
                        <strong>Prénom :</strong><input type="text" name="prenom"
                            value="<?= htmlspecialchars($userInfo->prenom) ?>" required></br>
                        <strong>Date de naissance :</strong><input type="date" name="date_naissance"
                            value="<?= htmlspecialchars($userInfo->date_naissance) ?>" required></br>

                        <strong>Email :</strong><input type="email" name="email"
                            value="<?= htmlspecialchars($userInfo->email) ?>" required></br>
                        <strong>Adresse :</strong><input type="text" name="adresse"
                            value="<?= htmlspecialchars($userInfo->adresse) ?>" required></br>
                        <strong>
                            Ville :</strong><input type="text" name="ville"
                            value="<?= htmlspecialchars($userInfo->ville) ?>" required></br>
                        <strong>Numéro de téléphone :</strong><input type="text" name="telephone"
                            value="<?= htmlspecialchars($userInfo->telephone) ?>" required></br>
                        <strong>Nombre de credit restant : </strong><?= htmlspecialchars($userInfo->credits) ?></br>
                        <strong>Etes vous chauffeur ? :</strong><input type="text" name="is_conducteur"
                            value="<?= htmlspecialchars($userInfo->is_conducteur) ?>" required></br>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <p>Aucune information utilisateur trouvée.</p>
                        <?php endif; ?>
                        </br>

                        <div class=" profil-actions">
                            <button type="submit" name="modifier" class="profil-btn">Modifier</button>

                        </div>
                </form>
                <form action="" method="post" enctype="multipart/form-data">
                    <label for="photo_profil">Modifier la photo de profil:</label></br>
                    <input type="file" id="photo_profil" name="photo_profil" accept="image/*"></br>
                    <small>Formats acceptés: JPG, JPEG, PNG, GIF (max 5MB).</small></br>
                    <button type="submit" name="upload" class="upload-btn">Upload</button></br>
                </form>


            </div>
            <div class="ligne-horizontale"></div></br>


            <h1>Ajouter un vehicule</h1> </br>
            <div class="devenir-chauffeur" id="section3">
                <div class="devenir-chauffeur-details">
                    <form action="" method="post">
                        <legend>Informations</legend>
                        <label for="vehicule">Véhicule :</label>
                        <input type="text" name="modele" id="modele_vehicule" placeholder="Modèle de la voiture"
                            required></br>
                        <label for="couleur">Couleur :</label>
                        <input type="text" name="couleur" id="couleur" placeholder="Couleur de la voiture"
                            required></br>
                        <label for="immatriculation">Immatriculation :</label>
                        <input type="text" name="immatriculation" id="immatriculation"
                            placeholder="Numéro d'immatriculation" required></br>
                        <label for="date_premiere_immatriculation">Date d'immatriculation :
                        </label>
                        <input for="date_immatriculation" type="date" name="date_immatriculation"
                            id="date_immatriculation" placeholder="Date d'immatriculation" required></br>

                        <label for="energie">Type de voiture :</label>
                        <select name="energie" id="energie">
                            <option value="essence">Essence</option>
                            <option value="diesel">Diesel</option>
                            <option value="electrique">Electrique</option>
                        </select>
                        <div class="devenir-chauffeur-actions">
                            <button class="devenir-chauffeur-btn" type="submit" name="ajouter_vehicule">Ajouter un
                                vehicule</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="ligne-horizontale"></div></br>
            <h1>Publier un trajet</h1> </br>
            <div class="publier-trajet" id="section2">
                <div class="publier-trajet-details">
                    <form action="" method="post">
                        <legend>Informations du trajet</legend>

                        <div class="form-group">
                            <label for="voiture_id">Voiture :</label>
                            <select class="form-control" id="voiture_id" name="voiture_id" required>
                                <option value="">Sélectionner votre voiture</option>
                                <?php if (!empty($voitureInfos)): ?>
                                <?php foreach ($voitureInfos as $voitureInfo): ?>
                                <option name="voiture_id" value="<?= htmlspecialchars($voitureInfo->voiture_id) ?>">
                                    <?= htmlspecialchars($voitureInfo->modele) ?> (Immatriculation:
                                    <?= htmlspecialchars($voitureInfo->immatriculation) ?>)
                                </option>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <option value="" disabled>Vous n'avez pas de voiture enregistrée.</option>
                                <?php endif; ?>
                            </select>
                            <?php if (empty($voitureInfos)): ?>
                            <small class="form-text text-muted">Vous devez enregistrer une voiture dans votre <a
                                    href="/pages/account.php#section3">profil</a> avant de publier un trajet.</small>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="depart">Ville de départ :</label>
                            <input type="text" class="form-control" name="lieu_depart" id="depart"
                                placeholder="Ville de départ" required>
                        </div>

                        <div class="form-group">
                            <label for="arrivee">Ville d'arrivée :</label>
                            <input type="text" class="form-control" name="lieu_arrivee" id="arrivee"
                                placeholder="Ville d'arrivée" required>
                        </div>

                        <div class="form-group">
                            <label for="date_depart">Date de départ :</label>
                            <input type="date" class="form-control" name="date_depart" id="date_depart"
                                placeholder="Date de départ" required>
                        </div>

                        <div class="form-group">
                            <label for="heure_depart">Heure de départ :</label>
                            <input type="time" class="form-control" name="heure_depart" id="heure_depart"
                                placeholder="Heure de départ" required>
                        </div>

                        <div class="form-group">
                            <label for="date_arrivee">Date d'arrivée (approximative) :</label>
                            <input type="date" class="form-control" name="date_arrivee" id="date_arrivee"
                                placeholder="Date d'arrivée">
                            <small class="form-text text-muted">Facultatif.</small>
                        </div>

                        <div class="form-group">
                            <label for="heure_arrivee">Heure d'arrivée (approximative) :</label>
                            <input type="time" class="form-control" name="heure_arrivee" id="heure_arrivee"
                                placeholder="Heure d'arrivée">
                            <small class="form-text text-muted">Facultatif.</small>
                        </div>

                        <div class="form-group">
                            <label for="prix_personne">Prix par personne (en crédits) :</label>
                            <input type="number" class="form-control" name="prix_personne" id="prix" placeholder="Prix"
                                min="0" required>
                        </div>

                        <div class="form-group">
                            <label for="nb_place">Nombre de places disponibles :</label>
                            <input type="number" class="form-control" name="nb_place" id="places"
                                placeholder="Nombre de places" min="1" required>
                        </div>

                        <div class="form-group">
                            <label for="commentaire">Informations complémentaires :</label>
                            <textarea class="form-control" name="commentaire" id="commentaire" rows="3"
                                placeholder="Ajoutez un commentaire (ex: détails sur le point de rencontre, etc.)"></textarea>
                        </div>

                        <div class="publier-trajet-actions">
                            <button type="submit" class="btn btn-primary publier-trajet-btn" name="publier_trajet"
                                <?php if (empty($voitureInfos)) echo 'disabled'; ?>>
                                Publier le trajet
                            </button>
                        </div>
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
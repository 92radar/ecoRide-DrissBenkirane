<?php
session_start();
// Démarrer la session pour accéder aux variables de session et les modifier
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '256M');
ini_set('log_errors', 'On');
ini_set('error_log', '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/php-error.log');

$pdo = new PDO("sqlite:/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/ecoride.db");
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
    try {
        $stmt = $pdo->prepare("SELECT *, c.duree_heures_minutes AS duree, v.modele AS voiture_modele,
                v.couleur AS voiture_couleur,
                v.immatriculation AS voiture_immatriculation,
                v.energie AS voiture_energie
            FROM covoiturages c
            INNER JOIN voitures v ON c.voiture_id = v.voiture_id
            WHERE c.user_id = :user_id
            ORDER BY c.created_at DESC LIMIT 3");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $covoituragesEnCours = $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        $error = "Erreur lors de la récupération des covoiturages en cours : " . $e->getMessage();
    }
    try {
        $stmt = $pdo->prepare("
            SELECT 
                c.*, 
                p.* FROM 
                participations p 
            LEFT JOIN 
                covoiturages c ON p.covoiturage_id = c.covoiturage_id 
            WHERE 
                p.voyageur_id = :voyageur_id
        ");
        $stmt->bindParam(':voyageur_id', $userId);
        $stmt->execute();
        $resultats = $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        $error = "Erreur lors de la récupération des participations : " . $e->getMessage();
    }
} else {
    header("Location: http://localhost:4000/pages/login.php");
    exit();
}





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
    header("Location: http://localhost:4000/pages/login.php"); // Redirige vers la page home
    exit();
}

if (isset($_FILES["photo_profil"]) && $_FILES["photo_profil"]["error"] == 0) {
    $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
    $filename = $_FILES["photo_profil"]["name"];
    $filetype = $_FILES["photo_profil"]["type"];
    $filesize = $_FILES["photo_profil"]["size"];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);



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
    $pseudo = $_POST['pseudo'];

    try {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = :nom, prenom = :prenom, date_naissance = :date_naissance, email = :email, adresse = :adresse, ville = :ville, telephone = :telephone, pseudo = :pseudo WHERE user_id = :id");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':date_naissance', $date_naissance);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':ville', $ville);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':pseudo', $pseudo);
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

if (isset($_POST['publier_trajet'])) {
    $depart = $_POST['lieu_depart'];
    $arrivee = $_POST['lieu_arrivee'];
    $date_depart = $_POST['date_depart'];
    $heure_depart = $_POST['heure_depart'];
    $date_arrivee = $_POST['date_arrivee'];
    $heure_arrivee = $_POST['heure_arrivee'];
    $prix = $_POST['prix_personne'];
    $places = $_POST['nb_place'];
    $commentaire = $_POST['commentaire'];
    $timeStamp = date('Y-m-d H:i:s');
    $voitureId = $_POST['voiture_id'];
    $prix_publication = $_POST['prix_publication'];

    $duree_secondes = null;
    $duree_heures_minutes = null; // Variable pour stocker la durée formatée

    try {
        $date_depart_str = $date_depart . ' ' . $heure_depart;
        $date_arrivee_str = $date_arrivee . ' ' . $heure_arrivee;

        $depart_dt = new DateTime($date_depart_str);
        $arrivee_dt = new DateTime($date_arrivee_str);

        $diff = $depart_dt->diff($arrivee_dt);

        $duree_secondes = ($diff->d * 24 * 3600) + ($diff->h * 3600) + ($diff->i * 60) + $diff->s;

        $heures = floor($duree_secondes / 3600);
        $minutes = floor(($duree_secondes % 3600) / 60);
        $duree_heures_minutes = sprintf('%dh%02d', $heures, $minutes); // Formatage en "XhYY"

        $stmt = $pdo->prepare("
            INSERT INTO covoiturages(
                lieu_depart,
                lieu_arrivee,
                date_depart,
                heure_depart,
                date_arrivee,
                heure_arrivee,
                prix_personne,
                nb_place,
                commentaire,
                user_id,
                created_at,
                voiture_id,
                duree_heures_minutes
            ) VALUES (
                :lieu_depart,
                :lieu_arrivee,
                :date_depart,
                :heure_depart,
                :date_arrivee,
                :heure_arrivee,
                :prix_personne,
                :nb_place,
                :commentaire,
                :user_id,
                :created_at,
                :voiture_id,
                :duree_heures_minutes
            )
        ");
        $stmt->bindParam(':lieu_depart', $depart);
        $stmt->bindParam(':lieu_arrivee', $arrivee);
        $stmt->bindParam(':date_depart', $date_depart);
        $stmt->bindParam(':heure_depart', $heure_depart);
        $stmt->bindParam(':date_arrivee', $date_arrivee);
        $stmt->bindParam(':heure_arrivee', $heure_arrivee);
        $stmt->bindParam(':prix_personne', $prix);
        $stmt->bindParam(':nb_place', $places, PDO::PARAM_INT);
        $stmt->bindParam(':commentaire', $commentaire);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':created_at', $timeStamp);
        $stmt->bindParam(':voiture_id', $voitureId, PDO::PARAM_INT);
        $stmt->bindParam(':duree_heures_minutes', $duree_heures_minutes); // Lie la durée formatée
        $stmt->execute();

        $success_message = "Votre trajet a été publié avec succès !";
    } catch (PDOException $e) {
        $error_message = "Erreur lors de la publication du trajet : " . $e->getMessage();
    }
    try {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET credits = credits - :prix WHERE user_id = :user_id");
        $stmt->bindParam(':prix', $prix_publication, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();
        var_dump($prix_publication);
        $success = "Crédits mis à jour avec succès!";
    } catch (PDOException $e) {
        $error = "Erreur lors de la mise à jour des crédits : " . $e->getMessage();
    }
}



if (isset($_POST['ajouter_vehicule'])) {
    $marque = $_POST['marque'];
    $modele = $_POST['modele'];
    $couleur = $_POST['couleur'];
    $immatriculation = $_POST['immatriculation'];
    $date_immatriculation = $_POST['date_premiere_immatriculation'];
    $energie = $_POST['energie'];


    try {
        $stmt = $pdo->prepare("INSERT INTO voitures (modele, couleur, immatriculation, date_premiere_immatriculation, energie, user_id, marque) VALUES (:modele, :couleur, :immatriculation, :date_premiere_immatriculation, :energie, :user_id, :marque)");
        $stmt->bindParam(':modele', $modele);
        $stmt->bindParam(':couleur', $couleur);
        $stmt->bindParam(':immatriculation', $immatriculation);
        $stmt->bindParam(':date_premiere_immatriculation', $date_immatriculation);
        $stmt->bindParam(':energie', $energie);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':marque', $marque);
        $stmt->execute();
        $success = "Véhicule ajouté avec succès!";

        header("Location: http://localhost:4000/pages/account.php");
        // Récupérer les informations utilisateur mises à jour

    } catch (PDOException $e) {
        $error_chauffeur = "Erreur lors de l'ajout des informations véhicule : " . $e->getMessage();
    }
}

if (isset($_POST['annuler_trajet'])) {
    $covoiturageId = $_POST['covoiturage_id'];
    $prix_publication = $_POST['prix_publication'];

    try {
        $stmt = $pdo->prepare("DELETE FROM covoiturages WHERE covoiturage_id = :covoiturage_id");
        $stmt->bindParam(':covoiturage_id', $covoiturageId);
        $stmt->execute();
        $success = "Trajet annulé avec succès!";

        header("Location: http://localhost:4000/pages/account.php");
    } catch (PDOException $e) {
        $error = "Erreur lors de l'annulation du trajet : " . $e->getMessage();
    }
    try {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET credits = credits + :prix WHERE user_id = :user_id");
        $stmt->bindParam(':prix', $prix_publication, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();
        var_dump($prix_publication);
        $success = "Crédits mis à jour avec succès!";
    } catch (PDOException $e) {
        $error = "Erreur lors de la mise à jour des crédits : " . $e->getMessage();
    }
}
if (isset($_POST['demarrer_trajet'])) { // Correction de la faute de frappe dans le nom du bouton
    $covoiturageId = $_POST['covoiturage_id'];

    try {
        $stmt = $pdo->prepare("UPDATE covoiturages SET statut = 'en_cours' WHERE covoiturage_id = :covoiturage_id");
        $stmt->bindParam(':covoiturage_id', $covoiturageId);
        $stmt->execute();
        $success = "Trajet démarré avec succès!";

        header("Location: http://localhost:4000/pages/account.php");
        exit(); // Ajout de exit() après la redirection
    } catch (PDOException $e) {

        $error = "Erreur lors du démarrage du trajet : " . $e->getMessage();
    }
}
if (isset($_POST['terminer_trajet'])) { // Correction de la faute de frappe dans le nom du bouton
    $covoiturageId = $_POST['covoiturage_id'];

    try {
        $stmt = $pdo->prepare("UPDATE covoiturages SET statut = 'terminer' WHERE covoiturage_id = :covoiturage_id");
        $stmt->bindParam(':covoiturage_id', $covoiturageId);
        $stmt->execute();
        $success = "Trajet démarré avec succès!";

        header("Location: http://localhost:4000/pages/account.php");
        exit(); // Ajout de exit() après la redirection
    } catch (PDOException $e) {
        $error = "Erreur lors du démarrage du trajet : " . $e->getMessage();
    }
}

if (isset($_POST['poster_avis'])) {
    $covoiturageId = $_POST['covoiturage_id'];
    $commentaire = $_POST['commentaire'];
    $note = $_POST['note'];
    $chauffeur_id = $_POST['chauffeur_id'];
    $prix_personne = $_POST['prix_personne'];

    try {
        $stmt = $pdo->prepare("INSERT INTO avis (covoiturage_id, voyageur_id, commentaire, note, chauffeur_id) VALUES (:covoiturage_id, :voyageur_id, :commentaire, :note, :chauffeur_id)");
        $stmt->bindParam(':covoiturage_id', $covoiturageId);
        $stmt->bindParam(':voyageur_id', $userId);
        $stmt->bindParam(':commentaire', $commentaire);
        $stmt->bindParam(':chauffeur_id', $chauffeur_id);
        $stmt->bindParam(':note', $note);

        $stmt->execute();
        $success = "Avis posté avec succès!";
    } catch (PDOException $e) {
        $error = "Erreur lors de la publication de l'avis : " . $e->getMessage();
    }
    try {
        $stmt = $pdo->prepare("UPDATE Participations SET note = :note, avis = :commentaire WHERE covoiturage_id = :covoiturage_id AND voyageur_id = :voyageur_id");
        $stmt->bindParam(':note', $note);
        $stmt->bindParam(':commentaire', $commentaire);
        $stmt->bindParam(':covoiturage_id', $covoiturageId);
        $stmt->bindParam(':voyageur_id', $userId);
        $stmt->execute();
    } catch (PDOException $e) {
        $error = "Erreur lors de la publication de l'avis : " . $e->getMessage();
    }

    try {
        $stmt = $pdo->prepare('UPDATE avis SET participation_id = (SELECT participation_id FROM participations WHERE covoiturage_id = :covoiturage_id AND voyageur_id = :voyageur_id) WHERE covoiturage_id = :covoiturage_id AND voyageur_id = :voyageur_id');
        $stmt->bindParam(':covoiturage_id', $covoiturageId);
        $stmt->bindParam(':voyageur_id', $userId);
        $stmt->execute();
        $success = "Avis posté avec succès!";
    } catch (PDOException $e) {
        $error = "Erreur lors de la publication de l'avis : " . $e->getMessage();
    }
    try {
        $stmt = $pdo->prepare('SELECT AVG(note) AS moyenne_note FROM avis WHERE chauffeur_id = :chauffeur_id');
        $stmt->bindParam(':chauffeur_id', $chauffeur_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $moyenne_note = $result['moyenne_note'];
        $moyenne_note_arrondi = round($moyenne_note, 1);

        $stmt = $pdo->prepare('UPDATE utilisateurs SET average_note = :moyenne_note WHERE user_id = :chauffeur_id');
        $stmt->bindParam(':moyenne_note', $moyenne_note_arrondi);
        $stmt->bindParam(':chauffeur_id', $chauffeur_id);
        $stmt->execute();
    } catch (PDOException $e) {
        $error = "Erreur lors de la publication de l'avis : " . $e->getMessage();
    }
    try {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET credits = credits + :prix WHERE user_id = :chauffeur_id");
        $stmt->bindParam(':prix', $prix_personne, PDO::PARAM_INT);
        $stmt->bindParam(':chauffeur_id', $chauffeur_id, PDO::PARAM_INT);
        $stmt->execute();

        $success = "Crédits mis à jour avec succès!";
    } catch (PDOException $e) {
        $error = "Erreur lors de la mise à jour des crédits : " . $e->getMessage();
    }
    try {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET credits = credits - :prix WHERE user_id = :user_id");
        $stmt->bindParam(':prix', $prix_personne, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        $error = "Erreur lors de la mise à jour des crédits : " . $e->getMessage();
    }
    try {
        $stmt = $pdo->prepare('UPDATE Participations SET statut = "a_verifier" WHERE covoiturage_id = :covoiturage_id AND voyageur_id = :voyageur_id');
        $stmt->bindParam(':covoiturage_id', $covoiturageId);
        $stmt->bindParam(':voyageur_id', $userId);
        $stmt->execute();
    } catch (PDOException $e) {
        $error = "Erreur lors de la publication de l'avis : " . $e->getMessage();
    }
    header("Location: http://localhost:4000/pages/account.php");
    exit();
}

require_once '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/header.php';


?>
<link rel="stylesheet" href="styles/account.css">


<div style="display: flex;">
    <div class="sidebar">
        <p>Navigation</p> </br>
        <div class="menu">
            <?php if (!empty($userInfos)): ?>
                <?php foreach ($userInfos as $userInfo): ?>
                    <img src="<?= $userInfo->photo ?>" alt="Photo de profil" class="photo-utilisateur" width="100"
                        height="100"></br></br></br>
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
                                <strong>Nom :</strong></br><input class="form-control" type="text" name="nom"
                                    value="<?= htmlspecialchars($userInfo->nom) ?>" required></br>
                                <strong>Prénom :</strong></br><input class="form-control" type="text" name="prenom"
                                    value="<?= htmlspecialchars($userInfo->prenom) ?>" required></br>
                                <strong>Pseudo :</strong></br><input class="form-control" type="text" name="pseudo"
                                    value="<?= htmlspecialchars($userInfo->pseudo) ?>" required></br>
                                <strong>Date de naissance :</strong></br><input class="form-control" type="date"
                                    name="date_naissance" value="<?= htmlspecialchars($userInfo->date_naissance) ?>"
                                    required></br>

                                <strong>Email :</strong></br><input class="form-control" type="email" name="email"
                                    value="<?= htmlspecialchars($userInfo->email) ?>" required></br>
                                <strong>Adresse :</strong></br><input class="form-control" type="text" name="adresse"
                                    value="<?= htmlspecialchars($userInfo->adresse) ?>" required></br>
                                <strong>
                                    Ville :</strong></br><input class="form-control" type="text" name="ville"
                                    value="<?= htmlspecialchars($userInfo->ville) ?>" required></br>
                                <strong>Numéro de téléphone :</strong></br><input class="form-control" type="text"
                                    name="telephone" value="<?= htmlspecialchars($userInfo->telephone) ?>" required></br>
                                <strong class="form-control">Nombre de credit restant :
                                    <?= htmlspecialchars($userInfo->credits) ?></strong></br>
                                <strong class="form-control">Note moyenne :


                                    <?= htmlspecialchars($userInfo->average_note) ?>⭐</strong></br>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Aucune information utilisateur trouvée.</p>
                        <?php endif; ?>
                        </br>

                        <div class=" profil-actions">
                            <button type="submit" name="modifier" class="profil-btn">Modifier</button></br>

                        </div></br>
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
                                <div class="form-group">
                                    <label for="marque_vehicule">Marque du véhicule :</label>
                                    <input type="text" class="form-control" name="marque" id="marque"
                                        placeholder="Marque de la voiture" required>
                                    <label for="vehicule">Modele du véhicule :</label>
                                    <input type="text" class="form-control" name="modele" id="modele"
                                        placeholder="modele de la voiture" required>
                                </div>

                                <div class="form-group">
                                    <label for="couleur">Couleur :</label>
                                    <input type="text" class="form-control" name="couleur" id="couleur_voiture"
                                        placeholder="Couleur du vehicule, ex: bleu" required>
                                </div>

                                <div class="form-group">
                                    <label for="immatriculation">Immatriculation :</label>
                                    <input type="text" class="form-control" name="immatriculation" id="immatriculation_vehicule"
                                        placeholder="Immatriculation" required>
                                </div>

                                <div class="form-group">
                                    <label for="date_immatriculation">Date d'immatriculation :</label>
                                    <input type="date" class="form-control" name="date_premiere_immatriculation"
                                        id="date_premiere_immatriculation" placeholder="Date de la premiere immatriculation"
                                        required>
                                </div>
                                <div class="form-group">

                                    <label for="energie">Type de voiture :</label></br>
                                    <select class="form-control" name="energie" id="energie">
                                        <option value="Essence">Essence</option>
                                        <option value="Diesel">Diesel</option>
                                        <option value="Electrique">Electrique</option>
                                    </select></br>
                                </div>



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
                                    <label for="date_arrivee">Date d'arrivée :</label>
                                    <input type="date" class="form-control" name="date_arrivee" id="date_arrivee"
                                        placeholder="Date d'arrivée">
                                    <small class="form-text text-muted">Facultatif.</small>
                                </div>

                                <div class="form-group">
                                    <label for="heure_arrivee">Heure d'arrivée :</label>
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
                                <div class="form-group">
                                    <label for="prix_publication">Nombre de credit prelevé pour publication d'un trajet
                                        :</label>
                                    <strong type="number" class="form-control" id="prix_publication">2</strong>
                                    <input type="hidden" type="number" name="prix_publication" id="prix_publication" value="2">
                                </div></br>

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

                    <?php if (!empty($covoituragesEnCours)) : ?>
                        <?php foreach ($covoituragesEnCours as $covoiturage): ?>
                            <strong>Départ :</strong> <?= htmlspecialchars($covoiturage->lieu_depart) ?>
                            <br>
                            <strong>Arrivée :</strong> <?= htmlspecialchars($covoiturage->lieu_arrivee) ?>
                            <br>
                            <strong>Date :</strong><?= htmlspecialchars($covoiturage->date_depart) ?>
                            </br>
                            <strong>Statut :</strong><?= htmlspecialchars($covoiturage->statut) ?>
                            <br>
                            <div class="ligne-horizontale"></div></br>

                        <?php endforeach; ?>
                    <?php else : ?>
                        <p>Vous n'avez pas de covoiturage en cours.</p>
                    <?php endif; ?>

                    <div class="ligne-horizontale"></div></br>
                    <h1 id="section5">Co-voiturage en cours</h1>
                    <?php if (!empty($covoituragesEnCours)) : ?>
                        <?php foreach ($covoituragesEnCours as $covoiturage): ?>
                            <div class="publication-cadre">
                                <div class="publication-header">
                                    <div class="utilisateur-info">
                                        <span class="date-creation">**Publié le :
                                            <?= htmlspecialchars($covoiturage->created_at) ?>**</span>
                                        </span>
                                    </div>
                                </div>

                                <div class="publication-details">
                                    <div class="trajet">
                                        <h3>Trajet</h3>
                                        <p>
                                            <strong>Départ :</strong> <?= htmlspecialchars($covoiturage->lieu_depart) ?>
                                            <br>
                                            <strong>Arrivée :</strong> <?= htmlspecialchars($covoiturage->lieu_arrivee) ?></br>
                                            <strong>Durée du trajet :</strong> <?= htmlspecialchars($covoiturage->duree) ?>
                                        </p>
                                    </div>

                                    <div class="dates">
                                        <h3>Dates et Horaires</h3>
                                        <p>
                                            <strong>Départ :</strong>
                                            <?= htmlspecialchars(date('d/m/Y', strtotime($covoiturage->date_depart))) ?> à
                                            <?= htmlspecialchars(date('H:i', strtotime($covoiturage->heure_depart))) ?> h
                                            <br>
                                            <strong>Arrivée :</strong>
                                            <?= htmlspecialchars(date('d/m/Y', strtotime($covoiturage->date_arrivee))) ?> à
                                            <?= htmlspecialchars(date('H:i', strtotime($covoiturage->heure_arrivee))) ?> h
                                        </p>
                                    </div>

                                    <div class="informations">
                                        <h3>Informations</h3>
                                        <p>
                                            <strong>Type de voiture :</strong> <?= htmlspecialchars($covoiturage->energie) ?>
                                            <br>
                                            <strong>Places disponibles :</strong> <?= htmlspecialchars($covoiturage->nb_place) ?>
                                            <br>
                                            <strong>Prix par place :</strong> <?= htmlspecialchars($covoiturage->prix_personne) ?>
                                            Credits
                                        </p>
                                    </div>

                                    <div class="publication-actions">
                                        <form method="post">
                                            <input type="hidden" name="covoiturage_id"
                                                value="<?= htmlspecialchars($covoiturage->covoiturage_id) ?>">
                                            <button type="submit" name="demarrer_trajet" class="btn btn-success"
                                                <?= ($covoiturage->statut !== 'en_attente') ? 'disabled' : '' ?>>
                                                Démarrer le trajet
                                            </button>
                                            <button type="submit" name="terminer_trajet" class="btn btn-danger"
                                                <?= ($covoiturage->statut !== 'en_cours') ? 'disabled' : '' ?>>
                                                Terminer le trajet
                                            </button>
                                            <input type="hidden" type="number" name="prix_publication" id="prix_publication" value="2">
                                            <button type="submit" name="annuler_trajet" class="btn btn-warning"
                                                <?= ($covoiturage->statut !== 'en_attente') ? 'disabled' : '' ?>>
                                                Annuler le trajet
                                            </button>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p>Vous n'avez pas de covoiturage en cours.</p>
                    <?php endif; ?>

                    <div class="ligne-horizontale"></div></br>
                    <h1 id="section6">Laisser un avis et une note</h1>


                    <?php foreach ($resultats as $resultat) : ?>
                        <?php if ($resultat->statut === "terminer") : ?>
                            <div class="publication-cadre">
                                <div class="publication-header">
                                    <div class="utilisateur-info">

                                        <span class="date-creation">**Publié le :
                                            <?= htmlspecialchars($resultat->created_at) ?>**</span>
                                    </div>
                                </div>

                                <div class="publication-details">
                                    <div class="trajet">
                                        <h3>Trajet</h3>
                                        <p>
                                            <strong>Départ :</strong> <?= htmlspecialchars($resultat->lieu_depart) ?><br>
                                            <strong>Arrivée :</strong> <?= htmlspecialchars($resultat->lieu_arrivee) ?>
                                        </p>
                                    </div>

                                    <div class="dates">
                                        <h3>Dates et Horaires</h3>
                                        <p>
                                            <strong>Départ :</strong>
                                            <?= htmlspecialchars(date('d/m/Y', strtotime($resultat->date_depart))) ?> à
                                            <?= htmlspecialchars(date('H:i', strtotime($resultat->heure_depart))) ?> h<br>
                                            <strong>Arrivée :</strong>
                                            <?= htmlspecialchars(date('d/m/Y', strtotime($resultat->date_arrivee))) ?> à
                                            <?= htmlspecialchars(date('H:i', strtotime($resultat->heure_arrivee))) ?> h
                                        </p>
                                    </div>

                                </div></br>

                                <div class="avis-form">
                                    <h1>Laisser un avis :</h1>
                                    <form method="post">
                                        <input type="hidden" name="covoiturage_id"
                                            value="<?= htmlspecialchars($resultat->covoiturage_id) ?>">
                                        <input type="hidden" type="number" name="prix_personne"
                                            value="<?= htmlspecialchars($resultat->prix_personne) ?>">
                                        <input type="hidden" name="chauffeur_id"
                                            value="<?= htmlspecialchars($resultat->chauffeur_id) ?>">
                                        <div class="form-group">
                                            <label for="note">Note (sur 5) :</label>
                                            <select class="form-control" name="note" id="note">
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="commentaire">Commentaire :</label>
                                            <textarea class="form-control" name="commentaire" id="commentaire" rows="3"></textarea>
                                        </div>
                                        <button type="submit" name="poster_avis" class="btn btn-success">Poster votre avis</button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

        </div>
    </div>
</div>
</body>

</html>
<?php
session_start();
// Démarrer la session pour accéder aux variables de session et les modifier






ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '256M');
ini_set('log_errors', 'On');
ini_set('error_log', '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/php-error.log');


require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

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
    header("Location: http://localhost:4000/public/login.php"); // Redirige vers la page home
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

        header("Location: http://localhost:4000/public/account.php");
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

        header("Location: http://localhost:4000/public/account.php");
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

        header("Location: http://localhost:4000/public/account.php");
        exit(); // Ajout de exit() après la redirection
    } catch (PDOException $e) {

        $error = "Erreur lors du démarrage du trajet : " . $e->getMessage();
    }
}

if (isset($_POST['terminer_trajet'])) {
    $covoiturageId = $_POST['covoiturage_id'];

    try {
        // Mise à jour du statut
        $stmt = $pdo->prepare("UPDATE covoiturages SET statut = 'terminer' WHERE covoiturage_id = :covoiturage_id");
        $stmt->bindParam(':covoiturage_id', $covoiturageId);
        $stmt->execute();
        $success = "Trajet terminé avec succès !<br>";
    } catch (PDOException $e) {
        exit("Erreur lors de la mise à jour du trajet : " . $e->getMessage());
    }

    try {
        // Récupère les voyageurs liés au trajet
        $stmt = $pdo->prepare('SELECT voyageur_id FROM participations WHERE covoiturage_id = :covoiturage_id');
        $stmt->bindParam(':covoiturage_id', $covoiturageId);
        $stmt->execute();
        $voyageurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($voyageurs as $voyageur) {
            // Récupère leur email
            $stmtEmail = $pdo->prepare('SELECT email FROM utilisateurs WHERE user_id = :voyageur_id');
            $stmtEmail->bindParam(':voyageur_id', $voyageur['voyageur_id']);
            $stmtEmail->execute();
            $emailData = $stmtEmail->fetch(PDO::FETCH_ASSOC);

            if ($emailData && isset($emailData['email'])) {
                $destinataire = $emailData['email'];

                // Envoi d’email via PHPMailer
                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'eco.ride.studi@gmail.com'; // ← Mon adresse Gmail
                    $mail->Password = 'ewiv oucj nytx nlek'; // ← Mot de passe d'application
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = 465;

                    $mail->setFrom('eco.ride.studi@gmail.com', 'ecoride');
                    $mail->addAddress($destinataire);

                    $mail->isHTML(true);
                    $mail->Subject = 'Confirmation de fin de trajet';
                    $mail->Body = '
                        <p>Bonjour,</p>
                        <p>Votre trajet est à partir de maintenant finis. Merci pour votre participation sur <strong>Ecoride</strong> !</p>
                        <p>Rendez-vous dans votre espace personnel pour laisser un avis ou consulter les details.</p>
                        <p>Cordialement,<br>L’equipe Ecoride</p>';
                    $mail->AltBody = 'Votre trajet est finis. Rendez-vous sur votre espace personnel Ecoride.';

                    $mail->send();
                    $success .= "Email envoyé aux participants avec succès !<br>";
                } catch (Exception $e) {
                    $success .= "Erreur lors de l'envoi à " . htmlspecialchars($destinataire) . ": " . $mail->ErrorInfo . "<br>";
                }
            }
        }
    } catch (PDOException $e) {
        exit("Erreur lors de l'envoi des emails : " . $e->getMessage());
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
    header("Location: http://localhost:4000/public/account.php");
    exit();
}

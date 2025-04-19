<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 'On');
ini_set('error_log', '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/php-error.log');

$userInfos = null; // Initialisation de $userInfos
$userDetails = null; // Initialisation de $userDetails
$avisDetails = null; // Initialisation de $avisDetails
$pdo = new PDO("sqlite:/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/ecoride.db");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $userId = $_SESSION['user_id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $userInfos = $stmt->fetchAll(PDO::FETCH_OBJ); // Utilisation de fetchAll pour récupérer tous les résultats
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération des informations de l'utilisateur : " . $e->getMessage();
    }
    try {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_OBJ); // Utilisation de fetchAll pour récupérer tous les résultats
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération des informations de l'utilisateur : " . $e->getMessage();
    }


    $debutPeriodeCovoit = date('Y-m-d', strtotime('-6 days'));
    $finPeriodeCovoit = date('Y-m-d');


    try { // Requête SQL pour compter le nombre de covoiturages par jour pour les 7 derniers jours
        $sqlCovoit = "SELECT DATE(date_depart) AS jour, COUNT(*) AS nombre_covoiturages
            FROM covoiturages
            WHERE date_depart >= :debut AND date_depart <= :fin
            GROUP BY DATE(date_depart)
            ORDER BY DATE(date_depart)";

        $stmtCovoit = $pdo->prepare($sqlCovoit);
        $stmtCovoit->bindParam(':debut', $debutPeriodeCovoit);
        $stmtCovoit->bindParam(':fin', $finPeriodeCovoit);
        $stmtCovoit->execute();
        $resultatsCovoit = $stmtCovoit->fetchAll(PDO::FETCH_ASSOC);

        $joursCovoiturages = [];
        $nombresCovoiturages = [];
        $covoituragesParJour = [];
        foreach ($resultatsCovoit as $row) {
            $covoituragesParJour[$row['jour']] = $row['nombre_covoiturages'];
        }

        // Générer un tableau des 7 derniers jours pour s'assurer qu'ils sont tous présents
        $joursPeriodeCovoit = [];
        for ($i = 0; $i < 7; $i++) {
            $joursPeriodeCovoit[] = date('Y-m-d', strtotime("-$i days"));
        }
        $joursPeriodeCovoit = array_reverse($joursPeriodeCovoit); // Inverser pour avoir l'ordre chronologique

        foreach ($joursPeriodeCovoit as $date) {
            $joursCovoiturages[] = date('d/m', strtotime($date)); // Format d'affichage du jour
            $nombresCovoiturages[] = isset($covoituragesParJour[$date]) ? $covoituragesParJour[$date] : 0;
        }
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération des covoiturages : " . $e->getMessage();
    }
    $debutPeriode = date('Y-m-d', strtotime('-6 days'));
    $finPeriode = date('Y-m-d');

    // Requête SQL pour calculer le total des crédits gagnés par jour

    try {
        $stmt = $pdo->prepare("SELECT DATE(date_depart) AS jour, SUM(credit_depense) AS total_credit
        FROM participations
        WHERE date_depart >= :debut AND date_depart <= :fin
        GROUP BY DATE(date_depart)
        ORDER BY DATE(date_depart)");
        $stmt->bindParam(':debut', $debutPeriode);
        $stmt->bindParam(':fin', $finPeriode);
        $stmt->execute();
        $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $jours = [];
        $credits = [];
        $creditsParJour = [];
        foreach ($resultats as $row) {
            $creditsParJour[$row['jour']] = $row['total_credit'];
        }

        // Générer un tableau des 7 derniers jours pour s'assurer qu'ils sont tous présents
        $joursPeriode = [];
        for ($i = 0; $i < 7; $i++) {
            $joursPeriode[] = date('Y-m-d', strtotime("-$i days"));
        }
        $joursPeriode = array_reverse($joursPeriode); // Inverser pour avoir l'ordre chronologique

        foreach ($joursPeriode as $date) {
            $jours[] = date('d/m', strtotime($date)); // Format d'affichage du jour
            $credits[] = isset($creditsParJour[$date]) ? floatval($creditsParJour[$date]) : 0; // Assurer un type numérique
        }
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération des crédits : " . $e->getMessage();
    }
} else {
    header("Location: http://localhost:4000/pages/home.php"); // Redirige vers la page home
    exit();
}
if (isset($_POST['modifier'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $dateNaissance = $_POST['date_naissance'];
    $email = $_POST['email'];
    $adresse = $_POST['adresse'];
    $ville = $_POST['ville'];
    $telephone = $_POST['telephone'];
    $pseudo = $_POST['pseudo'];

    try {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = :nom, prenom = :prenom, date_naissance = :date_naissance, email = :email, adresse = :adresse, ville = :ville, telephone = :telephone, pseudo = :pseudo WHERE user_id = :user_id");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':date_naissance', $dateNaissance);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':ville', $ville);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':pseudo', $pseudo);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $success = "Vos informations ont été mises à jour avec succès.";
    } catch (PDOException $e) {
        $error = "Erreur lors de la mise à jour des informations : " . $e->getMessage();
    }
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


if (isset($_POST['creer_compte_employe'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $dateNaissance = $_POST['date_naissance'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $telephone = $_POST['telephone'];
    $adresse = $_POST['adresse'];
    $ville = $_POST['ville'];
    $pseudo = $_POST['pseudo'];
    $role = $_POST['role'];



    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    try {
        $stmtUtilisateur = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, date_naissance, email, password, telephone, adresse, ville, pseudo, role)
            VALUES (:nom, :prenom, :date_naissance, :email, :password, :telephone, :adresse, :ville, :pseudo, :role)");
        $stmtUtilisateur->bindParam(':nom', $nom);
        $stmtUtilisateur->bindParam(':prenom', $prenom);
        $stmtUtilisateur->bindParam(':date_naissance', $dateNaissance);
        $stmtUtilisateur->bindParam(':email', $email);
        $stmtUtilisateur->bindParam(':password', $hashedPassword);
        $stmtUtilisateur->bindParam(':telephone', $telephone);
        $stmtUtilisateur->bindParam(':adresse', $adresse);
        $stmtUtilisateur->bindParam(':ville', $ville);
        $stmtUtilisateur->bindParam(':pseudo', $pseudo);
        $stmtUtilisateur->bindParam(':role', $role);
        $stmtUtilisateur->execute();
        $success = "Votre compte a été créé avec succès";
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}
if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {

    $selectedUserId = $_GET['user_id'];
    try {
        $stmtUserDetails = $pdo->prepare("SELECT *
                                               FROM utilisateurs WHERE user_id = :user_id");
        $stmtUserDetails->bindParam(':user_id', $selectedUserId, PDO::PARAM_INT);
        $stmtUserDetails->execute();
        $userDetails = $stmtUserDetails->fetchAll(PDO::FETCH_OBJ);
        if ($userDetails) {
            $userDetails = $userDetails[0]; // Récupérer le premier élément
        }
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}
if (isset($_POST['changer_role'])) {
    $userId = $_POST['user_id'];
    $role = $_POST['user_role'];

    try {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET role = :role WHERE user_id = :user_id");
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $success = "Le rôle de l'employé a été mis à jour avec succès.";
    } catch (PDOException $e) {
        $error = "Erreur lors de la mise à jour du rôle : " . $e->getMessage();
    }
}
if (isset($_POST['supprimer_compte'])) {
    $userId = $_POST['user_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $success = "Le compte a été supprimé avec succès.";
    } catch (PDOException $e) {
        $error = "Erreur lors de la suppression du compte : " . $e->getMessage();
    }
}

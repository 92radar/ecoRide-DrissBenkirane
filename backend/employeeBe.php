<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 'On');
ini_set('error_log', '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/php-error.log');

$avisDetails = null; // Initialisation de $avisDetails
$pdo = new PDO("sqlite:/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/ecoride.db");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
if (isset($_SESSION['role']) && $_SESSION['role'] === 'employee') {
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
        $stmt = $pdo->prepare("
            SELECT 
                *
                
            FROM Participations p
            INNER JOIN avis a ON p.voyageur_id = a.voyageur_id
            INNER JOIN utilisateurs u ON a.voyageur_id = u.user_id
            
        ");


        $stmt->execute();
        $avisVerifie = $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération des données : " . $e->getMessage();
    }
    try {
        $stmt = $pdo->prepare("
            SELECT 
                *
                
            FROM Participations p
            INNER JOIN avis a ON p.voyageur_id = a.voyageur_id
            INNER JOIN utilisateurs u ON a.voyageur_id = u.user_id
            WHERE statut_avis = 'en_attente'
        ");


        $stmt->execute();
        $avis = $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération des données : " . $e->getMessage();
    }
} else {
    header("Location: http://localhost:4000/public/home.php"); // Redirige vers la page home
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
            } catch (PDOException $e) {
                $error = "Erreur lors de la mise à jour du chemin de la photo de profil : " . $e->getMessage();
            }
        }
    } else {
        $error = "Format ou taille de fichier non autorisé pour la photo.";
    }
}
if (isset($_GET['avis_id']) && !empty($_GET['avis_id'])) {

    $selectedAvisId = $_GET['avis_id'];
    try {
        $stmtAvisDetails = $pdo->prepare("SELECT *
                                               FROM avis a
                                               INNER JOIN utilisateurs u ON a.voyageur_id = u.user_id
                                               WHERE a.avis_id = :avis_id");
        $stmtAvisDetails->bindParam(':avis_id', $selectedAvisId, PDO::PARAM_INT);
        $stmtAvisDetails->execute();
        $avisDetails = $stmtAvisDetails->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        $error = "Erreur lors de la récupération des détails de l'avis : " . $e->getMessage();
    }
    if ($avisDetails) {
        $avisDetails = $avisDetails[0]; // Récupérer le premier élément
    }


    if (isset($_POST['changer_statut'])) {
        $statutAvis = $_POST['statut_avis'];
        $commentaire = $_POST['commentaire'];


        try {
            $stmt = $pdo->prepare("UPDATE avis SET statut_avis = :statut_avis, commentaire = :commentaire WHERE avis_id = :avis_id");
            $stmt->bindParam(':statut_avis', $statutAvis);
            $stmt->bindParam(':commentaire', $commentaire);

            $stmt->bindParam(':avis_id', $selectedAvisId, PDO::PARAM_INT);
            $stmt->execute();
            $success = "Statut de l'avis mis à jour avec succès.";
            // Redirection après la mise à jour
            header("Location: http://localhost:4000/pages/employee.php");
            exit();
        } catch (PDOException $e) {
            $error = "Erreur lors de la mise à jour du statut de l'avis : " . $e->getMessage();
        }
        if (isset($statutAvis) && $statutAvis == 'validé') {
            try {
                $stmt = $pdo->prepare("UPDATE Participations SET statut = 'validé' WHERE voyageur_id = :voyageur_id");
                $stmt->bindParam(':voyageur_id', $avisDetails->voyageur_id, PDO::PARAM_INT);
                $stmt->execute();
            } catch (PDOException $e) {
                $error = "Erreur lors de la mise à jour du statut de l'avis : " . $e->getMessage();
            }
        } elseif (isset($statutAvis) && $statutAvis == 'refuser') {
            try {
                $stmt = $pdo->prepare("UPDATE Participations SET statut = 'refuser' WHERE voyageur_id = :voyageur_id");
                $stmt->bindParam(':voyageur_id', $avisDetails->voyageur_id, PDO::PARAM_INT);
                $stmt->execute();
            } catch (PDOException $e) {
                $error = "Erreur lors de la mise à jour du statut de l'avis : " . $e->getMessage();
            }
        }
    }
}

<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 'On');
ini_set('error_log', '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/php-error.log');

if (isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == true && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    $pdo = new PDO("sqlite:/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/ecoride.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $covoiturageInfo = null; // Initialisation de $covoiturageInfo

    if (isset($_GET['covoiturage_id'])) {
        $covoiturage_id = $_GET['covoiturage_id'];

        try {
            $stmt = $pdo->prepare("
            SELECT c.*, u.nom AS nom, u.prenom AS prenom, v.energie AS energie, v.modele AS modele, u.photo AS photo
            FROM covoiturages c
            LEFT JOIN utilisateurs u ON c.user_id = u.user_id
            LEFT JOIN voitures v ON c.voiture_id = v.voiture_id
            WHERE c.covoiturage_id = :covoiturage_id
        ");
            $stmt->bindParam(':covoiturage_id', $covoiturage_id, PDO::PARAM_INT);
            $stmt->execute();
            $covoiturageInfo = $stmt->fetch(PDO::FETCH_OBJ); // Utilisation de fetch(PDO::FETCH_OBJ)
            // var_dump($covoiturageInfo);
        } catch (PDOException $e) {
            echo "Erreur lors de la récupération des informations du covoiturage : " . $e->getMessage();
        }
    }


    if (isset($_POST['confirmer_participation'])) {
        $covoiturage_id = $_POST['covoiturage_id'];
        $nb_place = $_POST['nb_place'];
        $credit_depense = $covoiturageInfo->prix_personne * $nb_place;
        $date_depart = $covoiturageInfo->date_depart;

        try {
            $stmt = $pdo->prepare("SELECT nb_place FROM covoiturages WHERE covoiturage_id = :covoiturage_id");
            $stmt->bindParam(':covoiturage_id', $covoiturage_id, PDO::PARAM_INT);
            $stmt->execute();
            $covoiturage = $stmt->fetch(PDO::FETCH_OBJ);

            if ($covoiturage && $covoiturage->nb_place >= $nb_place) {
                // Mettre à jour le nombre de places restantes
                $new_nb_place = $covoiturage->nb_place - $nb_place;
                $update_stmt = $pdo->prepare("UPDATE covoiturages SET nb_place = :new_nb_place WHERE covoiturage_id = :covoiturage_id");
                $update_stmt->bindParam(':new_nb_place', $new_nb_place, PDO::PARAM_INT);
                $update_stmt->bindParam(':covoiturage_id', $covoiturage_id, PDO::PARAM_INT);
                $update_stmt->execute();
                try {
                    $stmt = $pdo->prepare("INSERT INTO Participations (covoiturage_id, voyageur_id, nb_place, statut, chauffeur_id, date_depart, credit_depense)  VALUES (:covoiturage_id, :voyageur_id, :nb_place, 'en attente', :chauffeur_id, :date_depart, :credit_depense)");
                    $stmt->bindParam(':nb_place', $nb_place, PDO::PARAM_INT);
                    $stmt->bindParam(':covoiturage_id', $covoiturage_id, PDO::PARAM_INT);
                    $stmt->bindParam(':voyageur_id', $_SESSION['user_id'], PDO::PARAM_INT);
                    $stmt->bindParam(':chauffeur_id', $covoiturageInfo->user_id, PDO::PARAM_INT);
                    $stmt->bindParam(':date_depart', $date_depart, PDO::PARAM_STR);
                    $stmt->bindParam(':credit_depense', $credit_depense, PDO::PARAM_INT);

                    $stmt->execute();
                    $success_message = "Participation confirmée avec succès !";
                } catch (PDOException $e) {
                    $error = "Erreur lors de la confirmation de la participation : " . $e->getMessage();
                }
            } else {
                echo "Nombre de places insuffisant.";
            }
        } catch (PDOException $e) {
            $error = "Erreur lors de la confirmation de la participation : " . $e->getMessage();
        }
        // Logique pour confirmer la participation
    }
}

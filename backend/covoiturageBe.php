<?php

$pdo = new PDO("sqlite:/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/ecoride.db");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);









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
    header("Location: http://localhost:4000/public/index.php"); // Redirige vers la page home
    exit();
}




$researcheResult = [];

$success = null;
$error = null;

if (isset($_GET['depart']) && isset($_GET['arrivee']) && isset($_GET['date'])) {
    $depart = $_GET['depart'];
    $arrivee = $_GET['arrivee'];
    $date = $_GET['date']; {
        try {
            $researchStmt = $pdo->prepare("SELECT COUNT(*) FROM covoiturages WHERE lieu_depart = :lieu_depart AND lieu_arrivee = :lieu_arrivee AND nb_place > 0 AND statut = 'en_attente'");
            $researchStmt->bindParam(':lieu_depart', $depart, PDO::PARAM_STR);
            $researchStmt->bindParam(':lieu_arrivee', $arrivee, PDO::PARAM_STR);

            $researchStmt->execute();
            $resultNumber = $researchStmt->fetchAll(PDO::FETCH_ASSOC);
            $resultNumber = $resultNumber[0];
            $count = $resultNumber['COUNT(*)'];
            $countSuccess = 'Nombre de covoiturages trouvés : ' . $count;


            if ($count > 0) {
                $researchStmt = $pdo->prepare("SELECT 
                c.*, 
                u.nom AS nom, 
                u.average_note AS average_note,
                u.photo AS photo,
                u.prenom AS prenom, 
                v.energie AS energie, 
                c.statut AS statut,
                c.duree_heures_minutes AS duree
            FROM covoiturages c
            INNER JOIN utilisateurs u ON c.user_id = u.user_id
            INNER JOIN voitures v ON c.voiture_id = v.voiture_id
            WHERE c.nb_place > 0
            AND c.statut = 'en_attente'
              AND c.lieu_depart = :lieu_depart
              AND c.lieu_arrivee = :lieu_arrivee
              AND c.date_depart LIKE :date_depart ORDER BY c.date_depart ASC
        ");

                $researchStmt->bindParam(':lieu_depart', $depart, PDO::PARAM_STR);
                $researchStmt->bindParam(':lieu_arrivee', $arrivee, PDO::PARAM_STR);
                $researchStmt->bindValue(':date_depart', $date . '%', PDO::PARAM_STR);

                $researchStmt->execute();
                $researcheResult = $researchStmt->fetchAll(PDO::FETCH_OBJ);
                $success = 'Recherche effectuée avec succès. ';
            } else {
                $error = 'Aucun covoiturage trouvé';
                $researcheResult = [];
            }
        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }
}



if (isset($_POST['search'])) {
    $depart = $_POST['depart'];
    $arrivee = $_POST['arrivee'];
    $date = $_POST['date'];
    try {
        $researchStmt = $pdo->prepare("SELECT COUNT(*) FROM covoiturages WHERE lieu_depart = :lieu_depart AND lieu_arrivee = :lieu_arrivee AND date_depart =
:date_depart AND nb_place > 0 AND statut = 'en_attente' ");
        $researchStmt->bindParam(':lieu_depart', $depart, PDO::PARAM_STR);
        $researchStmt->bindParam(':lieu_arrivee', $arrivee, PDO::PARAM_STR);
        $researchStmt->bindParam(':date_depart', $date, PDO::PARAM_STR);
        $researchStmt->execute();
        $resultNumber = $researchStmt->fetchAll(PDO::FETCH_ASSOC);
        $resultNumber = $resultNumber[0];
        $count = $resultNumber['COUNT(*)'];
        $countSuccess = 'Nombre de covoiturages trouvés : ' . $count;


        if ($count > 0) {

            $researchStmt = $pdo->prepare("SELECT c.*, u.nom AS nom, u.prenom AS prenom, 
            v.energie AS energie, c.statut AS statut FROM covoiturages c LEFT JOIN utilisateurs u ON c.user_id = u.user_id LEFT JOIN voitures v ON c.voiture_id = v.voiture_id
     WHERE c.nb_place > 0
        AND c.statut = 'en_attente'
     AND c.lieu_depart = :lieu_depart
        AND c.lieu_arrivee = :lieu_arrivee
        AND c.date_depart LIKE :date_depart

                ");

            $researchStmt->bindParam(':lieu_depart', $depart, PDO::PARAM_STR);
            $researchStmt->bindParam(':lieu_arrivee', $arrivee, PDO::PARAM_STR);
            $researchStmt->bindValue(':date_depart', $date . '%', PDO::PARAM_STR);

            $researchStmt->execute();
            $researcheResult = $researchStmt->fetchAll(PDO::FETCH_OBJ);
            $success = 'Recherche effectuée avec succès. ';
        } else {
            $error = 'Aucun covoiturage trouvé';
            $researcheResult = [];
        }
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}




if (isset($_POST['participer'])) {

    $covoiturage_id = $_POST['covoiturage_id'];
    header("Location: http://localhost:4000/pages/participer.php?covoiturage_id=$covoiturage_id");
}

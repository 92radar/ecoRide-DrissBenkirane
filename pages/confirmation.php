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
require_once '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/header.php';
?>



<div style="display: flex;">
    <div style="width: 250px; padding: 20px; background-color: #f0f0f0;">
        <h2>Confirmation de participation</h2>

        <?php if ($covoiturageInfo): ?>
            <p> confirmer votre participation à ce covoiturage ?</p>

            <p>
                <strong>Départ :</strong> <?= htmlspecialchars($covoiturageInfo->lieu_depart) ?><br>
                <strong>Arrivée :</strong> <?= htmlspecialchars($covoiturageInfo->lieu_arrivee) ?><br>
                <strong>Date :</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($covoiturageInfo->date_depart))) ?><br>
                <strong>Heure :</strong> <?= htmlspecialchars($covoiturageInfo->heure_depart) ?><br>
                <strong>Prix :</strong> <?= htmlspecialchars($covoiturageInfo->prix_personne) ?> Credits<br>
                <strong>Places restantes :</strong> <?= htmlspecialchars($covoiturageInfo->nb_place) ?></br></br>
            <form action="" method="post">

                <strong>Nombre de places demandées :</strong></br>
                <input type="number" name="nb_place" class="form-control" placeholder="exemple: 1" value="" min="1"
                    max="<?= htmlspecialchars($covoiturageInfo->nb_place) ?>" required>
                </p>
                <input type="hidden" name="covoiturage_id"
                    value="<?= htmlspecialchars($covoiturageInfo->covoiturage_id) ?>">
                <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true): ?>
                    <button type="submit" name="confirmer_participation">Confirmer</button>
                <?php endif; ?>
            </form>
        <?php else: ?>
            <p>Covoiturage non trouvé.</p>
        <?php endif; ?>
    </div>


    <div style="flex-grow: 1; padding: 20px;">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success" role="alert">
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <h2>Détails du covoiturage</h2>
        <?php if ($covoiturageInfo): ?>
            <div class="publication-cadre">

                <div class="publication-header">
                    <div class="utilisateur-info">

                        <span>
                            <img src="<?= $covoiturageInfo->photo ?>"
                                alt="Photo de <?= htmlspecialchars($covoiturageInfo->nom) ?>" class="photo-utilisateur"
                                height="50" width="50">
                        </span>
                        <span class="utilisateur" name="nom">
                            <?= htmlspecialchars($covoiturageInfo->nom) ?></br><?= htmlspecialchars($covoiturageInfo->prenom) ?>
                        </span>
                    </div>
                </div>


                <div class="publication-header">
                    <span class="date-creation" name="created_at">
                        **Publié le : <?= htmlspecialchars($covoiturageInfo->created_at) ?>**
                    </span>
                    <div class="utilisateur-info">


                    </div>

                </div>

                <div class="publication-details">
                    <div class="trajet">
                        <h3>Trajet</h3>
                        <p>
                            <strong>Départ :</strong> <?= htmlspecialchars($covoiturageInfo->lieu_depart) ?>
                            <br>
                            <strong>Arrivée :</strong> <?= htmlspecialchars($covoiturageInfo->lieu_arrivee) ?>
                        </p>
                    </div>

                    <div class="dates">
                        <h3>Dates et Horaires</h3>
                        <p>
                            <strong>Départ :</strong>
                            <?= htmlspecialchars(date('d/m/Y', strtotime($covoiturageInfo->date_depart))) ?> à
                            <?= htmlspecialchars(date('H:i', strtotime($covoiturageInfo->heure_depart))) ?> h
                            <br>
                            <strong>Arrivée :</strong>
                            <?= htmlspecialchars(date('d/m/Y', strtotime($covoiturageInfo->date_arrivee))) ?> à
                            <?= htmlspecialchars(date('H:i', strtotime($covoiturageInfo->heure_arrivee))) ?> h
                        </p>
                    </div>

                    <div class="informations">
                        <h3>Informations</h3>
                        <p>
                            <strong>Type de voiture :</strong>
                            <?= htmlspecialchars($covoiturageInfo->energie) ?>
                            <br>
                            <strong>Places disponibles :</strong>
                            <?= htmlspecialchars($covoiturageInfo->nb_place) ?>
                            <br>
                            <strong>Prix par place :</strong>
                            <?= htmlspecialchars($covoiturageInfo->prix_personne) ?>
                            Credits
                        </p>
                    </div>
                </div>

            <?php else: ?>
                <p>Covoiturage non trouvé.</p>
            <?php endif; ?>
            </div>
    </div>
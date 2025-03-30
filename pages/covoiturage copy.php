<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 'On');
ini_set('error_log', '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/php-error.log');



require_once '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/header.php';





$pdo = new PDO("sqlite:/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/ecorideDatabase.db");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == true && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];








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
}

$research = []; // Initialize an empty array for search results
$success = null;
$error = null;

if (isset($_POST['search'])) {
    $depart = $_POST['depart'];
    $arrivee = $_POST['arrivee'];
    $date = $_POST['date'];



    try {
        $researchStmt = $pdo->prepare("SELECT COUNT(*) FROM covoiturages WHERE lieu_depart = :lieu_depart OR lieu_arrivee = :lieu_arrivee OR date_depart =
:date_depart");
        $researchStmt->bindParam(':lieu_depart', $depart, PDO::PARAM_STR);
        $researchStmt->bindParam(':lieu_arrivee', $arrivee, PDO::PARAM_STR);
        $researchStmt->bindParam(':date_depart', $date, PDO::PARAM_STR);
        $researchStmt->execute();
        $result = $researchStmt->fetchAll(PDO::FETCH_ASSOC);
        $result = $result[0];
        $count = $result['COUNT(*)'];
        $countSuccess = 'Nombre de covoiturages trouvés : ' . $count;


        if ($count > 0) {
            $researchStmt = $pdo->prepare("SELECT user_id FROM covoiturages WHERE lieu_depart = :lieu_depart OR lieu_arrivee = :lieu_arrivee OR date_depart =
:date_depart");
            $researchStmt->bindParam(':lieu_depart', $depart, PDO::PARAM_STR);
            $researchStmt->bindParam(':lieu_arrivee', $arrivee, PDO::PARAM_STR);
            $researchStmt->bindParam(':date_depart', $date, PDO::PARAM_STR);
            $researchStmt->execute();
            $researchId = $researchStmt->fetchAll(PDO::FETCH_OBJ);


            $success = 'Recherche effectuée avec succès. ';
            $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $researchId, PDO::PARAM_INT);
            $stmt->execute();
            $userInfo = $stmt->fetchAll(PDO::FETCH_OBJ);
            var_dump($userInfo);

            $stmt = $pdo->prepare("SELECT * FROM covoiturages WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $researchId, PDO::PARAM_INT);
            $stmt->execute();
            $research = $stmt->fetchAll(PDO::FETCH_OBJ);
        } else {
            $error = 'Aucun covoiturage trouvé';
        }
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}

?>


<div>
    <p>Filtrer</p>
    </br>
    <div class="conteneur">
        <div class="colonne1">Type de voiture</br>
            <input class="form-check-input" type="checkbox" value="" id="moteurHybride" checked>
            <label class="form-check-label" for="moteurHybride">
                Electrique
            </label></br>
            <input class="form-check-input" type="checkbox" value="" id="moteurHybride" checked>
            <label class="form-check-label" for="moteurHybride">
                Hybride
            </label></br>
            <input class="form-check-input" type="checkbox" value="" id="moteurHybride" checked>
            <label class="form-check-label" for="moteurHybride">
                Essence
            </label></br>

        </div>
        <div class="colonne2">Echelle de prix : </br>
            <div class="recherche-multiple text-black">
                <label for="prixMini"></label>
                <input type="text" id="prixmini" name="prixmini" placeholder="minimum :">

                <label for="prixMaxi"></label>
                <input type="text" id="prixmaxi" name="prixmaxi" placeholder="maximum :"></br>
            </div>
        </div>
        <div class="colonne3">Durée du voyage :</br>
            <div id="dureevoyage">
                <div class="recherche-multiple text-black">
                    <label for="dureeMini"></label>
                    <input type="text" id="duree" name="duree" placeholder="1h">

                </div>
            </div>
        </div>
        <div class="colonne4">Evaluations :</br>
            <div class="rating">
                <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked" checked><span
                    class="star">⭐</span>
                <span class="star">⭐</span>
                <span class="star">⭐</span>
                <span class="star">⭐</span>
                <span class="star">⭐</span>
            </div>
            <div class="rating">
                <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                <span class="star">⭐</span>
                <span class="star">⭐</span>
                <span class="star">⭐</span>
                <span class="star">⭐</span>
                <span class="star">☆</span>
            </div>
            <div class="rating">
                <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                <span class="star">⭐</span>
                <span class="star">⭐</span>
                <span class="star">⭐</span>
                <span class="star">☆</span>
                <span class="star">☆</span>
            </div>
            <div class="rating">
                <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                <span class="star">⭐</span>
                <span class="star">⭐</span>
                <span class="star">☆</span>
                <span class="star">☆</span>
                <span class="star">☆</span>
            </div>
            <div class="rating">
                <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                <span class="star">⭐</span>
                <span class="star">☆</span>
                <span class="star">☆</span>
                <span class="star">☆</span>
                <span class="star">☆</span>
            </div>
        </div>

    </div>




    <div class="ligne-horizontale"></div></br>
    <?php if (isset($error)) : ?>
    <div class="alert alert-danger" role="alert">
        <?= $error ?>
    </div>
    <?php endif; ?>
    <?php if (isset($success)) : ?>
    <div class="alert alert-success container" role="alert">
        <?= $success ?></br>
        <?= $countSuccess ?>
    </div>
    <?php endif; ?></br>
    <?php foreach ($research as $result) foreach ($userInfo as $user) : ?>

    <div class="publication-cadre">

        <div class="publication-header">
            <div class="utilisateur-info">
                <img src="https://www.contraelcancer.es/es/user/1976"
                    class="photo-utilisateur"><?= $user->nom ?></br><?= $user->prenom ?>
                <span class="utilisateur"></span>
            </div>
            <span class="date-creation">**Publié le :** [Date de création]</span>
        </div>

        <div class="publication-details">
            <div class="trajet">
                <h3>Trajet</h3>
                <p>
                    <strong>Départ :<?= $result->lieu_depart ?> </strong> <span class="lieu-depart"></span>
                    <br>
                    <strong>Arrivée : <?= $result->lieu_arrivee ?> </strong> <span class="lieu-arrivee"></span>
                </p>
            </div>

            <div class="dates">
                <h3>Dates et Horaires</h3>
                <p>
                    <strong>Départ : <?= $result->date_depart ?> à <?= $result->heure_depart ?> h</strong> <span
                        class="date-depart"></span>
                    <br>
                    <strong>Arrivée : <?= $result->date_arrivee ?> à <?= $result->heure_arrivee ?> h</strong>
                    <span class="date-arrivee"></span>
                </p>
            </div>

            <div class="informations">
                <h3>Informations</h3>
                <p>
                    <strong>Places disponibles : <?= $result->nb_place ?></strong> <span
                        class="places-disponibles"></span>
                    <br>
                    <strong>Prix par place :<?= $result->prix_personne ?></strong> <span class="prix">Credits</span>
                </p>

            </div>
            <div class="publication-actions">
                <button class="participer-btn">Participer</button>
            </div>
        </div></br>

    </div>


</div></br>
<?php endforeach; ?>
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
        $resultNumber = $researchStmt->fetchAll(PDO::FETCH_ASSOC);
        $resultNumber = $resultNumber[0];
        $count = $resultNumber['COUNT(*)'];
        $countSuccess = 'Nombre de covoiturages trouvés : ' . $count;


        if ($count > 0) {
            $researchStmt = $pdo->prepare("SELECT *, u.nom AS nom, u.prenom AS prenom
                                FROM covoiturages c
                                LEFT JOIN utilisateurs u ON c.user_id = u.user_id
                                WHERE c.lieu_depart = :lieu_depart
                                   OR c.lieu_arrivee = :lieu_arrivee
                                   OR c.date_depart LIKE :date_depart");

            $researchStmt->bindParam(':lieu_depart', $depart, PDO::PARAM_STR);
            $researchStmt->bindParam(':lieu_arrivee', $arrivee, PDO::PARAM_STR);
            $researchStmt->bindValue(':date_depart', $date . '%', PDO::PARAM_STR);

            $researchStmt->execute();
            $researcheResult = $researchStmt->fetchAll(PDO::FETCH_OBJ);
            $success = 'Recherche effectuée avec succès. ';
        } else {
            $error = 'Aucun covoiturage trouvé';
        }
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}

?>







<div style="display: flex;">
    <div class="sidebar">
        <p>Filtrer</p>

        </br>


        <div class="filter-container">
            <div class="filter-group">
                <label>Type de voiture</label></br>
                <input class="form-check-input" type="checkbox" value="electrique" id="moteurElectrique">
                <label class="form-check-label" for="moteurElectrique">
                    Electrique
                </label></br>
                <input class="form-check-input" type="checkbox" value="hybride" id="moteurHybride">
                <label class="form-check-label" for="moteurHybride">
                    Hybride
                </label></br>
                <input class="form-check-input" type="checkbox" value="essence" id="moteurEssence">
                <label class="form-check-label" for="moteurEssence">
                    Essence
                </label></br>
            </div>

            <div class="filter-group">
                <label>Echelle de prix :</label> </br>
                <div class="price-range">
                    <label for="prixMini">Minimum :</label>
                    <input type="number" id="prixmini" name="prixmini" placeholder="minimum">
                    <br>
                    <label for="prixMaxi">Maximum :</label>
                    <input type="number" id="prixmaxi" name="prixmaxi" placeholder="maximum">
                </div>
            </div>

            <div class="filter-group">
                <label>Durée du voyage :</label></br>
                <div class="duration-filter">
                    <label for="dureeMax">Max (ex: 1h30):</label>
                    <input type="text" id="dureeMax" name="dureeMax" placeholder="max">
                </div>
            </div>

            <div class="filter-group">
                <label>Evaluations :</label></br>
                <?php for ($i = 5; $i >= 1; $i--) : ?>
                <div class="rating-filter">
                    <input class="form-check-input" type="checkbox" value="<?= $i ?>" id="rating<?= $i ?>">
                    <?php for ($j = 0; $j < $i; $j++) : ?>
                    <span class="star">⭐</span>
                    <?php endfor; ?>
                    <?php for ($j = $i; $j < 5; $j++) : ?>
                    <span class="star">☆</span>
                    <?php endfor; ?>
                </div>
                <?php endfor; ?>
            </div>
            <button id="applyFiltersBtn" class="apply-filters-button">Appliquer les filtres</button></br>

        </div>
    </div>

    <div style="flex-grow: 1;">
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

        <?php foreach ($researcheResult as $result): ?>
        <div class="publication-cadre">
            <div class="publication-header">
                <div class="utilisateur-info">
                    <img src="https://via.placeholder.com/40" alt="Photo de <?= htmlspecialchars($result->nom) ?>"
                        class="photo-utilisateur">
                    <span
                        class="utilisateur"><?= htmlspecialchars($result->nom) ?></br><?= htmlspecialchars($result->prenom) ?></span>
                </div>
                <span class="date-creation">**Publié le :**
                </span>
            </div>

            <div class="publication-details">
                <div class="trajet">
                    <h3>Trajet</h3>
                    <p>
                        <strong>Départ :</strong> <?= htmlspecialchars($result->lieu_depart) ?> <span
                            class="lieu-depart"></span>
                        <br>
                        <strong>Arrivée :</strong> <?= htmlspecialchars($result->lieu_arrivee) ?> <span
                            class="lieu-arrivee"></span>
                    </p>
                </div>

                <div class="dates">
                    <h3>Dates et Horaires</h3>
                    <p>
                        <strong>Départ :</strong>
                        <?= htmlspecialchars(date('d/m/Y', strtotime($result->date_depart))) ?> à
                        <?= htmlspecialchars(date('H:i', strtotime($result->heure_depart))) ?> h<span
                            class="date-depart"></span>
                        <br>
                        <strong>Arrivée :</strong>
                        <?= htmlspecialchars(date('d/m/Y', strtotime($result->date_arrivee))) ?> à
                        <?= htmlspecialchars(date('H:i', strtotime($result->heure_arrivee))) ?> h <span
                            class="date-arrivee"></span>
                    </p>
                </div>

                <div class="informations">
                    <h3>Informations</h3>
                    <p>
                        <strong>Places disponibles :</strong> <?= htmlspecialchars($result->nb_place) ?> <span
                            class="places-disponibles"></span>
                        <br>
                        <strong>Prix par place :</strong><?= htmlspecialchars($result->prix_personne) ?> <span
                            class="prix">Credits</span>
                    </p>
                </div>
                <div class="publication-actions">
                    <button class="participer-btn">Participer</button>
                </div>
            </div></br>
        </div>
        <?php endforeach; ?>
    </div>
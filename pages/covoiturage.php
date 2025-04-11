<?php
session_start();
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
    header("Location: http://localhost:4000/pages/home.php"); // Redirige vers la page home
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

require_once '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/header.php';
?>






<body>
    <div style="display: flex;">
        <div class="sidebar">
            <p>Filtrer</p>

            </br>


            <div class="filter-container">
                <form action="" method="post">
                    <div class="filter-group">
                        <label>Type de voiture</label></br>
                        <input class="form-check-input" type="checkbox" name="Electrique" value="Electrique"
                            id="moteurElectrique">
                        <label class="form-check-label" for="moteurElectrique">
                            Electrique
                        </label></br>
                        <input class="form-check-input" type="checkbox" name="Hybride" value="Hybride"
                            id="moteurHybride">
                        <label class="form-check-label" for="moteurHybride">
                            Hybride
                        </label></br>
                        <input class="form-check-input" type="checkbox" name="Essence" value="Essence"
                            id="moteurEssence">
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
                                <input class="form-check-input" name="evaluation" type="checkbox" value="<?= $i ?>"
                                    id="rating<?= $i ?>">
                                <?php for ($j = 0; $j < $i; $j++) : ?>
                                    <span class="star">⭐</span>
                                <?php endfor; ?>
                                <?php for ($j = $i; $j < 5; $j++) : ?>
                                    <span class="star">☆</span>
                                <?php endfor; ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                    <button id="applyFiltersBtn" type="submit" name="applyFilters"
                        class="apply-filters-button">Appliquer
                        les
                        filtres</button></br>
                </form>
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
                <form method="post">
                    <div class="publication-cadre">
                        <div class="publication-header">
                            <div class="utilisateur-info">
                                <span>
                                    <img src="<?= $result->photo ?>" alt="Photo de <?= htmlspecialchars($result->nom) ?>"
                                        class="photo-utilisateur" height="50" width="50">
                                </span>
                                <span class="utilisateur" name="nom">
                                    <?= htmlspecialchars($result->nom) ?></br><?= htmlspecialchars($result->prenom) ?>
                                </span>
                            </div>
                            <span class="date-creation" name="created_at">
                                **Publié le : <?= htmlspecialchars($result->created_at) ?>**
                            </span>
                        </div>

                        <div class="publication-details">
                            <div class="trajet">
                                <h3>Trajet</h3>
                                <p>
                                    <strong>Départ :</strong> <?= htmlspecialchars($result->lieu_depart) ?>
                                    <br>
                                    <strong>Arrivée :</strong> <?= htmlspecialchars($result->lieu_arrivee) ?></br>
                                    <strong>Durée du trajet :</strong> <span
                                        class="duree"><?= htmlspecialchars($result->duree) ?></span>
                                </p>
                            </div>

                            <div class="dates">
                                <h3>Dates et Horaires</h3>
                                <p>
                                    <strong>Départ :</strong>
                                    <?= htmlspecialchars(date('d/m/Y', strtotime($result->date_depart))) ?> à
                                    <span class="h_depart">
                                        <?= htmlspecialchars(date('H:i', strtotime($result->heure_depart))) ?></span> h
                                    <br>
                                    <strong>Arrivée :</strong>
                                    <?= htmlspecialchars(date('d/m/Y', strtotime($result->date_arrivee))) ?> à
                                    <span
                                        class="h_arrivee"><?= htmlspecialchars(date('H:i', strtotime($result->heure_arrivee))) ?></span>
                                    h
                                </p>
                            </div>

                            <div class="informations">
                                <h3>Informations</h3>
                                <p>
                                    <strong>Type de voiture :</strong>
                                    <span class="energie"> <?= htmlspecialchars($result->energie) ?></span>
                                    </span>
                                    <br>
                                    <strong>Places disponibles :</strong> <?= htmlspecialchars($result->nb_place) ?>
                                    <br>

                                    <strong>Prix par place :</strong>
                                    <span class="prix"> <?= htmlspecialchars($result->prix_personne) ?>
                                    </span><span>¢</span>


                                    <br>
                                    <strong> Note : </strong><span
                                        class="note"><?= htmlspecialchars($result->average_note) ?> </span>⭐</strong>

                                    <br>
                                </p>
                            </div>

                            <div class="publication-actions">
                                <input type="hidden" name="covoiturage_id"
                                    value="<?= htmlspecialchars($result->covoiturage_id) ?>">
                                <button class="participer-btn" type="submit" name="participer">Participer</button>
                            </div>
                        </div>
                    </div>
                </form>
            <?php endforeach; ?>
        </div>
        <script src="/JS/filter_script.js"></script>
</body>
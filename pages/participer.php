<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 'On');
ini_set('error_log', '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/php-error.log');



$pdo = new PDO("sqlite:/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/ecorideDatabase.db");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_GET['covoiturage_id'])) {
    $covoiturage_id = $_GET['covoiturage_id'];

    try {
        $stmt = $pdo->prepare("SELECT *, u.nom AS nom, u.prenom AS prenom,
    v.energie AS energie FROM covoiturages c LEFT JOIN utilisateurs u ON c.user_id = u.user_id LEFT JOIN voitures v ON c.voiture_id = v.voiture_id
WHERE c.covoiturage_id = :covoiturage_id");
        $stmt->bindParam(':covoiturage_id', $covoiturage_id, PDO::PARAM_INT);
        $stmt->execute();
        $covoiturage = $stmt->fetchAll(PDO::FETCH_OBJ);
        var_dump($covoiturage);
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération des informations du covoiturage : " . $e->getMessage();
    }
}



require_once '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/header.php';

require_once '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/footer.php';
?>

<h1> Information sur le trajet</h1>
<div class="ligne-horizontale"></div>
<p class="description">Voici les informations sur le trajet que vous avez choisi. Si vous souhaitez participer à ce
    trajet, cliquez sur le bouton "Participer".</p>
<div class="ligne-horizontale"></div>

<?php foreach ($covoiturage as $covoit): ?>
    <form method="post">
        <div class="publication-cadre">
            <div class="publication-header">
                <div class="utilisateur-info">
                    <span>
                        <img src="<?= $covoit->photo ?>" alt="Photo de <?= htmlspecialchars($covoit->nom) ?>"
                            class="photo-utilisateur" height="50" width="50">
                    </span>
                    <span class="utilisateur" name="nom">
                        <?= htmlspecialchars($covoit->nom) ?></br><?= htmlspecialchars($covoit->prenom) ?>
                    </span>
                </div>
                <span class="date-creation" name="created_at">
                    **Publié le : <?= htmlspecialchars($covoit->created_at) ?>**
                </span>
            </div>

            <div class="publication-details">
                <div class="trajet">
                    <h3>Trajet</h3>
                    <p>
                        <strong>Départ :</strong> <?= htmlspecialchars($covoit->lieu_depart) ?>
                        <br>
                        <strong>Arrivée :</strong> <?= htmlspecialchars($covoit->lieu_arrivee) ?>
                    </p>
                </div>

                <div class="dates">
                    <h3>Dates et Horaires</h3>
                    <p>
                        <strong>Départ :</strong>
                        <?= htmlspecialchars(date('d/m/Y', strtotime($covoit->date_depart))) ?> à
                        <?= htmlspecialchars(date('H:i', strtotime($covoit->heure_depart))) ?> h
                        <br>
                        <strong>Arrivée :</strong>
                        <?= htmlspecialchars(date('d/m/Y', strtotime($covoit->date_arrivee))) ?> à
                        <?= htmlspecialchars(date('H:i', strtotime($covoit->heure_arrivee))) ?> h
                    </p>
                </div>

                <div class="informations">
                    <h3>Informations</h3>
                    <p>
                        <strong>Type de voiture :</strong> <?= htmlspecialchars($covoit->energie) ?>
                        <br>
                        <strong>Places disponibles :</strong> <?= htmlspecialchars($covoit->nb_place) ?>
                        <br>
                        <strong>Prix par place :</strong> <?= htmlspecialchars($covoit->prix_personne) ?> Credits
                    </p>
                </div>

                <div class="publication-actions">
                    <input type="hidden" name="covoiturage_id" value="<?= htmlspecialchars($covoit->covoiturage_id) ?>">
                    <button class="participer-btn" type="submit" name="participer">Participer</button>
                </div>
            </div>
        </div>
    </form>
<?php endforeach; ?>


</div>
<h1> Information sur le chauffeur</h1>
<div class="ligne-horizontale"></div>
<p class="description">
    Voici les informations sur le chauffeur du trajet que vous avez choisi. Si vous souhaitez participer à ce trajet,
    cliquez sur le bouton "Participer".
</p></br>
<?php foreach ($covoiturage as $covoit): ?>
    <div class="form-control">
        <div class="publication-header">
            <div class="utilisateur-info">
                <span>
                    <img src="<?= $covoit->photo ?>" alt="Photo de <?= htmlspecialchars($covoit->nom) ?>"
                        class="photo-utilisateur" height="50" width="50">
                </span>
                <span class="utilisateur" name="prenom">
                    <?= htmlspecialchars($covoit->prenom) ?>
                </span>
                <span class="utilisateur" name="nom">
                    <?= htmlspecialchars($covoit->nom) ?></br></br></br></br>

                    <span class="utilisateur" name="date_naissance">Date de naissance :
                        <?= htmlspecialchars($covoit->date_naissance) ?></br>
                    </span>
                    <span class="utilisateur" name="voiture">Modele de la voiture :
                        <?= htmlspecialchars($covoit->modele) ?></br>
                    </span>
                    <span class="utilisateur" name="couleur">Couleur de la voiture :
                        <?= htmlspecialchars($covoit->couleur) ?></br>
                    </span>
                    <span class="utilisateur" name="nom">Immatriculation du vehicule :
                        <?= htmlspecialchars($covoit->immatriculation) ?></br>
                    </span>
                    <span class="utilisateur" name="nom">Note du chauffeur :
                        <?= htmlspecialchars($covoit->note) ?></br>
                    </span>
                    <span class="utilisateur" name="nom">Nombre d'avis :
                        <?= htmlspecialchars($covoit->nb_avis) ?></br>
                    </span>
            </div>

        </div>

    <?php endforeach; ?>



    <div class="ligne-horizontale"></div>
    <h1> Preferences du chauffeur</h1>
    <p class="description">
        Voici les preferences du chauffeur du trajet que vous avez choisi. Si vous souhaitez participer à ce trajet,
        cliquez sur le bouton "Participer".
    </p></br>
    <?php foreach ($covoiturage as $covoit): ?>
        <div class="utilisateur">Preferences : <?= htmlspecialchars($covoit->commentaire) ?></br>

        </div>
    <?php endforeach; ?>
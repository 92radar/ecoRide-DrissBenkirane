<?php
require_once "/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/header.php";
require_once "/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/backend/confirmationBe.php";

require_once "/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/mobile-nav.php";
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
<?php


require_once "/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/second_header.php";
require_once "/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/backend/participerBe.php";

require_once "/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/mobile-nav.php";
?>

<div class="container">
    <h1>Informations sur le chauffeur</h1>
    <div class="ligne-horizontale"></div>
    <p class="description">
        Voici les informations sur le chauffeur du trajet que vous avez choisi. Si vous souhaitez participer à ce
        trajet,
        cliquez sur le bouton "Participer".
    </p><br>

    <?php if (!empty($covoiturage)) : ?>
        <div class="form-control">
            <div class="publication-header">
                <div class="utilisateur-info">
                    <span>
                        <img src="<?= htmlspecialchars($covoiturage->photo) ?>"
                            alt="Photo de <?= htmlspecialchars($covoiturage->nom) ?>" class="photo-utilisateur" height="50"
                            width="50">
                    </span>
                    <span class="utilisateur" name="prenom">
                        <?= htmlspecialchars($covoiturage->prenom) ?>
                    </span>
                    <span class="utilisateur" name="nom">
                        <?= htmlspecialchars($covoiturage->nom) ?></span><br><br><br><br>

                    <span class="utilisateur" name="date_naissance">Date de naissance :
                        <?= htmlspecialchars($covoiturage->date_naissance) ?><br>
                    </span> <span class="utilisateur" name="marque">Marque de la voiture :
                        <?= htmlspecialchars($covoiturage->marque) ?><br>
                    </span>
                    <span class="utilisateur" name="voiture">Modèle de la voiture :
                        <?= htmlspecialchars($covoiturage->modele) ?><br>
                    </span>
                    <span class="utilisateur" name="couleur">Couleur de la voiture :
                        <?= htmlspecialchars($covoiturage->couleur) ?><br>
                    </span>
                    <span class="utilisateur" name="nom">Immatriculation du véhicule :
                        <?= htmlspecialchars($covoiturage->immatriculation) ?><br>

                    </span>
                    <span class="utilisateur" name="note">Note moyenne :
                        <?= htmlspecialchars($covoiturage->average_note) ?> ⭐</span><br>

                </div>
            </div>
        </div>
        <div class="ligne-horizontale"></div></br>
        <h1>Préférences du chauffeur</h1>
        <p2 class="description">
            Voici les préférences du chauffeur du trajet que vous avez choisi.
        </p2><br>
        <div class="utilisateur">
            <p>Préférences : <?= htmlspecialchars($covoiturage->commentaire) ?><br>
            </p>
        </div></br>
    <?php else: ?>
        <p>Covoiturage non trouvé.</p>
    <?php endif; ?>

    <div class="ligne-horizontale"></div>
    <div class="utilisateur-info">
        <h3>Avis des Participants</h3>
    </div></br>
    <?php if (!empty($avis)) : ?>
        <?php foreach ($avis as $unAvis) : ?>
            <div class="publication-cadre">
                <div class="publication-header">

                </div>
                <div class="publication-details">
                    <div class="avis-section">

                        <div class="avis-cadre">
                            <div class="avis-header">
                                <span>
                                    <img src="<?= htmlspecialchars($unAvis->photo) ?>"
                                        alt="Photo de <?= htmlspecialchars($unAvis->nom) ?>" class="photo-utilisateur"
                                        height="50" width="50">
                                </span>
                                <strong class="container">Avis donné par : </strong> <?= htmlspecialchars($unAvis->prenom) ?>
                                <?= htmlspecialchars($unAvis->nom) ?></br></br>

                            </div>

                            <div class="avis-contenu">
                                <p>"<?= htmlspecialchars($unAvis->commentaire) ?>"</p></br>
                                <span class="container">Note : <?= htmlspecialchars($unAvis->note) ?>/5</span>

                            </div>
                        </div>




                    </div>
                </div>

            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p>Aucun avis pour ce chauffeur pour le moment.</p>
    <?php endif; ?>

    <div class="ligne-horizontale"></div></br>
    <h1>Information sur le trajet</h1></br>
    <p class="description">Voici les informations sur le trajet que vous avez choisi. Si vous souhaitez participer à ce
        trajet, cliquez sur le bouton "Participer".</p></br>

    <form method="post">
        <?php if (!empty($covoiturage)) : ?>
            <div class="container-bottom2">
                <div class="publication-cadre">
                    <div class="publication-header">
                        <div class="utilisateur-info">
                            <span>

                                <img src="<?= htmlspecialchars($covoiturage->photo) ?>"
                                    alt="Photo de <?= htmlspecialchars($covoiturage->nom) ?>" class="photo-utilisateur"
                                    height="50" width="50">
                            </span>
                            <span class="utilisateur" name="nom">
                                <?= htmlspecialchars($covoiturage->nom) ?></br><?= htmlspecialchars($covoiturage->prenom) ?>
                            </span>
                        </div>
                        <span class="date-creation" name="created_at">
                            **Publié le : <?= htmlspecialchars($covoiturage->created_at) ?>**
                        </span>
                    </div>

                    <div class="publication-details">
                        <div class="trajet">
                            <h3>Trajet</h3>
                            <p>
                                <strong>Départ :</strong> <?= htmlspecialchars($covoiturage->lieu_depart) ?>
                                <br>
                                <strong>Arrivée :</strong> <?= htmlspecialchars($covoiturage->lieu_arrivee) ?>
                            </p>
                        </div>

                        <div class="dates">
                            <h3>Dates et Horaires</h3>
                            <p>
                                <strong>Départ :</strong>
                                <?= htmlspecialchars(date('d/m/Y', strtotime($covoiturage->date_depart))) ?> à
                                <?= htmlspecialchars(date('H:i', strtotime($covoiturage->heure_depart))) ?> h
                                <br>
                                <strong>Arrivée :</strong>
                                <?= htmlspecialchars(date('d/m/Y', strtotime($covoiturage->date_arrivee))) ?> à
                                <?= htmlspecialchars(date('H:i', strtotime($covoiturage->heure_arrivee))) ?> h
                            </p>
                        </div>

                        <div class="informations">
                            <h3>Informations</h3>
                            <p>
                                <strong>Type de voiture :</strong> <?= htmlspecialchars($covoiturage->energie) ?>
                                <br>
                                <strong>Places disponibles :</strong> <?= htmlspecialchars($covoiturage->nb_place) ?>
                                <br>
                                <strong>Prix par place :</strong> <?= htmlspecialchars($covoiturage->prix_personne) ?>
                                Credits
                            </p>
                        </div>

                        <div class="publication-actions">
                            <input type="hidden" name="covoiturage_id"
                                value="<?= htmlspecialchars($covoiturage->covoiturage_id) ?>">
                            <button class="participer-btn" type="submit" name="participer">Participer</button>

                        </div>

                    </div>

                </div>

            </div>
        <?php else: ?>
            <p>Covoiturage non trouvé.</p>
        <?php endif; ?>
    </form>

</div>



</div>




<?php require_once '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/footer.php'; ?>
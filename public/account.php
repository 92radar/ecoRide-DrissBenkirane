<?php
session_start();

require_once "/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/backend/accountBe.php";
require_once "/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/second_header.php";
require_once "/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/mobile-nav.php";
?>
<link rel="stylesheet" href="styles/account.css">
<style>
    @media screen and (max-width: 968px) {
        .sidebar {
            display: none;
        }





    }
</style>


<div style="display: flex;">
    <div class="sidebar">
        <p>Navigation</p> </br>
        <div class="menu">
            <?php if (!empty($userInfos)): ?>
                <?php foreach ($userInfos as $userInfo): ?>
                    <img src="<?= $userInfo->photo ?>" alt="Photo de profil" class="photo-utilisateur" width="100"
                        height="100"></br></br></br>
                <?php endforeach; ?>
            <?php endif; ?>

            <ul>


                <li><a href="#section1">Informations personnelles</a></li>
                <li><a href="#section2">Publier un trajet</a></li>
                <li><a href="#section3">Devenir chauffeur</a></li>
                <li><a href="#section4">Historique des trajets</a></li>
                <li><a href="#section5">Co-voiturage en cours</a></li>
            </ul>
        </div>

    </div>

    <div style="flex-grow: 1;" class="container"></br>
        <form method="post">
            <button type="submit" name="logout" class="logout-btn">Se déconnecter</button>
        </form>
        <div class="ligne-horizontale"></div></br>
        <?php if (isset($success)) : ?>
            <div class="alert alert-success container" role="alert">
                <?= $success ?></br>
            </div>
        <?php endif; ?>
        <?php if (isset($error_chauffeur)) : ?>
            <div class="alert alert-danger container" role="alert">
                <?= $error_chauffeur ?></br>
            </div>
        <?php endif; ?>


        <?php if (isset($error)) : ?>
            <div class="alert alert-danger container" role="alert">
                <?= $error ?></br>
            </div>
        <?php endif; ?>
        <h1>Profil</h1>
        <div class="profil">
            <?php if (!empty($userInfos)): ?>
                <?php foreach ($userInfos as $userInfo): ?>
                    <h3>Informations personnelles</h3>
                    <div class="profil-info" id="">

                        <form action="" method="post">
                            <div class="form-group">
                                <div class="profil-details">


                                </div>
                                <strong>Nom :</strong></br><input class="form-control" type="text" name="nom"
                                    value="<?= htmlspecialchars($userInfo->nom) ?>" required></br>
                                <strong>Prénom :</strong></br><input class="form-control" type="text" name="prenom"
                                    value="<?= htmlspecialchars($userInfo->prenom) ?>" required></br>
                                <strong>Pseudo :</strong></br><input class="form-control" type="text" name="pseudo"
                                    value="<?= htmlspecialchars($userInfo->pseudo) ?>" required></br>
                                <strong>Date de naissance :</strong></br><input class="form-control" type="date"
                                    name="date_naissance" value="<?= htmlspecialchars($userInfo->date_naissance) ?>"
                                    required></br>

                                <strong>Email :</strong></br><input class="form-control" type="email" name="email"
                                    value="<?= htmlspecialchars($userInfo->email) ?>" required></br>
                                <strong>Adresse :</strong></br><input class="form-control" type="text" name="adresse"
                                    value="<?= htmlspecialchars($userInfo->adresse) ?>" required></br>
                                <strong>
                                    Ville :</strong></br><input class="form-control" type="text" name="ville"
                                    value="<?= htmlspecialchars($userInfo->ville) ?>" required></br>
                                <strong>Numéro de téléphone :</strong></br><input class="form-control" type="text"
                                    name="telephone" value="<?= htmlspecialchars($userInfo->telephone) ?>" required></br>
                                <strong class="form-control">Nombre de credit restant :
                                    <?= htmlspecialchars($userInfo->credits) ?></strong></br>
                                <strong class="form-control">Note moyenne :


                                    <?= htmlspecialchars($userInfo->average_note) ?>⭐</strong></br>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Aucune information utilisateur trouvée.</p>
                        <?php endif; ?>
                        </br>

                        <div class=" profil-actions">
                            <button type="submit" name="modifier" class="profil-btn">Modifier</button></br>

                        </div></br>
                        </form>
                        <form action="" method="post" enctype="multipart/form-data">
                            <label for="photo_profil">Modifier la photo de profil:</label></br>
                            <input type="file" id="photo_profil" name="photo_profil" accept="image/*"></br>
                            <small>Formats acceptés: JPG, JPEG, PNG, GIF (max 5MB).</small></br>
                            <button type="submit" name="upload" class="upload-btn">Upload</button></br>
                        </form>


                    </div>
                    <div class="ligne-horizontale"></div></br>


                    <h1>Ajouter un vehicule</h1> </br>
                    <div class="devenir-chauffeur" id="section3">
                        <div class="devenir-chauffeur-details">
                            <form action="" method="post">
                                <legend>Informations</legend>
                                <div class="form-group">
                                    <label for="marque_vehicule">Marque du véhicule :</label>
                                    <input type="text" class="form-control" name="marque" id="marque"
                                        placeholder="Marque de la voiture" required>
                                    <label for="vehicule">Modele du véhicule :</label>
                                    <input type="text" class="form-control" name="modele" id="modele"
                                        placeholder="modele de la voiture" required>
                                </div>

                                <div class="form-group">
                                    <label for="couleur">Couleur :</label>
                                    <input type="text" class="form-control" name="couleur" id="couleur_voiture"
                                        placeholder="Couleur du vehicule, ex: bleu" required>
                                </div>

                                <div class="form-group">
                                    <label for="immatriculation">Immatriculation :</label>
                                    <input type="text" class="form-control" name="immatriculation" id="immatriculation_vehicule"
                                        placeholder="Immatriculation" required>
                                </div>

                                <div class="form-group">
                                    <label for="date_immatriculation">Date d'immatriculation :</label>
                                    <input type="date" class="form-control" name="date_premiere_immatriculation"
                                        id="date_premiere_immatriculation" placeholder="Date de la premiere immatriculation"
                                        required>
                                </div>
                                <div class="form-group">

                                    <label for="energie">Type de voiture :</label></br>
                                    <select class="form-control" name="energie" id="energie">
                                        <option value="Essence">Essence</option>
                                        <option value="Hybride">Hybride</option>
                                        <option value="Electrique">Electrique</option>
                                    </select></br>
                                </div>



                                <div class="devenir-chauffeur-actions">
                                    <button class="devenir-chauffeur-btn" type="submit" name="ajouter_vehicule">Ajouter un
                                        vehicule</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="ligne-horizontale"></div></br>
                    <h1>Publier un trajet</h1> </br>
                    <div class="publier-trajet" id="section2">
                        <div class="publier-trajet-details">
                            <form action="" method="post">
                                <legend>Informations du trajet</legend>

                                <div class="form-group">
                                    <label for="voiture_id">Voiture :</label>
                                    <select class="form-control" id="voiture_id" name="voiture_id" required>
                                        <option value="">Sélectionner votre voiture</option>
                                        <?php if (!empty($voitureInfos)): ?>
                                            <?php foreach ($voitureInfos as $voitureInfo): ?>
                                                <option name="voiture_id" value="<?= htmlspecialchars($voitureInfo->voiture_id) ?>">
                                                    <?= htmlspecialchars($voitureInfo->modele) ?> (Immatriculation:
                                                    <?= htmlspecialchars($voitureInfo->immatriculation) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="" disabled>Vous n'avez pas de voiture enregistrée.</option>
                                        <?php endif; ?>
                                    </select>
                                    <?php if (empty($voitureInfos)): ?>
                                        <small class="form-text text-muted">Vous devez enregistrer une voiture dans votre <a
                                                href="/pages/account.php#section3">profil</a> avant de publier un trajet.</small>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="depart">Ville de départ :</label>
                                    <input type="text" class="form-control" name="lieu_depart" id="depart"
                                        placeholder="Ville de départ" required>
                                </div>

                                <div class="form-group">
                                    <label for="arrivee">Ville d'arrivée :</label>
                                    <input type="text" class="form-control" name="lieu_arrivee" id="arrivee"
                                        placeholder="Ville d'arrivée" required>
                                </div>

                                <div class="form-group">
                                    <label for="date_depart">Date de départ :</label>
                                    <input type="date" class="form-control" name="date_depart" id="date_depart"
                                        placeholder="Date de départ" required>
                                </div>

                                <div class="form-group">
                                    <label for="heure_depart">Heure de départ :</label>
                                    <input type="time" class="form-control" name="heure_depart" id="heure_depart"
                                        placeholder="Heure de départ" required>
                                </div>

                                <div class="form-group">
                                    <label for="date_arrivee">Date d'arrivée :</label>
                                    <input type="date" class="form-control" name="date_arrivee" id="date_arrivee"
                                        placeholder="Date d'arrivée">
                                    <small class="form-text text-muted">Facultatif.</small>
                                </div>

                                <div class="form-group">
                                    <label for="heure_arrivee">Heure d'arrivée :</label>
                                    <input type="time" class="form-control" name="heure_arrivee" id="heure_arrivee"
                                        placeholder="Heure d'arrivée">
                                    <small class="form-text text-muted">Facultatif.</small>
                                </div>

                                <div class="form-group">
                                    <label for="prix_personne">Prix par personne (en crédits) :</label>
                                    <input type="number" class="form-control" name="prix_personne" id="prix" placeholder="Prix"
                                        min="0" required>
                                </div>

                                <div class="form-group">
                                    <label for="nb_place">Nombre de places disponibles :</label>
                                    <input type="number" class="form-control" name="nb_place" id="places"
                                        placeholder="Nombre de places" min="1" required>
                                </div>

                                <div class="form-group">
                                    <label for="commentaire">Informations complémentaires :</label>
                                    <textarea class="form-control" name="commentaire" id="commentaire" rows="3"
                                        placeholder="Ajoutez un commentaire (ex: détails sur le point de rencontre, etc.)"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="prix_publication">Nombre de credit prelevé pour publication d'un trajet
                                        :</label>
                                    <strong type="number" class="form-control" id="prix_publication">2</strong>
                                    <input type="hidden" type="number" name="prix_publication" id="prix_publication" value="2">
                                </div></br>

                                <div class="publier-trajet-actions">
                                    <button type="submit" class="btn btn-primary publier-trajet-btn" name="publier_trajet"
                                        <?php if (empty($voitureInfos)) echo 'disabled'; ?>>
                                        Publier le trajet
                                    </button>
                                </div>
                            </form>
                        </div>



                    </div></br>
                    <div class="ligne-horizontale"></div></br>
                    <h1 id="section4">Historique des trajets</h1>

                    <?php if (!empty($covoituragesEnCours)) : ?>
                        <?php foreach ($covoituragesEnCours as $covoiturage): ?>
                            <strong>Départ :</strong> <?= htmlspecialchars($covoiturage->lieu_depart) ?>
                            <br>
                            <strong>Arrivée :</strong> <?= htmlspecialchars($covoiturage->lieu_arrivee) ?>
                            <br>
                            <strong>Date :</strong><?= htmlspecialchars($covoiturage->date_depart) ?>
                            </br>
                            <strong>Statut :</strong><?= htmlspecialchars($covoiturage->statut) ?>
                            <br>
                            <div class="ligne-horizontale"></div></br>

                        <?php endforeach; ?>
                    <?php else : ?>
                        <p>Vous n'avez pas de covoiturage en cours.</p>
                    <?php endif; ?>

                    <div class="ligne-horizontale"></div></br>
                    <h1 id="section5">Co-voiturage en cours</h1>
                    <?php if (!empty($covoituragesEnCours)) : ?>
                        <?php foreach ($covoituragesEnCours as $covoiturage): ?>
                            <div class="publication-cadre">
                                <div class="publication-header">
                                    <div class="utilisateur-info">
                                        <span class="date-creation">**Publié le :
                                            <?= htmlspecialchars($covoiturage->created_at) ?>**</span>
                                        </span>
                                    </div>
                                </div>

                                <div class="publication-details">
                                    <div class="trajet">
                                        <h3>Trajet</h3>
                                        <p>
                                            <strong>Départ :</strong> <?= htmlspecialchars($covoiturage->lieu_depart) ?>
                                            <br>
                                            <strong>Arrivée :</strong> <?= htmlspecialchars($covoiturage->lieu_arrivee) ?></br>
                                            <strong>Durée du trajet :</strong> <?= htmlspecialchars($covoiturage->duree) ?>
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
                                        <form method="post">
                                            <input type="hidden" name="covoiturage_id"
                                                value="<?= htmlspecialchars($covoiturage->covoiturage_id) ?>">
                                            <button type="submit" name="demarrer_trajet" class="btn btn-success"
                                                <?= ($covoiturage->statut !== 'en_attente') ? 'disabled' : '' ?>>
                                                Démarrer le trajet
                                            </button>
                                            <button type="submit" name="terminer_trajet" class="btn btn-danger"
                                                <?= ($covoiturage->statut !== 'en_cours') ? 'disabled' : '' ?>>
                                                Terminer le trajet
                                            </button>
                                            <input type="hidden" type="number" name="prix_publication" id="prix_publication" value="2">
                                            <button type="submit" name="annuler_trajet" class="btn btn-warning"
                                                <?= ($covoiturage->statut !== 'en_attente') ? 'disabled' : '' ?>>
                                                Annuler le trajet
                                            </button>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p>Vous n'avez pas de covoiturage en cours.</p>
                    <?php endif; ?>

                    <div class="ligne-horizontale"></div></br>
                    <h1 id="section6">Laisser un avis et une note</h1>


                    <?php foreach ($resultats as $resultat) : ?>
                        <?php if ($resultat->statut === "terminer") : ?>
                            <div class="publication-cadre">
                                <div class="publication-header">
                                    <div class="utilisateur-info">

                                        <span class="date-creation">**Publié le :
                                            <?= htmlspecialchars($resultat->created_at) ?>**</span>
                                    </div>
                                </div>

                                <div class="publication-details">
                                    <div class="trajet">
                                        <h3>Trajet</h3>
                                        <p>
                                            <strong>Départ :</strong> <?= htmlspecialchars($resultat->lieu_depart) ?><br>
                                            <strong>Arrivée :</strong> <?= htmlspecialchars($resultat->lieu_arrivee) ?>
                                        </p>
                                    </div>

                                    <div class="dates">
                                        <h3>Dates et Horaires</h3>
                                        <p>
                                            <strong>Départ :</strong>
                                            <?= htmlspecialchars(date('d/m/Y', strtotime($resultat->date_depart))) ?> à
                                            <?= htmlspecialchars(date('H:i', strtotime($resultat->heure_depart))) ?> h<br>
                                            <strong>Arrivée :</strong>
                                            <?= htmlspecialchars(date('d/m/Y', strtotime($resultat->date_arrivee))) ?> à
                                            <?= htmlspecialchars(date('H:i', strtotime($resultat->heure_arrivee))) ?> h
                                        </p>
                                    </div>

                                </div></br>

                                <div class="avis-form">
                                    <h1>Laisser un avis :</h1>
                                    <form method="post">
                                        <input type="hidden" name="covoiturage_id"
                                            value="<?= htmlspecialchars($resultat->covoiturage_id) ?>">
                                        <input type="hidden" type="number" name="prix_personne"
                                            value="<?= htmlspecialchars($resultat->prix_personne) ?>">
                                        <input type="hidden" name="chauffeur_id"
                                            value="<?= htmlspecialchars($resultat->chauffeur_id) ?>">
                                        <div class="form-group">
                                            <label for="note">Note (sur 5) :</label>
                                            <select class="form-control" name="note" id="note">
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="commentaire">Commentaire :</label>
                                            <textarea class="form-control" name="commentaire" id="commentaire" rows="3"></textarea>
                                        </div>
                                        <button type="submit" name="poster_avis" class="btn btn-success">Poster votre avis</button>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

        </div>
    </div>
</div>
</body>

</html>
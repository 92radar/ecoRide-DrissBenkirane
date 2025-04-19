<?php

require_once '/home/clients/5afa198c535310a01279d2a30398c842/sites/eco-ride.online/backend/covoiturageBe.php';
require_once '/home/clients/5afa198c535310a01279d2a30398c842/sites/eco-ride.online/elements/header.php';
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
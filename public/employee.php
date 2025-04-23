<?php
session_start();


require_once "/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/second_header.php";
require_once "/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/backend/employeeBe.php";

require_once "/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/mobile-nav.php";
?>
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
                <li><a href="#section2">Verifier les Avis</a></li>
                <li><a href="#section3">Avis verifié</a></li>
                <li><a href="#section4">Historique des trajets</a></li>

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
                    <h3>Verifier les avis</h3></br>
                    <form method="GET" id="verifierAvisForm">
                        <div class="form-group">
                            <label for="avis_id">Sélectionner un avis :</label>
                            <select class="form-control" id="avis_id" name="avis_id" onchange="this.form.submit()">
                                <option value="">Sélectionner un avis</option>
                                <?php if (!empty($avis)) : ?>
                                    <?php foreach ($avis as $unAvis) : ?>
                                        <option value="<?= htmlspecialchars($unAvis->avis_id) ?>"
                                            <?= isset($_GET['avis_id']) && $_GET['avis_id'] == $unAvis->avis_id ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($unAvis->nom) ?> <?= htmlspecialchars($unAvis->prenom) ?>
                                            (<?= htmlspecialchars($unAvis->statut_avis) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </form>



                    <?php if ($avisDetails) : ?>

                        <div class="form-group">
                            <form method="POST" action="">
                                <label for="statut_avis">Changer le statut de l'avis :</label>
                                <select class="form-control" id="statut_avis" name="statut_avis">
                                    <option value="en_attente">
                                        En attente
                                    </option>
                                    <option value="validé">
                                        Accepté
                                    </option>
                                    <option value="refuser">
                                        Refusé
                                    </option>
                                </select>
                                <input type="hidden" name="avis_id" value="<?= htmlspecialchars($avisDetails->avis_id) ?>">
                                <strong>Commentaire :</strong></br>
                                <textarea class="form-control" name="commentaire" rows="4"
                                    required><?= htmlspecialchars($avisDetails->commentaire) ?></textarea></br>
                                <strong class="form-control">Note :
                                    <?= htmlspecialchars($avisDetails->note) ?> </strong></br>
                                <button type="submit" name="changer_statut" class="btn btn-primary">Changer le statut</button>
                            </form>
                        </div>


        </div>
    <?php else : ?>
        <p>Aucun avis sélectionné.</p>
    <?php endif; ?>
    <div class="ligne-horizontale"></div></br>
    <h3>Les avis vérifiés</h3></br>
    <form method="GET" id="verifierAvisForm">
        <div class="form-group">
            <label for="avis_id">Sélectionner un avis :</label>
            <select class="form-control" id="avis_id" name="avis_id" onchange="this.form.submit()">
                <option value="">Sélectionner un avis</option>
                <?php if (!empty($avisVerifie)) : ?>
                    <?php foreach ($avisVerifie as $unAvisVerifie) : ?>
                        <option value="<?= htmlspecialchars($unAvisVerifie->avis_id) ?>"
                            <?= isset($_GET['avis_id']) && $_GET['avis_id'] == $unAvisVerifie->avis_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($unAvisVerifie->nom) ?> <?= htmlspecialchars($unAvisVerifie->prenom) ?>
                            (<?= htmlspecialchars($unAvisVerifie->statut_avis) ?>)
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
    </form>

    <div class="ligne-horizontale"></div></br>
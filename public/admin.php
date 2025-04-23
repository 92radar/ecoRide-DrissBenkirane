<?php

session_start();
require_once "/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/second_header.php";
require_once "/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/backend/adminBe.php";

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
                <li><a href="#section2">Creer un compte employé
                    </a></li>
                <li><a href="#section3">Gestion des comptes employé</a></li>
                <li><a href="#section4">Graphique des activitées de l'entreprise</a></li>

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

                        <div class="ligne-horizontale"></div></br>
                        <h3>Cree un compte employé</h3></br>
                        <form action="" method="post">
                            <strong>Nom :</strong></br><input class="form-control" type="text" name="nom" required></br>
                            <strong>Prénom :</strong></br><input class="form-control" type="text" name="prenom" required></br>
                            <strong>Pseudo :</strong></br><input class="form-control" type="text" name="pseudo" required></br>
                            <strong>Date de naissance :</strong></br><input class="form-control" type="date"
                                name="date_naissance" required></br>
                            <strong>Email :</strong></br><input class="form-control" type="email" name="email" required></br>
                            <strong>Adresse :</strong></br><input class="form-control" type="text" name="adresse" required></br>
                            <strong>Ville :</strong></br><input class="form-control" type="text" name="ville" required></br>
                            <strong>Numéro de téléphone :</strong></br><input class="form-control" type="text" name="telephone"
                                required></br>
                            <strong>Mot de passe :</strong></br><input class="form-control" type="password" name="password"
                                required></br>
                            <strong>Confirmer le mot de passe :</strong></br><input class="form-control" type="password"
                                name="confirm_password" required></br>
                            <legend> Role de l'employé : </legend></br>
                            <select class="form-control" id="role" name="role"></br>
                                <option value="">Sélectionner un role</option>
                                <option value="employee"> Employé</option>
                                <option value="user"> Utilisateur</option>
                            </select></br>


                            <button type="submit" name="creer_compte_employe" class="creer-compte-btn">Créer un compte
                                employé</button></br>


                        </form>
                    </div>
        </div>


        <div class="ligne-horizontale"></div></br>
        <h3>Gestion des comptes employé et utilisateur</h3></br>
        <form method="GET" id="verifierAvisForm">
            <div class="form-group">
                <label for="avis_id">Sélectionner un compte :</label>
                <select class="form-control" id="user_id" name="user_id" onchange="this.form.submit()">
                    <option value="">Sélectionner un compte</option>
                    <?php if (!empty($users)) : ?>
                        <?php foreach ($users as $user) : ?>
                            <option value="<?= htmlspecialchars($user->user_id) ?>"
                                <?= isset($_GET['user_id']) && $_GET['user_id'] == $employee->user_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user->nom) ?> <?= htmlspecialchars($user->prenom) ?>
                                (<?= htmlspecialchars($user->role) ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </form>

        <div class="ligne-horizontale"></div>

        </br>
        <?php if ($userDetails) : ?>

            <div class="form-group">
                <h3>Informations de l'employé sélectionné</h3>
                <strong>Nom :</strong></br><input class="form-control" type="text" name="nom"
                    value="<?= htmlspecialchars($userDetails->nom) ?>" disabled></br>
                <strong>Prénom :</strong></br><input class="form-control" type="text" name="prenom"
                    value="<?= htmlspecialchars($userDetails->prenom) ?>" disabled></br>
                <strong>Pseudo :</strong></br><input class="form-control" type="text" name="pseudo"
                    value="<?= htmlspecialchars($userDetails->pseudo) ?>" disabled></br>
                <strong>Date de naissance :</strong></br><input class="form-control" type="date" name="date_naissance"
                    value="<?= htmlspecialchars($userDetails->date_naissance) ?>" disabled></br>
                <strong>Email :</strong></br><input class="form-control" type="email" name="email"
                    value="<?= htmlspecialchars($userDetails->email) ?>" disabled></br>
                <strong>Telephone :</strong></br><input class="form-control" type="number" name="telephone"
                    value="<?= htmlspecialchars($userDetails->telephone) ?>" disabled></br>
                <form method="POST" action="">
                    <label for="statut_avis">Changer le role de l'employé :</label></br>
                    <select class="form-control" id="user_role" name="user_role">
                        <option value="">Sélectionner un role</option>
                        <option value="employee">
                            Employé
                        </option>
                        <option value="user">
                            Utilisateur
                        </option>


                    </select>
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($userDetails->user_id) ?>">

                    <button type="submit" name="changer_role" class="btn btn-primary">Changer le role</button>
                    <button type="submit" name="supprimer_compte" class="btn btn-danger">Supprimer le compte</button>
                </form>
            </div>

        <?php else : ?>
            <p>Aucun employé sélectionné.</p>
        <?php endif; ?>
        <div class="ligne-horizontale"></div></br>
        <h3>Graphique des activitées de l'entreprise</h3></br>
        <title>Nombre de covoiturages par jour</title>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        </head>

        <body>
            <h1>Nombre de covoiturages par jour</h1>
            <div class="ligne-horizontale"></div></br>
            <h3>Graphique des activitées de l'entreprise</h3></br>
            <h4>Nombre de covoiturages par jour (7 derniers jours)</h4>
            <div>
                <canvas id="monGraphiqueCovoiturages"></canvas>
            </div>
            <script>
                const ctxCovoiturages = document.getElementById('monGraphiqueCovoiturages').getContext('2d');
                const monGraphiqueCovoiturages = new Chart(ctxCovoiturages, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($joursCovoiturages); ?>,
                        datasets: [{
                            label: 'Nombre de covoiturages',
                            data: <?php echo json_encode($nombresCovoiturages); ?>,
                            backgroundColor: 'rgba(54, 162, 235, 0.8)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Nombre de covoiturages'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Jour (JJ/MM)'
                                }
                            }
                        }
                    }
                });
            </script>

            <div class="ligne-horizontale"></div></br>

            <title>Gains de crédit par jour</title>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



            <h1>Gains de crédit par jour (<?php echo date('d/m/Y', strtotime($debutPeriode)); ?> au
                <?php echo date('d/m/Y', strtotime($finPeriode)); ?>)</h1>

            <div>
                <canvas id="monGraphiqueCredits"></canvas>
            </div>
            <h2>Crédits gagnés par jour</h2>
            <script>
                const ctxCredits = document.getElementById('monGraphiqueCredits').getContext('2d');
                const monGraphiqueCredits = new Chart(ctxCredits, {
                    type: 'line', // Un graphique linéaire est souvent plus approprié pour visualiser des tendances
                    data: {
                        labels: <?php echo json_encode($jours); ?>,
                        datasets: [{
                            label: 'Crédits gagnés',
                            data: <?php echo json_encode($credits); ?>,
                            borderColor: 'rgba(75, 192, 192, 1)', // Couleur de la ligne
                            backgroundColor: 'rgba(75, 192, 192, 0.2)', // Couleur de fond sous la ligne (facultatif)
                            borderWidth: 2,
                            pointRadius: 5,
                            pointBackgroundColor: 'rgba(75, 192, 192, 1)'
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Crédits'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Jour'
                                }
                            }
                        }
                    }
                });
            </script>
        </body>

        </html>
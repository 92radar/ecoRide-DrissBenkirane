<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 'On');
ini_set('error_log', '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/php-error.log');

$userInfos = null; // Initialisation de $userInfos
$userDetails = null; // Initialisation de $userDetails
$avisDetails = null; // Initialisation de $avisDetails
$pdo = new PDO("sqlite:/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/ecoride.db");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $userId = $_SESSION['user_id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $userInfos = $stmt->fetchAll(PDO::FETCH_OBJ); // Utilisation de fetchAll pour récupérer tous les résultats
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération des informations de l'utilisateur : " . $e->getMessage();
    }
    try {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_OBJ); // Utilisation de fetchAll pour récupérer tous les résultats
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération des informations de l'utilisateur : " . $e->getMessage();
    }
    function getDebutFinSemaine($annee, $numeroSemaine)
    {
        $dateTime = new DateTime();
        $dateTime->setISODate($annee, $numeroSemaine);
        $debutSemaine = $dateTime->format('Y-m-d');
        $dateTime->modify('+6 days');
        $finSemaine = $dateTime->format('Y-m-d');
        return ['debut' => $debutSemaine, 'fin' => $finSemaine];
    }

    // Récupérer l'année actuelle
    $anneeActuelle = date('Y');

    // Générer la liste des semaines disponibles (vous pouvez adapter cette logique)
    $nombreSemainesAAfficher = 5; // Nombre de semaines à afficher dans la liste
    $optionsSemaines = [];
    for ($i = 0; $i < $nombreSemainesAAfficher; $i++) {
        $numeroSemaine = date('W', strtotime("-$i week"));
        $anneeSemaine = date('Y', strtotime("-$i week"));
        $optionsSemaines[$anneeSemaine . '-' . $numeroSemaine] = "Semaine $numeroSemaine ($anneeSemaine)";
    }
    krsort($optionsSemaines); // Trier les semaines par ordre décroissant

    // Récupérer la semaine sélectionnée depuis le formulaire
    $semaineSelectionnee = $_GET['semaine'] ?? date('Y') . '-' . date('W'); // Semaine actuelle par défaut
    list($anneeSelectionnee, $numeroSemaineSelectionnee) = explode('-', $semaineSelectionnee);

    // Obtenir les dates de début et de fin de la semaine sélectionnée
    $datesSemaineSelectionnee = getDebutFinSemaine($anneeSelectionnee, $numeroSemaineSelectionnee);
    $debutSemaine = $datesSemaineSelectionnee['debut'];
    $finSemaine = $datesSemaineSelectionnee['fin'];

    // Requête SQL pour compter le nombre de covoiturages par jour pour la semaine sélectionnée
    $sql = "SELECT DATE(date_depart) AS jour, COUNT(*) AS nombre_covoiturages
            FROM covoiturages
            WHERE date_depart >= :debut AND date_arrivee <= :fin
            GROUP BY DATE(date_depart)
            ORDER BY DATE(date_depart)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':debut', $debutSemaine);
    $stmt->bindParam(':fin', $finSemaine);
    $stmt->execute();
    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $jours = [];
    $nombres = [];
    $covoituragesParJour = [];
    foreach ($resultats as $row) {
        $covoituragesParJour[$row['jour']] = $row['nombre_covoiturages'];
    }

    $joursSemaine = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
    $datesSemaine = [];
    for ($i = 0; $i < 7; $i++) {
        $datesSemaine[] = date('Y-m-d', strtotime("$debutSemaine + $i days"));
    }

    foreach ($datesSemaine as $date) {
        $jours[] = date('l', strtotime($date));
        $nombres[] = isset($covoituragesParJour[$date]) ? $covoituragesParJour[$date] : 0;
    }

    $debutPeriode = date('Y-m-d', strtotime('-6 days'));
    $finPeriode = date('Y-m-d');

    // Requête SQL pour calculer le total des crédits gagnés par jour
    $sql = "SELECT DATE(date_depart) AS jour, SUM(credit_depense) AS total_credit
        FROM participations
        WHERE date_depart >= :debut AND date_depart <= :fin
        GROUP BY DATE(date_depart)
        ORDER BY DATE(date_depart)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':debut', $debutPeriode);
    $stmt->bindParam(':fin', $finPeriode);
    $stmt->execute();
    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $jours = [];
    $credits = [];
    $creditsParJour = [];
    foreach ($resultats as $row) {
        $creditsParJour[$row['jour']] = $row['total_credit'];
    }

    // Générer un tableau des 7 derniers jours pour s'assurer qu'ils sont tous présents
    $joursPeriode = [];
    for ($i = 0; $i < 7; $i++) {
        $joursPeriode[] = date('Y-m-d', strtotime("-$i days"));
    }
    $joursPeriode = array_reverse($joursPeriode); // Inverser pour avoir l'ordre chronologique

    foreach ($joursPeriode as $date) {
        $jours[] = date('d/m', strtotime($date)); // Format d'affichage du jour
        $credits[] = isset($creditsParJour[$date]) ? floatval($creditsParJour[$date]) : 0; // Assurer un type numérique
    }
} else {
    header("Location: http://localhost:4000/pages/home.php"); // Redirige vers la page home
    exit();
}
if (isset($_POST['modifier'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $dateNaissance = $_POST['date_naissance'];
    $email = $_POST['email'];
    $adresse = $_POST['adresse'];
    $ville = $_POST['ville'];
    $telephone = $_POST['telephone'];
    $pseudo = $_POST['pseudo'];

    try {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = :nom, prenom = :prenom, date_naissance = :date_naissance, email = :email, adresse = :adresse, ville = :ville, telephone = :telephone, pseudo = :pseudo WHERE user_id = :user_id");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':date_naissance', $dateNaissance);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':ville', $ville);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':pseudo', $pseudo);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $success = "Vos informations ont été mises à jour avec succès.";
    } catch (PDOException $e) {
        $error = "Erreur lors de la mise à jour des informations : " . $e->getMessage();
    }
}
if (isset($_FILES["photo_profil"]) && $_FILES["photo_profil"]["error"] == 0) {
    $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
    $filename = $_FILES["photo_profil"]["name"];
    $filetype = $_FILES["photo_profil"]["type"];
    $filesize = $_FILES["photo_profil"]["size"];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);



    if (array_key_exists($ext, $allowed) && in_array($filetype, $allowed) && $filesize <= (5 * 1024 * 1024)) { // Exemple de validation
        $new_filename = uniqid() . "." . $ext;

        $upload_dir = "uploads/"; // Assurez-vous que ce dossier existe et est accessible en écriture
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $photo_profil_path = $upload_dir . $new_filename;

        if (!move_uploaded_file($_FILES["photo_profil"]["tmp_name"], $photo_profil_path)) {
            $error = "Erreur lors de l'upload de la photo.";
        } else {
            // Mettre à jour le chemin de la photo de profil dans la base de données
            try {
                $stmt = $pdo->prepare("UPDATE utilisateurs SET photo = :photo_profil WHERE user_id = :id");
                $stmt->bindParam(':photo_profil', $photo_profil_path);
                $stmt->bindParam(':id', $userId);
                $stmt->execute();
                $success = "Informations et photo de profil mises à jour avec succès!";
                $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE user_id = :id");
                $stmt->bindParam(':id', $userId);
                $stmt->execute();
                $userInfos = $stmt->fetchAll(PDO::FETCH_OBJ);
            } catch (PDOException $e) {
                $error = "Erreur lors de la mise à jour du chemin de la photo de profil : " . $e->getMessage();
            }
        }
    } else {
        $error = "Format ou taille de fichier non autorisé pour la photo.";
    }
}


if (isset($_POST['creer_compte_employe'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $dateNaissance = $_POST['date_naissance'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $telephone = $_POST['telephone'];
    $adresse = $_POST['adresse'];
    $ville = $_POST['ville'];
    $pseudo = $_POST['pseudo'];
    $role = $_POST['role'];



    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    try {
        $stmtUtilisateur = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, date_naissance, email, password, telephone, adresse, ville, pseudo, role)
            VALUES (:nom, :prenom, :date_naissance, :email, :password, :telephone, :adresse, :ville, :pseudo, :role)");
        $stmtUtilisateur->bindParam(':nom', $nom);
        $stmtUtilisateur->bindParam(':prenom', $prenom);
        $stmtUtilisateur->bindParam(':date_naissance', $dateNaissance);
        $stmtUtilisateur->bindParam(':email', $email);
        $stmtUtilisateur->bindParam(':password', $hashedPassword);
        $stmtUtilisateur->bindParam(':telephone', $telephone);
        $stmtUtilisateur->bindParam(':adresse', $adresse);
        $stmtUtilisateur->bindParam(':ville', $ville);
        $stmtUtilisateur->bindParam(':pseudo', $pseudo);
        $stmtUtilisateur->bindParam(':role', $role);
        $stmtUtilisateur->execute();
        $success = "Votre compte a été créé avec succès";
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}
if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {

    $selectedUserId = $_GET['user_id'];
    try {
        $stmtUserDetails = $pdo->prepare("SELECT *
                                               FROM utilisateurs WHERE user_id = :user_id");
        $stmtUserDetails->bindParam(':user_id', $selectedUserId, PDO::PARAM_INT);
        $stmtUserDetails->execute();
        $userDetails = $stmtUserDetails->fetchAll(PDO::FETCH_OBJ);
        if ($userDetails) {
            $userDetails = $userDetails[0]; // Récupérer le premier élément
        }
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}
if (isset($_POST['changer_role'])) {
    $userId = $_POST['user_id'];
    $role = $_POST['user_role'];

    try {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET role = :role WHERE user_id = :user_id");
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $success = "Le rôle de l'employé a été mis à jour avec succès.";
    } catch (PDOException $e) {
        $error = "Erreur lors de la mise à jour du rôle : " . $e->getMessage();
    }
}
if (isset($_POST['supprimer_compte'])) {
    $userId = $_POST['user_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $success = "Le compte a été supprimé avec succès.";
    } catch (PDOException $e) {
        $error = "Erreur lors de la suppression du compte : " . $e->getMessage();
    }
}







require_once '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/header.php';
?>
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
        <form method="post">
            <button type="submit" name="logout" class="logout-btn">Se déconnecter</button>
        </form>
    </div>

    <div style="flex-grow: 1;">
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

            <form method="get">
                <label for="semaine">Sélectionner la semaine :</label>
                <select name="semaine" id="semaine" onchange="this.form.submit()">
                    <?php foreach ($optionsSemaines as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php if ($value == $semaineSelectionnee) echo 'selected'; ?>>
                            <?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
            </form>

            <h2>Semaine du <?php echo date('d/m/Y', strtotime($debutSemaine)); ?> au
                <?php echo date('d/m/Y', strtotime($finSemaine)); ?></h2>

            <div>
                <canvas id="monGraphique"></canvas>
            </div>

            <script>
                const ctx = document.getElementById('monGraphique').getContext('2d');
                const monGraphique = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($jours); ?>,
                        datasets: [{
                            label: 'Nombre de covoiturages',
                            data: <?php echo json_encode($nombres); ?>,
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
                                    text: 'Jour de la semaine'
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
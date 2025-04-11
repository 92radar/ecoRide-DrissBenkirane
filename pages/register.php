<?php




ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 'On');
ini_set('error_log', '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/php-error.log');




$pdo = new PDO("sqlite:/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/ecoride.db");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



$error = null;
$success = null;




try {
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, date_naissance, email, password, telephone, adresse, ville, pseudo)
    VALUES (:nom, :prenom, :date_naissance, :email, :password, :telephone, :adresse, :ville, :pseudo)");

    if (isset($_POST['submit'])) {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $dateNaissance = $_POST['date_naissance'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $telephone = $_POST['telephone'];
        $adresse = $_POST['adresse'];
        $ville = $_POST['ville'];
        $pseudo = $_POST['pseudo'];


        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'date_naissance' => $dateNaissance,
            'email' => $email,
            'password' => $hashedPassword,
            'telephone' => $telephone,
            'adresse' => $adresse,
            'ville' => $ville,
            'pseudo' => $pseudo

        ]);
        $success = "Votre compte a été créé avec succès";
    }
} catch (PDOException $e) {
    $error = $e->getMessage();
}


require_once '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/second_header.php';
?>


<body>
    <?php if ($error) : ?>
    <div class="alert alert-danger" role="alert">
        <?= $error ?>
    </div>
    <?php endif; ?>
    <?php if ($success) : ?>
    <div class="alert alert-success" role="alert">
        <?= $success ?>
    </div>
    <?php endif; ?>
    <div class="container">
        <h2>Inscription</h2>
        <form action="" method="post" id="form-inscription" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="nom">Nom:</label>
                <input type="text" id="nom" name="nom" required>
            </div>
            <div class="form-group">
                <label for="prenom">Prénom:</label>
                <input type="text" id="prenom" name="prenom" required>
            </div>
            <div class="form-group">
                <label for="prenom">Pseudo:</label>
                <input type="text" id="pseudo" name="pseudo" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="mot_de_passe">Mot de passe:</label>
                <input type="password" id="password" name="password" required>
                <div id="password-feedback" style="color: red; font-size: 0.9em; margin-top: 5px;"></div>

                </ul>
            </div>
            <div class="form-group">
                <label for="verif_mot_de_passe">Vérifiez le mot de passe:</label>
                <input type="password" id="verif_mot_de_passe" name="verif_mot_de_passe" required>
                <div id="password_error" class="error-message"></div>
            </div>
            <div class="form-group">
                <label for="ville">Ville:</label>
                <input type="text" id="ville" name="ville" required>
            </div>
            <div class="form-group">
                <label for="adresse">Adresse:</label>
                <input type="text" id="adresse" name="adresse" required>
            </div>
            <div class="form-group">
                <label for="telephone">Numéro de téléphone:</label>
                <input type="text" id="telephone" name="telephone" required>
            </div>

            <div class="form-group">
                <label for="date_naissance">Date de naissance:</label>
                <input type="date" id="date_naissance" name="date_naissance" required>
            </div>
            <button type="submit" id="submit-btn" name="submit">S'inscrire</button>
        </form>
    </div>
    <script src="/JS/passwordverify.js"></script>
    <script src="/JS/passwordSecurity.js"></script>
</body>

<?php
require_once '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/elements/footer.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
    integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

<?php
$pdo = new PDO("mysql:host=localhost;dbname=EcorideDatabase", "Driss", "Bluecrush92");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$error = null;
$success = null;


$nom = $_POST['nom'];
$prenom = $_POST['prenom'];
$email = $_POST['email'];
$pseudo = $_POST['pseudo'];
$password = $_POST['password'];


$hashed_password = password_hash($password, PASSWORD_BCRYPT);


try {
    if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['email']) && isset($_POST['pseudo']) && isset($_POST['password'])) {
        $error = "Veuillez remplir tous les champs";


        $insertquery = 'INSERT INTO utilisateurs (nom, prenom, email, pseudo, password) VALUES (:nom, :prenom, :email, :pseudo, :password)';
        $stmt = $pdo->prepare($insertquery);
        $stmt->bindValue(':nom', $nom);
        $stmt->bindValue(':prenom', $prenom);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':pseudo', $pseudo);
        $stmt->bindValue(':password', $hashed_password);
        $stmt->execute();
        $success = "Votre compte a été créé avec succès";
        $lignesAffectees = $stmt->rowCount();
        if ($lignesAffectees > 0) {
            $sucess = true;
        }
    }
} catch (PDOException $e) {
    echo "Erreur lors de l’inscription : " . $e->getMessage();
}



?>





<div class="container">
    <?php if ($sucess = true): ?>
    <div class="alert alert-success">
        <?= $success ?></div>
    <?php endif; ?>

    <form method="post" action="register.php">
        <h1>Créer un compte</h1>
        <label for="nom">Nom :</label>
        <input type="text" name="nom" id="nom" required><br>
        <label for="prenom">prenom :</label>
        <input type="text" name="prenom" id="prenom" required><br>
        <label for="pseudo">Pseudo :</label>
        <input type="text" name="pseudo" id="pseudo" required><br>

        <label for="password">Mot de passe :</label>
        <input type="password" name="password" id="password" required><br>

        <label for="email">Email :</label>
        <input type="email" name="email" id="email" required><br>

        <button type="submit" class="btn btn-primary" name="register">S'inscrire</button>
    </form>
</div>
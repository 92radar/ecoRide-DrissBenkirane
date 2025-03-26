<link rel="stylesheet" href="styles/utilisateur.css">
<?php

$pdo = new PDO("mysql:host=localhost;dbname=EcorideDatabase", "Driss", "Bluecrush92");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$error = null;
try {
    $query = $pdo->prepare("SELECT * FROM utilisateurs ");
    $query->execute();
    $utilisateurs = $query->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    $error = $e->getMessage();
}

?>
<?php if ($error): ?>
<div class="alert alert-danger">
    <?= $error ?>
</div>
<?php else: ?>
<div class="container">
    <h1>liste des utilisateurs</h1>
    <ul class="list-group">
        <?php foreach ($utilisateurs as $utilisateur): ?>


        <li><?= $utilisateur->nom ?></li>


        <li class="list-group-item"><a href="profil_utilisateur.php?id=<?= $utilisateur->utilisateur_id ?>">
                <?= htmlentities($utilisateur->pseudo) ?></a>
        </li>


    </ul>
    <?php endforeach; ?>
    <?php endif; ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
    integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">



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


        <li class="list-group-item"><a href="profil_utilisateur.php?id=<?= $utilisateur->utilisateur_id ?>">
                <?= htmlentities($utilisateur->pseudo) ?></a>
        </li>


    </ul>
    <?php endforeach; ?>
    <?php endif; ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
    integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">



<?php

$pdo = new PDO("mysql:host=localhost;dbname=EcorideDatabase", "Driss", "Bluecrush92");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$id = $pdo->quote($_GET['id']);
$error = null;;

try {
    $query = $pdo->prepare("SELECT * FROM avis WHERE avis_id = :id");
    $query->execute(['id' => $_GET['id']]);
    $avis = $query->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    $error = $e->getMessage();
}
try {
    $query = $pdo->prepare('SELECT * FROM utilisateurs WHERE utilisateur_id = :id');
    $query->execute(['id' => $_GET['id']]);
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

    <h2>Les avis de l'utilisateur</h2>
    <ul class="list-group">
        <?php foreach ($avis as $avi): ?>


        <li class="list-group-item">ID :
            <?= $avi->avis_id ?>
        </li>
        <li class="list-group-item">Commentaire :
            <?= $avi->commentaire ?>
        </li>
        <li class="list-group-item">note :
            <?= $avi->note ?>
        </li>
        <li class="list-group-item">statut :<?= $avi->statut ?>
        </li>



        <li> </li>
        <?php endforeach; ?>
    </ul>


    <a href='Liste_utilisateur.php'>revenir à la liste des utilisateurs</a>

</div>



<?php endif; ?>
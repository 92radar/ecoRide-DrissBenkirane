<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../vendor/autoload.php';

use MongoDB\Client;

$client = new Client("mongodb://localhost:27017");
$collection = $client->ma_base->utilisateurs; // This targets the 'utilisateurs' collection

// Charger les données JSON
$json = file_get_contents('yx2Loi_ecoride098765456.json');
$full_json_data = json_decode($json, true); // Decode the entire JSON structure

$users_data_found = false;
$users_to_insert = [];

// Iterate through the top-level array to find the 'utilisateurs' table data
if (is_array($full_json_data)) {
    foreach ($full_json_data as $block) {
        if (isset($block['type']) && $block['type'] === 'table' && $block['name'] === 'utilisateurs') {
            $users_to_insert = $block['data']; // This is the array of user documents
            $users_data_found = true;
            break; // Found the users data, no need to continue
        }
    }
}

// Supprimer les anciens pour éviter doublons et insérer les nouveaux
if ($users_data_found && is_array($users_to_insert) && !empty($users_to_insert)) {
    $collection->drop(); // Drop the existing collection
    $collection->insertMany($users_to_insert); // Insert only the user documents
    echo "Données utilisateurs importées avec succès.<br>";
} else {
    echo "Erreur : Les données des utilisateurs n'ont pas été trouvées ou le fichier JSON est mal formé/vide.<br>";
}

// Récupération des utilisateurs pour affichage
$utilisateurs = $collection->find();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des utilisateurs</title>
    <style>
        body {
            font-family: Arial;
            margin: 2rem;
        }

        table {
            border-collapse: collapse;
            width: 60%;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 0.5rem;
        }
    </style>
</head>

<body>
    <h1>Liste des utilisateurs</h1>
    <table>
        <tr>
            <th>Nom</th>
            <th>Email</th>
        </tr>
        <?php foreach ($utilisateurs as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['nom'] ?? '—') ?></td>
                <td><?= htmlspecialchars($user['email'] ?? '—') ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>
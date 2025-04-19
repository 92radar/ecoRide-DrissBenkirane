<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 'On');
ini_set('error_log', '/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/php-error.log');



if (isset($_SESSION['loggedin']) &&  $_SESSION['loggedin'] == true) {

    try {
        $pdo = new PDO("sqlite:/Users/macosdev/Documents/GitHub/ecoRide-DrissBenkirane/ecoride.db");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare("SELECT nom, prenom FROM utilisateurs WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $welcome = $stmt->fetchAll(PDO::FETCH_OBJ);
        if ($welcome) {
            $welcomeInfo = $welcome[0]; // Récupérer le premier élément
        }



        $success = "Bienvenue "  . $welcomeInfo->prenom  . " ,vous etes connecté";
    } catch (PDOException $e) {
        $error = "Erreur de connexion à la base de données : " . $e->getMessage();
    }
}

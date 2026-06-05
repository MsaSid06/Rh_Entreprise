<?php

$host = "localhost";
$db = "rh_entreprise";
$mdp = "lome2006";
$user = "root";
try {

    $pdo  = new PDO("mysql:host=$host;dbname=$db", $user, $mdp);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //activer les exept
} catch (Exception $e) {
    echo "Erreur : " .$e->getMessage();

}

// http://localhost/TP_BD/config/database.php


/**
 * git init
*    git add .
*    git commit -m "init"
*    git remote add origin URL //faire le lien ave le github
*    git push -u origin main // envoyer le dossier
 */

<?php
$host = "localhost";        // Adresse du serveur MySQL local (Wamp)
$dbname = "mondolxsai";     // Nom de la base de données locale
$username = "root";         // Utilisateur par défaut sur Wamp
$password = "root";             // Mot de passe vide par défaut sur Wamp

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Chemin de base en local
define('BASE_URL', '/MONDOLIAV2.2/');
?>

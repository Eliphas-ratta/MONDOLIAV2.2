<?php
$host = "mondolxsai.mysql.db";  // Adresse du serveur MySQL OVH
 $dbname = "mondolxsai";         // Nom de la base de données OVH
 $username = "mondolxsai";       // Nom d'utilisateur MySQL OVH
 $password = "Archaon1886";      // Mot de passe MySQL OVH

    // Mot de passe MySQL OVH

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}





define('BASE_URL', 'https://mondolia.ovh/');
// Mets le bon chemin de ton projet

?>

<?php
session_start();
session_unset();  // Supprime toutes les variables de session
session_destroy(); // Détruit la session

// Redirection vers la page d'accueil
header("Location: ../index.php");
exit();
?>

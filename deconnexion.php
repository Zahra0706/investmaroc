<?php
// Démarrage de la session
session_start();

// Supprimer toutes les variables de session
session_unset();

// Détruire la session
session_destroy();

// Rediriger l'utilisateur vers la page de connexion
header("Location: connexion.php");
exit;
?>

<?php
// Démarrez la session
session_start();

// Détruisez la session
session_destroy();

// Redirigez l'utilisateur vers la page d'accueil
header("Location: accueil.html");
exit();
?>
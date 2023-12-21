<?php
session_start();
	$idemploye = $_SESSION['idemploye'];
	$nom = $_SESSION['nom'];
	$prenom = $_SESSION['prenom'];
	$estChef = $_SESSION['estChef'];
	$estRH = $_SESSION['estRH'];
	$estDirecteurFinancier = $_SESSION['estDirecteurFinancier'];

echo '
	<!DOCTYPE html>
	<html lang="fr">
	<head>
    	<meta charset="UTF-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    	<title>GestionPersonnel</title>
    	<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body><div class="menu-container">
    	<h2>Menu GestionPersonnel</h2>';

		echo "Bienvenue $prenom $nom" ;
	echo '<div class="submenu-container">
    	<h3>Introduction de congé personnel</h3></div>' ;
	echo '<div class="submenu-container">
    	<h3>Introduction de notes de frais</h3></div>' ;
	if ($estChef==1)
	{
	echo '<div class="submenu-container">
    	<h3>Demandes à approuver(chef)</h3></div>' ; 
	}

 	if ($estChef==1)
	{
	echo '<div class="submenu-container">
    	<h3>Notes de frais à approuver(chef)</h3></div>' ; 
	}

	if ($estRH==1)
	{
	echo '<div class="submenu-container">
    	<h3>Menu RH</h3></div>' ;
	}

	if ($estDirecteurFinancier==1)
	{
	echo '<div class="submenu-container">
    	<h3>Validation finale de notes de frais(Direction Financière)</h3></div>' ;
	}

echo '	</div>
	</body>
	</html>' ;
?>

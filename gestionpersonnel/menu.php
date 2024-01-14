<?php
session_start();
$idemploye = $_SESSION['idemploye'];
$nom = $_SESSION['nom'];
$prenom = $_SESSION['prenom'];
$estChef = $_SESSION['estChef'];
$estRH = $_SESSION['estRH'];
$estDirecteurFinancier = $_SESSION['estDirecteurFinancier'];
$nomTypeEmploye = $_SESSION['nomTypeEmploye'];


echo '
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GestionPersonnel</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>';



echo '<div class="logo"></div>' ;
echo  '<div class="menu-container">';

echo '<div class="top-right" align="right">';
echo "Bienvenue $prenom $nom <br> $nomTypeEmploye <br>";
echo '<a href="deconnexion.php" class="logout-link">Déconnexion</a>';
echo '<br></div>' ;


echo        '<h2>Menu GestionPersonnel</h2>';




echo '<a href="demandeConge.php"><div class="submenu-container">
        <h3>Introduction de congé personnel</h3></div></a>' ;

echo '<a href="demandeNoteFrais.php"><div class="submenu-container">
        <h3>Introduction de notes de frais</h3></div></a>' ;

if ($estChef==1)
{
    echo '<a href="approuveConge.php"><div class="submenu-container">
        <h3>Demandes de congé à approuver(chef)</h3></div></a>' ; 
}

if ($estChef==1)
{
    echo '<a href="approuveNoteFrais.php"><div class="submenu-container">
        <h3>Notes de frais à approuver(chef)</h3></div></a>' ; 
}

if ($estChef==1)
{
    echo '<a href="calendrierService.php"><div class="submenu-container">
        <h3>Calendrier de mon service(chef)</h3></div></a>' ; 
}

if ($estRH==1)
{
    echo '<a href="menuRH.php"><div class="submenu-container">
        <h3>Menu RH</h3></div></a>' ;
}

if ($estDirecteurFinancier==1)
{
    echo '<a href="approuveNoteFraisDirFin.php"><div class="submenu-container">
        <h3>Validation finale de notes de frais(Direction Financière)</h3></div></a>' ;
}



echo '  </div>
</body>
</html>';
?>

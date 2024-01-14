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
// Ajout de la flèche de retour vers la page menu.php
echo '<div class="top-left" align="left">
        <a href="menu.php" class="back-link">↩ Retour au menu</a>
      </div>';
echo '<div class="top-right" align="right">';
echo "Bienvenue $prenom $nom <br> $nomTypeEmploye <br>";
echo '<a href="deconnexion.php" class="logout-link">Déconnexion</a>';
echo '<br></div>' ;


echo        '<h2>Menu RH</h2>';



if ($estRH==1)
{
echo '<a href="RHajoutEmploye.php"><div class="submenu-container">
        <h3>Création employé</h3></div></a>' ;

echo '<a href="RHmodifEmploye.php"><div class="submenu-container">
        <h3>Modification employé</h3></div></a>' ;

echo '<a href="RHajoutContrat.php"><div class="submenu-container">
        <h3>Création contrat employé</h3></div></a>' ;

echo '<a href="RHmodifContrat.php"><div class="submenu-container">
        <h3>Modification contrat employé</h3></div></a>' ;

echo '<a href="RHafficheSalaire.php"><div class="submenu-container">
        <h3>Impression fiche salaire</h3></div></a>' ;


}




echo '  </div>
</body>
</html>';
?>

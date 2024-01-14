<?php
session_start();
$idemploye = $_SESSION['idemploye'];
$nom = $_SESSION['nom'];
$prenom = $_SESSION['prenom'];
$estChef = $_SESSION['estChef'];
$estRH = $_SESSION['estRH'];
$estDirecteurFinancier = $_SESSION['estDirecteurFinancier'];
$nomTypeEmploye = $_SESSION['nomTypeEmploye'];

$base = new PDO('mysql:host=localhost;dbname=gestionpersonnel','root','');

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




echo  '<div class="menu-container">';
// Ajout de la flèche de retour vers la page menu.php
echo '<div class="top-left" align="left">
        <a href="menu.php" class="back-link">↩ Retour au menu</a>
      </div>';
echo '<div class="top-right" align="right">';
echo "Bienvenue $prenom $nom <br> $nomTypeEmploye <br>";
echo '<a href="deconnexion.php" class="logout-link">Déconnexion</a>';
echo '<br></div>' ;

echo '<div class="submenu-container">';
echo '<h2>Approbation Congé</h2>';

if ($estChef==1)
{
  echo '<h4>Afficher les demandes de congé</h4>';
  echo "<br> voila les employés qui ont fait une demande :<br><br>";

  $sql = "SELECT * from conge,employe WHERE conge.FKIdEmplye = employe.IdEmploye";
  $result = $base->query($sql);

  while($ligne = $result->fetch(PDO::FETCH_ASSOC))
  {
    $IdEmploye=$ligne['IdEmploye'];
    $IdConge=$ligne['IdConge'];
    $nom=$ligne['Nom'];
    $prenom=$ligne['Prenom'];
    $dateDebut=$ligne['DateDebutConge'];
    $dateFin=$ligne['DateFinConge'];
    echo "$IdEmploye- $IdConge - Nom : $nom, Prénom : $prenom, Debut: $dateDebut, Fin: $dateFin";

    echo '<form method="post" action="">';
            echo '<input type="submit" name="approuver" value="Approuver">';
            echo '<input type="submit" name="refuser" value="Refuser">';
            echo '</form>';

            if ($_SERVER["REQUEST_METHOD"] == "POST") 
            {
              // Vérifiez le bouton soumis
              if (isset($_POST["approuver"])) 
              {
                  $sql = 'UPDATE conge SET estApprouve = 1 WHERE IdEmploye = :IdEmploye';
                  $stmt = $base->prepare($sql);
                  $stmt->bindParam(':IdEmploye', $idEmploye, PDO::PARAM_INT);
                  $stmt->execute();
              } 
              elseif (isset($_POST["refuser"])) 
              {
                  $sql = 'UPDATE conge SET estApprouve = -1 WHERE IdEmploye = :IdEmploye';
                  $stmt = $base->prepare($sql);
                  $stmt->bindParam(':IdEmploye', $idEmploye, PDO::PARAM_INT);
                  $stmt->execute();
              } 
              else 
              {
                  $sql = 'UPDATE conge SET estApprouve = 0 WHERE IdEmploye = :IdEmploye';
                  $stmt = $base->prepare($sql);
                  $stmt->bindParam(':IdEmploye', $idEmploye, PDO::PARAM_INT);
                  $stmt->execute();
              }
          }
  }
}

echo '</div>' ;

echo '  </div>
</body>
</html>';
?>

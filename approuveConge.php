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

echo '<div class="submenu-container">';
echo '<h2>Approbation Congé</h2>';

if ($estChef == 1)
{
  echo '<h4>Voici les demandes de congé</h4>';
  echo "<br><br>";

  $query = 'SELECT c.*, e.Nom as NomEmploye, e.Prenom as PrenomEmploye, manager.Nom as NomManager, manager.Prenom as PrenomManager
          FROM conge c
          JOIN employe e ON c.FKIdEmploye = e.IdEmploye
          LEFT JOIN employe manager ON e.FKIdEmployeManager = manager.IdEmploye
          WHERE e.FKIdEmployeManager = ?
          AND c.FkIDEmployeApprouve IS NULL
          AND manager.IdEmploye IS NOT NULL';
  $result = $base->prepare($query);
  $result->bindParam(1, $idemploye, PDO::PARAM_INT);
  $result->execute();

  while($ligne = $result->fetch(PDO::FETCH_ASSOC))
  {
    echo '<form method="post" action="approuveConge.php">';
    $IdEmpl = $ligne['FKIdEmploye'];
    $IdConge = $ligne['IdConge'];
    $nom = $ligne['NomEmploye'];
    $prenom = $ligne['PrenomEmploye'];
    $dateDebut = $ligne['DateDebutConge'];
    $dateFin = $ligne['DateFinConge'];
    
    echo "iD employé : $IdEmpl - iD Congé : $IdConge <br> 
    Nom : $nom <br> Prénom : $prenom <br> 
    Debut: $dateDebut <br> Fin: $dateFin <br>";
    
    if ($ligne['estApprouve'] == 0) 
    {
      echo '<input type="submit" name="approuver" value="Approuver">';
      echo '<input type="submit" name="refuser" value="Refuser">';
    } 
    elseif ($ligne['estApprouve'] == 1) 
    {
      echo '<span style="color: green;">Demande approuvée</span>';
    } 
    elseif ($ligne['estApprouve'] == -1) 
    {
      echo '<span style="color: red;">Demande refusée</span>';
    }
    echo '</form>';
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") 
  {
    // On vérifie le bouton soumis
    if (isset($_POST["approuver"])) 
    {
      $sql = 'UPDATE conge SET FkIDEmployeApprouve = ?, estApprouve = 1 WHERE IdConge = ?';
      $stmt = $base->prepare($sql);
      $stmt->bindParam(1, $idemploye, PDO::PARAM_INT);
      $stmt->bindParam(2, $IdConge, PDO::PARAM_INT);
      $stmt->execute();
    } 
    elseif (isset($_POST["refuser"])) 
    {
      $sql = 'UPDATE conge SET FkIDEmployeApprouve = ?, estApprouve = -1 WHERE IdConge = ?';
      $stmt = $base->prepare($sql);
      $stmt->bindParam(1, $idemploye, PDO::PARAM_INT);
      $stmt->bindParam(2, $IdConge, PDO::PARAM_INT);
      $stmt->execute();
    } 
  }
}
else
{
  echo "Vous n'êtes pas habilité ! <br>";
}

echo '</div>' ;

echo '  </div>
</body>
</html>';
?>
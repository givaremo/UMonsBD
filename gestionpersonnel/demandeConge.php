<?php
session_start();
$idemploye = $_SESSION['idemploye'];
$nom = $_SESSION['nom'];
$prenom = $_SESSION['prenom'];
$estChef = $_SESSION['estChef'];
$estRH = $_SESSION['estRH'];
$estDirecteurFinancier = $_SESSION['estDirecteurFinancier'];
$nomTypeEmploye = $_SESSION['nomTypeEmploye'];
$estApprouve = 0;

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
echo '<div class="menu-container">';
// Ajout de la flèche de retour vers la page menu.php
echo '<div class="top-left" align="left">
        <a href="menu.php" class="back-link">↩ Retour au menu</a>
      </div>';
echo '<div class="top-right" align="right">';
echo "Bienvenue $prenom $nom <br> $nomTypeEmploye <br>";
echo '<a href="deconnexion.php" class="logout-link">Déconnexion</a>';
echo '<br></div>' ;

echo '<h2>Demande Congé</h2>';

echo '<form action="demandeConge.php" method="post">';
 echo '<div class="submenu-container">';

  echo '<div class="submenu-container">';
  echo "Date de début: <input type='date' name='DateDebutConge'><br>";
  echo '</div>' ;

  echo '<div class="submenu-container">';
  echo "Date de fin: <input type='date' name='DateFinConge'><br>";
  echo '</div>' ;

 echo '<div class="submenu-container">';
  echo '<label for="FKIdTypeConge">Choix du congé:</label>';
   echo '<select name="FKIdTypeConge" id="FKIdTypeConge">';
    echo '<option value="1"> Congé légal</option>';
    echo '<option value="2"> Congé récupération heure supplémentaires</option>';
    echo '<option value="3"> Congé force majeure</option>';
    echo '<option value="4"> Congé maternité</option>';
    echo '<option value="5"> Congé éducation</option>';
    echo '<option value="6"> Congé sans solde</option>';
   echo '</select>';
  echo '<br><br>';
  echo "<input type='submit' name='submitForm' value='submit'>";
echo '</div></form>' ;

// Vérifie si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
  if (isset($ligne['DateDebutConge'])) 
  {
    $dateDebut = $ligne['DateDebutConge'];
  } 
  else 
  {
    $dateDebut = $_POST["DateDebutConge"];
  }

  if (isset($ligne['DateFinConge'])) 
  {
    $dateFin = $ligne['DateFinConge'];
  } 
  else 
  {
    $dateFin = $_POST["DateFinConge"];
  }

  if (isset($ligne['FKIdTypeConge'])) 
  {
    $idTypeConge = $ligne['FKIdTypeConge'];
  }
  else 
  {
    $idTypeConge = $_POST["FKIdTypeConge"];
  }

  // Les valeurs de date sont renseignées avant d'effectuer l'insertion
  if (!empty($dateDebut) && !empty($dateFin)) 
  {
    $base = new PDO('mysql:host=localhost;dbname=gestionpersonnel', 'root', '');
    $sql = "INSERT INTO conge (DateDebutConge, DateFinConge, FKIdTypeConge, FKIdEmploye, estApprouve) VALUES (?, ?, ?, ?, ?)";
    $stmt = $base->prepare($sql);
    $stmt->execute([$dateDebut, $dateFin, $idTypeConge, $idemploye, $estApprouve]);    
  }
}

echo '<div class="submenu-container">';
$base = new PDO('mysql:host=localhost;dbname=gestionpersonnel', 'root', '');
$sql = "SELECT * from conge where FKIdEmploye=" . $idemploye;
$result = $base->query($sql);

while($ligne = $result->fetch(PDO::FETCH_ASSOC))
{
  echo '<form method="post" action="demandeConge.php">';
  $IdEmploye=$ligne['FkIDEmployeApprouve'];
  $IdConge=$ligne['IdConge'];
  $idTypeConge=$ligne['FKIdTypeConge'];
  $dateDebut=$ligne['DateDebutConge'];
  $dateFin=$ligne['DateFinConge'];
  $estApprouve=$ligne['estApprouve'];
  echo "iD Chef : $IdEmploye - iD Congé : $IdConge <br> Type de congé : $idTypeConge <br> Debut: $dateDebut <br> Fin: $dateFin <br>";

  echo '<h4>Approbation :</h4>';
  if ($ligne['estApprouve'] == 1) {
      echo '<span style="color: green;">Demande approuvée <br></span>';
  } elseif ($ligne['estApprouve'] == -1) {
      echo '<span style="color: red;">Demande refusée<br></span>';
  } else {
      echo 'En attente<br>';
  }
  echo '</form>';
}
echo '</div>' ;
  

echo '</div>
</body>
</html>';
?>


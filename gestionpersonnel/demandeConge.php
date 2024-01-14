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



echo '<div class="submenu-container">';
echo "Date de début: <input type='date' name='DateDebutConge'><br>";
echo '</div>' ;

echo '<div class="submenu-container">';
echo "Date de fin: <input type='date' name='DateFinConge'><br>";
echo '</div>' ;

echo '<div class="submenu-container">';
 echo '<form action="demandeConge.php" method="post">';
  echo '<label for="FKIdTypeConge">Choose congé:</label>';
   echo '<select name="FKIdTypeConge" id="FKIdTypeConge">';
    echo '<option value="1"> Congé légal</option>';
    echo '<option value="2"> Congé récupération heure supplémentaires</option>';
    echo '<option value="3"> Congé force majeure</option>';
    echo '<option value="5"> Congé éducation</option>';
    echo '<option value="6"> Congé sans solde</option>';
   echo '</select>';
  echo '<br><br>';
  echo "<input type='submit' name='FKIdTypeConge' value='submit'>";
echo '</form>
      </div>' ;

// Vérifie si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $dateDebut = $_POST["DateDebutConge"];
  $dateFin = $_POST["DateFinConge"];
  $idTypeConge = $_POST["FKIdTypeConge"];

  // Les valeurs de date sont renseignées avant d'effectuer l'insertion
  if (!empty($dateDebut) && !empty($dateFin)) {
      
      $base = new PDO('mysql:host=localhost;dbname=gestionpersonnel', 'root', '');
      $sql = "INSERT INTO conge (DateDebut, DateFin, FKIdTypeConge, FKIdEmploye, estApprouve) VALUES (?, ?, ?, ?, ?)";
      $stmt = $base->prepare($sql);
      $stmt->execute([$dateDebut, $dateFin, $idTypeConge, $idemploye, $estApprouve]);    
  }
}

echo '<div class="submenu-container">';
  echo '<h4>Est approuvé</h4>';
  if ($estApprouve == 1) {
      echo 'Oui<br>';
  } elseif ($estApprouve == -1) {
      echo 'Non<br>';
  } else {
      echo 'En attente<br>';
  }
echo '</div>' ;

echo '</div>
</body>
</html>';
?>


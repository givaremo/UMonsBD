<?php



session_start();
$idemploye = $_SESSION['idemploye'];
$nom = $_SESSION['nom'];
$prenom = $_SESSION['prenom'];
$estChef = $_SESSION['estChef'];
$estRH = $_SESSION['estRH'];
$estDirecteurFinancier = $_SESSION['estDirecteurFinancier'];
$nomTypeEmploye = $_SESSION['nomTypeEmploye'];

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mettre à jour la variable de session avec la nouvelle date
    $_SESSION["datecalendrier"] = $_POST["datecalendrier"];
}

// Définir une date par défaut si aucune date n'est sélectionnée
$datecal = isset($_SESSION["datecalendrier"]) ? $_SESSION["datecalendrier"] : date("Y-m-d");




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

	
echo        '<h2>Calendrier présence/absence du service</h2>';



echo '<div class="submenu-container">';

if ($estChef==1)
{

	// Connexion à la base de données
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "gestionpersonnel";

	$conn = new mysqli($servername, $username, $password, $dbname);

	// Vérifier la connexion
	if ($conn->connect_error) {
	    die("Échec de la connexion à la base de données : " . $conn->connect_error);
	}

	

	// Préparer et exécuter la procédure stockée
	$stmt = $conn->prepare("CALL usp_affichecalendrier(?, ?)");
	$stmt->bind_param("ss", $idemploye, $datecal);
	$stmt->execute();

	// Récupérer le résultat de la procédure stockée
	$result = $stmt->get_result();
	?>
		<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		    <label for="datecalendrier">Date :</label>
		    <input type="date" id="datecalendrier" name="datecalendrier" value="<?php echo $datecal; ?>" onchange="this.form.submit()">
		</form>
	<?php
	
		    echo '<div class="table-container">
			<table align="center">
			    <thead>
			        <tr>
			            <th>Nom</th>
			            <th>Prénom</th>
			            <th>Présence</th>
			        </tr>
			    </thead>
			    <tbody>
			';

		while ($row = $result->fetch_assoc()) 
		{  

				
		    
			
		    $nomemploye = $row['Nom'];
		    $prenomemploye = $row['Prenom'];
		    $presence = $row['presence'];
    			
		    echo "<tr><td> $nomemploye </td><td>$prenomemploye</td><td><img height=25px width=25px src="; 
		    if ($presence=='P')
			{
			   //présent
			   echo '"img/presence.png"' ;
			}
		    else if ($presence=='A')
			{
			   // absent
			   echo '"img/conge.png"' ;
			}
		    else if ($presence=='O')
			{
			  // 
			   echo '"img/ongoing.png"' ;
			}
		    else if ($presence=='R')
			{
		  	   echo '"img/presence.png"><img height=25px width=25px src="img/refuse.png"' ;
			}

  		    echo "></td>" ;

		} 
		
		echo '</div></tbody></table>';


	// Fermer la connexion à la base de données
	$conn->close();





}

echo '</div>' ;
echo '  </div>
</body>
</html>';
?>

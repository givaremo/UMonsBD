<?php
session_start();
$idemploye = $_SESSION['idemploye'];
$nom = $_SESSION['nom'];
$prenom = $_SESSION['prenom'];
$estChef = $_SESSION['estChef'];
$estRH = $_SESSION['estRH'];
$estDirecteurFinancier = $_SESSION['estDirecteurFinancier'];
$nomTypeEmploye = $_SESSION['nomTypeEmploye'];



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitContrat'])) {
    // Récupérer les données du formulaire
    $datedebutcontrat = $_POST['datedebutcontrat'];
    $datefincontrat = $_POST['datefincontrat'];
    $salaire = $_POST['salaire'];
    $fkidemploye = $_POST['fkidemploye'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "gestionpersonnel";
    
// Créer une connexion à la base de données
    $connexion = new mysqli($servername, $username, $password, $dbname);

    // Vérifier la connexion
    if ($connexion->connect_error) {
        die("La connexion à la base de données a échoué : " . $connexion->connect_error);
    }

// Exemple de requête MySQL utilisant la procédure stockée
$query = "CALL usp_ajoutContrat(?, ?,?, ?)";
$stmt = $connexion->prepare($query);

// Vérifier si la préparation de la requête a réussi
if ($stmt === FALSE) {
    die("Erreur lors de la préparation de la requête : " . $connexion->error);
}


// Binder les paramètres à la requête
$stmt->bind_param("ssdi", $datedebutcontrat, $datefincontrat, $salaire, $fkidemploye);

// Exécuter la requête
if ($stmt->execute()) {
	$resexecution = "Contrat ajouté avec succès";
} else {
	$resexecution = "Erreur lors de l'ajout de la note de frais : " . $stmt->error;
}

// Fermer la requête
$stmt->close();
}







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
        <a href="menuRH.php" class="back-link">↩ Retour au menu</a>
      </div>';
echo '<div class="top-right" align="right">';
echo "Bienvenue $prenom $nom <br> $nomTypeEmploye <br>";
echo '<a href="deconnexion.php" class="logout-link">Déconnexion</a>';
echo '<br></div>' ;


echo        '<h2>Ajout contrat</h2>';



if ($estRH==1)
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

	$sqlemploye = "call usp_selectEmploye";
    $resultemploye = $conn->query($sqlemploye);





echo '<div class="submenu-container">' ;


echo '<form method="post" action="">
	<table>
	
	<tr>
		<td>Employé</td>
		<td>
			<select type="text" name="fkidemploye" required>';
            // Génération des options de la liste déroulante
			if ($resultemploye !== false && $resultemploye->num_rows > 0) {
                while($row = $resultemploye->fetch_assoc()) {
                    echo "<option value='" . $row["idemploye"] . "'>" . $row["idemploye"] . "-" . $row["prenomemploye"] . " " . $row["nomemploye"] . " / " . $row["nomdepartement"] . "</option>";
                }
            } else {
                echo "<option value=''>Aucun élément trouvé</option>";
            }
			echo '</select>
		</td>
	</tr>
	
	<tr>
        <td>Salaire</td>
		<td><input type="floatval" name="salaire" required></td>
	</tr>	
	
	<tr>
        <td>Date début contrat</td>
		<td><input type="date" name="datedebutcontrat" required></td>
	</tr>
	<tr>
        <td>Date fin contrat</td>
		<td><input type="date" name="datefincontrat" required value="2099-12-31"></td>
	</tr>

	</table>
        <br>
	<input type="submit" name="submitContrat" value="Création contrat">
	<br><br>';

	if (isset($resexecution)) {
	    echo "<h4> $resexecution </h4>";
	}	
	echo '
      </form>';


echo  '</div>' ;
}




echo '  </div>
</body>
</html>';
?>

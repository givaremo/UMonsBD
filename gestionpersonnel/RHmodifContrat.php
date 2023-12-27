<?php
session_start();
$idemploye = $_SESSION['idemploye'];
$nom = $_SESSION['nom'];
$prenom = $_SESSION['prenom'];
$estChef = $_SESSION['estChef'];
$estRH = $_SESSION['estRH'];
$estDirecteurFinancier = $_SESSION['estDirecteurFinancier'];
$nomTypeEmploye = $_SESSION['nomTypeEmploye'];

if (isset($_GET['idcontrat'])) {
    $idcontrat = $_GET['idcontrat'];
} else {
    $idcontrat = null;
	$fkidemploye = null;
	$datedebutcontrat = null ;
	$datefincontrat = null ;
	$salaire = null;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitContrat'])) {
    // Récupérer les données du formulaire
    $datedebutcontrat = $_POST['datedebutcontrat'];
    $datefincontrat = $_POST['datefincontrat'];
    $salaire = $_POST['salaire'];
    $fkidemploye = $_POST['fkidemploye'];
	$idcontrat=$_POST['idcontrat'];
		

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
	$query = "CALL usp_modifContrat(?, ?, ?,?, ?)";
	$stmt = $connexion->prepare($query);

	// Vérifier si la préparation de la requête a réussi
	if ($stmt === FALSE) {
		die("Erreur lors de la préparation de la requête : " . $connexion->error);
	}


	// Binder les paramètres à la requête
	$stmt->bind_param("issdi",$idcontrat, $datedebutcontrat, $datefincontrat, $salaire, $fkidemploye);

	// Exécuter la requête
	if ($stmt->execute()) {
		$resexecution = "Contrat modifié avec succès";
	} else {
		$resexecution = "Erreur lors de la modification du contrat : " . $stmt->error;
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


echo        '<h2>Modification Contrat</h2>';



if ($estRH==1)
{

	// Connexion à la base de données
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "gestionpersonnel";

	$conn = new mysqli($servername, $username, $password, $dbname);
	$conn2 = new mysqli($servername, $username, $password, $dbname);
	$conn3 = new mysqli($servername, $username, $password, $dbname);
	

	$sqlcontrat = "call usp_selectContrat";
    $resultcontrat = $conn->query($sqlcontrat);

	$sqlemploye = "call usp_selectEmploye";
    $resultemploye = $conn2->query($sqlemploye);
	
	$sqlinfocontrat = "call usp_selectinfoContrat($idcontrat)";
    $resultinfocontrat = $conn3->query($sqlinfocontrat);
	
	

	if ($resultinfocontrat !== false && $resultinfocontrat->num_rows > 0) {
        while($row = $resultinfocontrat->fetch_assoc()) {
			$datedebutcontrat=$row["datedebut"];
			$datefincontrat=$row["datefin"];
			$salaire=$row["salaire"];
			$fkidemploye=$row["fkidemploye"];

        }
            
    }
}


echo '<div class="submenu-container">' ;


echo '<form method="post" action="">';
			echo '<select type="text" id="idcontrat" name="idcontrat" required>';
			if ($idcontrat==null){
			echo '<option value="" selected disabled hidden>Choisissez un contrat</option> ';
			}
			
            // Génération des options de la liste déroulante
			if ($resultcontrat !== false && $resultcontrat->num_rows > 0) {
                while($row = $resultcontrat->fetch_assoc()) {
					echo "<option value='" . $row["idcontrat"] . "'";
					if ($idcontrat==$row["idcontrat"]){
						echo " selected ";
					}
					echo ">" . $row["nomemploye"] . " " . $row["prenomemploye"] . " /#" . $row["idcontrat"] . "/" . $row["datedebut"] . " - " . $row["datefin"] ;
					echo "</option>";
                }
            } else {
                echo "<option value=''>Aucun élément trouvé</option>";
            }
			echo '</select>

	<table>
	<td>Employé</td>
	<td>	
	    <select type="text" name="fkidemploye" required>';
			if ($fkidemploye==null){
				echo '<option value="" selected disabled hidden>-</option> ';
			}
            // Génération des options de la liste déroulante
			if ($resultemploye !== false && $resultemploye->num_rows > 0) {
            //if ($resultemploye->num_rows > 0) {
                while($row = $resultemploye->fetch_assoc()) {
                    echo "<option value='" . $row["idemploye"] . "'";
					if ($fkidemploye==$row["idemploye"]){
						echo " selected ";
					}
					echo ">" . $row["idemploye"] . "-" . $row["nomemploye"] . " " . $row["prenomemploye"] . " - " . $row["nomdepartement"] . "</option>";
                }
            } else {
                echo "<option value=''>Aucun élément trouvé</option>";
            }
	    echo '</select>
	</td>

	</tr>
	<tr>
    <td>Salaire</td>
	<td><input type="floatval" name="salaire" required value="' . $salaire . '"></td>
	</tr>
	<tr>
        <td>Date début contrat</td>
	<td><input type="date" name="datedebutcontrat" required value="' . $datedebutcontrat . '"></td>
	</tr>
	<tr>
        <td>Date fin contrat</td>
	<td><input type="date" name="datefincontrat" required value="' . $datefincontrat . '"></td>
	</tr>


	</table>
        <br>
	<input type="submit" name="submitContrat" value="Modification contrat">
	<br><br>';

	if (isset($resexecution)) {
	    echo "<h4> $resexecution </h4>";
	}	
	echo '
      </form>';


echo  '</div>' ;


echo '  </div>';


echo "
	<script>
		document.getElementById('idcontrat').addEventListener('input', function() {
			// Récupérer la valeur sélectionnée
			var selectedValue = this.value;

			// Mettre à jour l'URL avec la nouvelle valeur
			var currentUrl = window.location.href;
			var updatedUrl;

			
			if (currentUrl.includes('idcontrat=')) {
				// Remplacer la valeur existante
				updatedUrl = currentUrl.replace(/idcontrat=([^&]*)/, 'idcontrat=' + selectedValue);
			} else {
				// Ajouter la valeur à l'URL
				updatedUrl = currentUrl + (currentUrl.includes('?') ? '&' : '?') + 'idcontrat=' + selectedValue;
			}

			// Rediriger vers la nouvelle URL
			window.location.href = updatedUrl;
		});
	</script>";

echo'
</body>
</html>';

?>

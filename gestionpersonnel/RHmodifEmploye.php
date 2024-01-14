<?php

session_start();
$idemploye = $_SESSION['idemploye'];
$nom = $_SESSION['nom'];
$prenom = $_SESSION['prenom'];
$estChef = $_SESSION['estChef'];
$estRH = $_SESSION['estRH'];
$estDirecteurFinancier = $_SESSION['estDirecteurFinancier'];
$nomTypeEmploye = $_SESSION['nomTypeEmploye'];

if (isset($_GET['idemp'])) {
    $idemp = $_GET['idemp'];
} else {
    $idemp = null;
	$formfkiddepartement=null;
	$formfkidadresse=null;
	$formfkidtypeemploye=null;
	$formfkidemployemanager=null;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitEmploye'])) {
    // Récupérer les données du formulaire
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $telephone = $_POST['telephone'];
    $mail = $_POST['mail'];
    $motdepasse = $_POST['motdepasse'];
    $iban = $_POST['iban'];
    $niss = $_POST['niss'];
    $departement = $_POST['departement'];
    $adresse = $_POST['adresse'];
    $typeemploye = $_POST['typeemploye'];
    $manager = $_POST['manager'];
	$idemp=$_POST['idemp'];
		

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
	$query = "CALL usp_modifEmploye(?, ?, ?,?, ?,?, ?,?, ?,?, ?,?)";
	$stmt = $connexion->prepare($query);

	// Vérifier si la préparation de la requête a réussi
	if ($stmt === FALSE) {
		die("Erreur lors de la préparation de la requête : " . $connexion->error);
	}


	// Binder les paramètres à la requête
	$stmt->bind_param("isssssssiiii",$idemp, $nom, $prenom, $telephone, $mail, $motdepasse, $iban, $niss, $departement, $adresse, $typeemploye, $manager);

	// Exécuter la requête
	if ($stmt->execute()) {
		$resexecution = "Employé modifié avec succès";
	} else {
		$resexecution = "Erreur lors de la modification de l'employé : " . $stmt->error;
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



echo '<div class="logo"></div>' ;
echo  '<div class="menu-container">';
// Ajout de la flèche de retour vers la page menu.php
echo '<div class="top-left" align="left">
        <a href="menuRH.php" class="back-link">↩ Retour au menu</a>
      </div>';
echo '<div class="top-right" align="right">';
echo "Bienvenue $prenom $nom <br> $nomTypeEmploye <br>";
echo '<a href="deconnexion.php" class="logout-link">Déconnexion</a>';
echo '<br></div>' ;




echo        '<h2>Modification employé</h2>';



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
	$conn4 = new mysqli($servername, $username, $password, $dbname);
	$conn5 = new mysqli($servername, $username, $password, $dbname);
	$conn6 = new mysqli($servername, $username, $password, $dbname);

	// Vérifier la connexion
	if ($conn->connect_error) {
	    die("Échec de la connexion à la base de données : " . $conn->connect_error);
	}

	$sqldepartement = "call usp_selectDepartement";
    $resultdepartement = $conn->query($sqldepartement);

	$sqladresse = "call usp_selectAdresse";
    $resultadresse = $conn2->query($sqladresse);

	$sqltypeemploye = "call usp_selectTypeEmploye";
    $resulttypeemploye = $conn3->query($sqltypeemploye);

	$sqlmanager = "call usp_selectManager";
    $resultmanager = $conn4->query($sqlmanager);

	$sqlemploye = "call usp_selectEmploye";
    $resultemploye = $conn5->query($sqlemploye);
	
	$sqlinfoemploye = "call usp_selectinfoEmploye($idemp)";
	$resultinfoemploye = $conn6->query($sqlinfoemploye);

	if ($resultinfoemploye !== false && $resultinfoemploye->num_rows > 0) {
        while($row = $resultinfoemploye->fetch_assoc()) {
			$nomform=$row["nom"];
			$prenomform=$row["prenom"];
			$formnumerotelephone=$row["numerotelephone"];
			$formadressemail=$row["adressemail"];
			$formpassword=$row["password"];
			$formiban=$row["iban"];
			$formnumeroregistrenational=$row["numeroregistrenational"];
			$formfkiddepartement=$row["fkiddepartement"];
			$formfkidadresse=$row["fkidadresse"];
			$formfkidtypeemploye=$row["fkidtypeemploye"];
			$formfkidemployemanager=$row["fkidemployemanager"];

        }
            
    }

echo '<div class="submenu-container">' ;


echo '<form method="post" action="">';
			echo '<select type="text" id="idemp" name="idemp" required>';
			if ($idemp==null){
			echo '<option value="" selected disabled hidden>Choisissez un employé</option> ';
			}
			
            // Génération des options de la liste déroulante
			if ($resultemploye !== false && $resultemploye->num_rows > 0) {
                while($row = $resultemploye->fetch_assoc()) {
					echo "<option value='" . $row["idemploye"] . "'";
					if ($idemp==$row["idemploye"]){
						echo " selected ";
					}
					echo ">" . $row["idemploye"] . "-" . $row["prenomemploye"] . " " . $row["nomemploye"] . " / " . $row["nomdepartement"] ;

					echo "</option>";
						
                    
                }
            } else {
                echo "<option value=''>Aucun élément trouvé</option>";
            }
			echo '</select>

	<table>
	<tr>
        <td>Nom</td>
	<td><input type="text" name="nom" required value="' . $nomform . '"></td>
	</tr>
	<tr>
        <td>Prénom</td>
	<td><input type="text" name="prenom" required value="' . $prenomform . '"></td>
	</tr>
	<tr>
        <td>Numéro téléphone</td>
	<td><input type="text" name="telephone" required value="' . $formnumerotelephone . '"></td>
	</tr>
        <td>Adresse Mail</td>
	<td><input type="email" name="mail" required value="' . $formadressemail . '"></td>
	</tr>
        <td>Mot de passe</td>
	<td><input type="password" name="motdepasse" required value="' . $formpassword . '"></td>
	</tr>
        <td>Compte bancaire IBAN</td>
	<td><input type="text" name="iban" required value="' . $formiban . '"></td>
	</tr>
        <td>Numéro NISS</td>
	<td><input type="text" name="niss" required value="' . $formnumeroregistrenational . '"></td>
	</tr>
        <td>Département</td>
	<td>
	    <select type="text" name="departement" required>';
			if ($formfkiddepartement==null){
				echo '<option value="" selected disabled hidden>-</option> ';
			}
            // Génération des options de la liste déroulante
			if ($resultdepartement !== false && $resultdepartement->num_rows > 0) {
            //if ($resultdepartement->num_rows > 0) {
                while($row = $resultdepartement->fetch_assoc()) {
                    echo "<option value='" . $row["iddepartement"] . "'";
					if ($formfkiddepartement==$row["iddepartement"]){
						echo " selected ";
					}
					echo ">" . $row["nom"] . "</option>";
                }
            } else {
                echo "<option value=''>Aucun élément trouvé</option>";
            }
	    echo '</select>
	</td>
	</tr>
	<tr>
        <td>Adresse</td>
       	<td>
	    <select type="text" name="adresse" required>';
			if ($formfkidadresse==null){
				echo '<option value="" selected disabled hidden>-</option> ';
			}
            // Génération des options de la liste déroulante
			if ($resultadresse !== false && $resultadresse->num_rows > 0) {
            //if ($resultadresse->num_rows > 0) {
                while($row = $resultadresse->fetch_assoc()) {
                    echo "<option value='" . $row["idadresse"] . "'";
					if ($formfkidadresse==$row["idadresse"]){
						echo " selected ";
					}
					echo ">" . $row["rue"] . ", " . $row["numerorue"] . " " . $row["boite"] . " " . $row["codepostal"] . " " . $row["nomville"] . "</option>";
                }
            } else {
                echo "<option value=''>Aucun élément trouvé</option>";
            }
	    echo '</select>
	</td>
	</tr>
	<tr>
        <td>Type employé</td>
       	<td>
	    <select type="text" name="typeemploye" required>';
			if ($formfkidtypeemploye==null){
				echo '<option value="" selected disabled hidden>-</option> ';
			}
            // Génération des options de la liste déroulante
			if ($resulttypeemploye !== false && $resulttypeemploye->num_rows > 0) {
            //if ($resulttypeemploye->num_rows > 0) {
                while($row = $resulttypeemploye->fetch_assoc()) {
                    echo "<option value='" . $row["idtypeemploye"] . "'";
					if ($formfkidtypeemploye==$row["idtypeemploye"]){
						echo " selected ";
					}
					echo ">" . $row["nomtypeemploye"] . "</option>";
                }
            } else {
                echo "<option value=''>Aucun élément trouvé</option>";
            }
	    echo '</select>
	</td>
	</tr>
        <td>Manager</td>
	<td>	
	    <select type="text" name="manager" required>';
			if ($formfkidemployemanager==null){
				echo '<option value="" selected disabled hidden>-</option> ';
			}
            // Génération des options de la liste déroulante
			if ($resultmanager !== false && $resultmanager->num_rows > 0) {
            //if ($resultmanager->num_rows > 0) {
                while($row = $resultmanager->fetch_assoc()) {
                    echo "<option value='" . $row["idmanager"] . "'";
					if ($formfkidemployemanager==$row["idmanager"]){
						echo " selected ";
					}
					echo ">" . $row["nommanager"] . " " . $row["prenommanager"] . " - " . $row["typeemployemanager"] . " - " . $row["nomdepartement"] . "</option>";
                }
            } else {
                echo "<option value=''>Aucun élément trouvé</option>";
            }
	    echo '</select>
	</td>

	</tr>
        <!-- Ajoutez d\'autres champs du formulaire ici -->
	</table>
        <br>
	<input type="submit" name="submitEmploye" value="Modification employé">
	<br><br>';

	if (isset($resexecution)) {
	    echo "<h4> $resexecution </h4>";
	}	
	echo '
      </form>';


echo  '</div>' ;
}




echo '  </div>';


echo "
	<script>
		document.getElementById('idemp').addEventListener('input', function() {
			// Récupérer la valeur sélectionnée
			var selectedValue = this.value;

			// Mettre à jour l'URL avec la nouvelle valeur
			var currentUrl = window.location.href;
			var updatedUrl;

			// Vérifier si l'URL contient déjà une valeur pour idemp
			if (currentUrl.includes('idemp=')) {
				// Remplacer la valeur existante
				updatedUrl = currentUrl.replace(/idemp=([^&]*)/, 'idemp=' + selectedValue);
			} else {
				// Ajouter la valeur à l'URL
				updatedUrl = currentUrl + (currentUrl.includes('?') ? '&' : '?') + 'idemp=' + selectedValue;
			}

			// Rediriger vers la nouvelle URL
			window.location.href = updatedUrl;
		});
	</script>";

echo'
</body>
</html>';

?>

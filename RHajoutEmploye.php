<?php
session_start();
$idemploye = $_SESSION['idemploye'];
$nom = $_SESSION['nom'];
$prenom = $_SESSION['prenom'];
$estChef = $_SESSION['estChef'];
$estRH = $_SESSION['estRH'];
$estDirecteurFinancier = $_SESSION['estDirecteurFinancier'];
$nomTypeEmploye = $_SESSION['nomTypeEmploye'];



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitEmploye'])) {
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
$query = "CALL usp_ajoutEmploye(?, ?,?, ?,?, ?,?, ?,?, ?,?)";
$stmt = $connexion->prepare($query);

// Vérifier si la préparation de la requête a réussi
if ($stmt === FALSE) {
    die("Erreur lors de la préparation de la requête : " . $connexion->error);
}


// Binder les paramètres à la requête
$stmt->bind_param("sssssssiiii", $nom, $prenom, $telephone, $mail, $motdepasse, $iban, $niss, $departement, $adresse, $typeemploye, $manager);

// Exécuter la requête
if ($stmt->execute()) {
	$resexecution = "Employé ajouté avec succès";
} else {
	$resexecution = "Erreur lors de l'ajout de l'employé : " . $stmt->error;
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


echo        '<h2>Ajout employé</h2>';



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




echo '<div class="submenu-container">' ;


echo '<form method="post" action="">
	<table>
	<tr>
        <td>Nom</td>
	<td><input type="text" name="nom" required></td>
	</tr>
	<tr>
        <td>Prénom</td>
	<td><input type="text" name="prenom" required></td>
	</tr>
	<tr>
        <td>Numéro téléphone</td>
	<td><input type="text" name="telephone" required></td>
	</tr>
        <td>Adresse Mail</td>
	<td><input type="email" name="mail" required></td>
	</tr>
        <td>Mot de passe</td>
	<td><input type="password" name="motdepasse" required></td>
	</tr>
        <td>Compte bancaire IBAN</td>
	<td><input type="text" name="iban" required></td>
	</tr>
        <td>Numéro NISS</td>
	<td><input type="text" name="niss" required></td>
	</tr>
        <td>Département</td>
	<td>
	    <select type="text" name="departement" required>';
            // Génération des options de la liste déroulante
			if ($resultdepartement !== false && $resultdepartement->num_rows > 0) {
            //if ($resultdepartement->num_rows > 0) {
                while($row = $resultdepartement->fetch_assoc()) {
                    echo "<option value='" . $row["iddepartement"] . "'>" . $row["nom"] . "</option>";
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
            // Génération des options de la liste déroulante
			if ($resultadresse !== false && $resultadresse->num_rows > 0) {
            //if ($resultadresse->num_rows > 0) {
                while($row = $resultadresse->fetch_assoc()) {
                    echo "<option value='" . $row["idadresse"] . "'>" . $row["rue"] . ", " . $row["numerorue"] . " " . $row["boite"] . " " . $row["codepostal"] . " " . $row["nomville"] . "</option>";
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
            // Génération des options de la liste déroulante
			if ($resulttypeemploye !== false && $resulttypeemploye->num_rows > 0) {
            //if ($resulttypeemploye->num_rows > 0) {
                while($row = $resulttypeemploye->fetch_assoc()) {
                    echo "<option value='" . $row["idtypeemploye"] . "'>" . $row["nomtypeemploye"] . "</option>";
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
            // Génération des options de la liste déroulante
			if ($resultmanager !== false && $resultmanager->num_rows > 0) {
            //if ($resultmanager->num_rows > 0) {
                while($row = $resultmanager->fetch_assoc()) {
                    echo "<option value='" . $row["idmanager"] . "'>" . $row["nommanager"] . " " . $row["prenommanager"] . " - " . $row["typeemployemanager"] . " - " . $row["nomdepartement"] . "</option>";
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
	<input type="submit" name="submitEmploye" value="Création employé">
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

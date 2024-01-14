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


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitSalaire'])) {

	$idemp=$_POST['idemp'];
	$datefichepaie=$_POST['datefichepaie'];
	$datefichepaie = $datefichepaie . "-01";
	$currentDate = date('Y-m');

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
	$query = "call usp_CalculeFichePaie(?, ?)";
	$stmt = $connexion->prepare($query);

	// Vérifier si la préparation de la requête a réussi
	if ($stmt === FALSE) {
		die("Erreur lors de la préparation de la requête : " . $connexion->error);
	}


	// Binder les paramètres à la requête
	$stmt->bind_param("si",$datefichepaie, $idemp);

	// Exécuter la requête
	if ($stmt->execute()) {
		$resexecution = "Fiche de paie générée pour l'employé " . $idemp . " période : " . $datefichepaie;
	} else {
		$resexecution = "Erreur lors de la génération de la fiche de paie : " . $stmt->error;
	}

	// Fermer la requête
	$stmt->close();
	
	
	
	
	
	
	
	
	
	
	
	
	require_once('./fpdf186/fpdf.php');

	class mypdf extends FPDF{
		public $idemp;
		function customizePage() {
			
		   $this->SetFont('Arial', 'B',16);
		   $this->Cell(40,10,"Fiche de salaire employé numéro " . $this->idemp);
		   
		}
		public function setIdemp($value) {
			$this->idemp = $value;
		}
	}
	

	$pdf = new mypdf();
	$pdf->setIdemp($idemp);
	$pdf->AddPage(); 
	
	$pdf->customizePage();
	$pdf->Output(); 
	

	
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




echo        '<h2>Création fiche de paie</h2>';



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


echo '<form method="post" action="" target="_blank">';
			echo 'Employé : <select type="text" id="idemp" name="idemp" required>';
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

		<br><br>
		<label for="P">Mois : </label>
		<input type="month" id="datefichepaie" name="datefichepaie" required min="2018-03" max="' . $currentDate . '" value="' . $currentDate . '"/>

    <br><br>
	<input type="submit" name="submitSalaire" value="Génération Fiche de paie employé">
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

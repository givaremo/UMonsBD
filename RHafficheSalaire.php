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
	$genererfiche = 0 ;

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
		
		
		    // Récupérer les valeurs des champs
			$stmt->store_result(); // Stocker les résultats pour pouvoir utiliser fetch
			$stmt->bind_result($salaire, $datedebut, $datefin, $jourssanssolde, $notefrais, $prorata, $Nom, $Prenom, $IBAN);

			// Fetch les résultats
			if ($stmt->fetch()) {
				$genererfiche = 1 ;
				if($datedebut==NULL){
					$genererfiche=0;
				}
				if($jourssanssolde==NULL){
					$jourssanssolde=0;
				}
				if($notefrais==NULL){
					$notefrais=0;
				}
				
			} else {
				$genererfiche = 0 ;
				echo "Aucun résultat trouvé.";
			}
		
		
	} else {
		$resexecution = "Erreur lors de la génération de la fiche de paie : " . $stmt->error;
	}

	
	
	
	
	
	
	
	
	
	
	
	
	
	require_once('./fpdf186/fpdf.php');

	class mypdf extends FPDF{
		public $idemp;
		public $salaire;
		public $datedebut;
		public $datefin;
		public $jourssanssolde;
		public $notefrais;
		public $prorata;
		public $Nom;
		public $Prenom;
		public $IBAN;
		public $genererfiche;
		
		function customizePage() {
		$image1 = "img/logouniv.jpg";
		$this->Cell( 40, 40, $this->Image($image1, $this->GetX(), $this->GetY(), 33.78), 0, 0, 'R', false );
		$this->Ln(20);
		if ($this->genererfiche==1){ 
		   $this->SetFont('Arial', 'B',16);
		   $this->Cell(0,0,utf8_decode("Fiche de salaire "),0,0,'C');
		   
		   $this->Ln(20);
		   
		   $this->SetFont('Arial', 'I',8);
		   $this->Cell(0,0,utf8_decode("Nom : " . $this->Nom),0,0,'L');
		   $this->Ln(5);
		   $this->SetFont('Arial', 'I',8);
		   $this->Cell(0,0,utf8_decode("Prénom : " . $this->Prenom),0,0,'L');
		   $this->Ln(5);
		   $this->SetFont('Arial', 'I',8);
		   $this->Cell(0,0,utf8_decode("Date référence salaire : " . $this->datedebut . " - " . $this->datefin),0,0,'L');
		   
		   $this->Ln(10);
		   $this->SetFont('Arial','',10);
		   $this->Cell(0,0,utf8_decode("Salaire de base : " . $this->salaire . "EUR" ),0,0,'R');
		   
		   $this->Ln(5);
		   $this->SetFont('Arial','',10);
		   $this->Cell(0,0,utf8_decode("Notes de frais : +" . $this->notefrais . "EUR" ),0,0,'R');
		   
		   $this->Ln(5);
		   $this->SetFont('Arial','',10);
		   $this->Cell(0,0,utf8_decode("Jours congé sans solde : " . $this->jourssanssolde . " " ),0,0,'R');
		   
		   $this->Ln(5);
		   $this->SetFont('Arial','',10);
		   $this->Cell(0,0,utf8_decode("Prorata salaire/jours comptabilisés : " . $this->prorata * 100 . "%" ),0,0,'R');
		   
		   $this->Ln(5);
		   $this->SetFont('Arial','',10);
		   $this->Cell(0,0,utf8_decode("Salaire rectifié au prorata : " . round($this->salaire * $this->prorata,2) . "EUR" ),0,0,'R');
		   
		   $this->Ln(5);
		   $this->SetFont('Arial','B',12);
		   $this->Cell(0,0,utf8_decode("Total salaire : " . round($this->salaire * $this->prorata + $this->notefrais,2) . "EUR" ),0,0,'R');
		   
		   $this->Ln(15);
		   $this->SetFont('Arial','I',8);
		   $this->Cell(0,0,utf8_decode("Le salaire sera versé lors du premier jour ouvrable suivnat la période de référencce sur le compte IBAN : " . $this->IBAN),0,0,'L');
		   
		}
		else{
			$this->Ln(20);
		   $this->SetFont('Arial', 'B',16);
		   $this->Cell(0,0,utf8_decode("Pas de fiche de salaire pour cette période et cet employé"),0,0,'C');
		}
		   
		}
		public function setvars($value, $value2, $value3, $value4, $value5, $value6, $value7 , $value8, $value9, $value10, $value11) {
			$this->idemp = $value;
			$this->salaire = $value2;
			$this->datedebut = $value3;
			$this->datefin = $value4;
			$this->jourssanssolde = $value5;
			$this->notefrais = $value6;
			$this->prorata = $value7;
			$this->Nom = $value8;
			$this->Prenom = $value9;
			$this->IBAN = $value10;
			$this->genererfiche = $value11;
		}
	}
	

	$pdf = new mypdf();
	$pdf->setvars($idemp, $salaire, $datedebut, $datefin, $jourssanssolde, $notefrais, $prorata, $Nom, $Prenom, $IBAN, $genererfiche);
	$pdf->AddPage(); 
	
	$pdf->customizePage();
	$pdf->Output(); 
	
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

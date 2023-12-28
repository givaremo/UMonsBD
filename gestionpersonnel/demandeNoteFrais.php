<?php
session_start();
$idemploye = $_SESSION['idemploye'];
$nom = $_SESSION['nom'];
$prenom = $_SESSION['prenom'];
$estChef = $_SESSION['estChef'];
$estRH = $_SESSION['estRH'];
$estDirecteurFinancier = $_SESSION['estDirecteurFinancier'];
$nomTypeEmploye = $_SESSION['nomTypeEmploye'];
$idmanager = $_SESSION['idmanager'];

// Etablir la connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestionpersonnel";
$connexion = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($connexion->connect_error) {
    die("La connexion à la base de données a échoué : " . $connexion->connect_error);
}

// Traitement du formulaire de note de frais
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitNoteFrais'])) {
    // Récupérer les données du formulaire
    $montant = $_POST['montant'];
    $dateDuFrais = $_POST['date_du_frais'];
    $motif = $_POST['motif'];

    // Assurez-vous de valider et de sécuriser les données avant de les insérer dans la base de données
    // Utilisez des requêtes préparées pour éviter les attaques par injection SQL

    // Exemple de requête MySQL utilisant la procédure stockée
    $query = "CALL usp_ajoutNotesdeFrais(?, ?, ?, ?, ?, ?, ?, ? , ?)";
    $stmt = $connexion->prepare($query);

    // Vérifier si la préparation de la requête a réussi
    if ($stmt === FALSE) {
        die("Erreur lors de la préparation de la requête : " . $connexion->error);
    }

    // Initialiser les champs d'approbation à NULL
    $FKIdEmploye = $idemploye;
	if ($estDirecteurFinancier==1){
		$fkemplaprouv=$idemploye;
		$fkdirfinaprouv=$idemploye;
		$appchef=1;
		$appfin =1;
	}
	else if($idmanager==null)
	{
    $fkemplaprouv = $idemploye;
    $fkdirfinaprouv = null;
	$appchef=1;
	$appfin = null ;
	}
	else{
    $fkemplaprouv = null;
    $fkdirfinaprouv = null;
	$appchef= null ;
	$appfin = null ;		
	}

    // Binder les paramètres à la requête
    $stmt->bind_param("ssdssssss", $dateDuFrais, $dateDuFrais, $montant, $motif, $FKIdEmploye, $fkemplaprouv, $fkdirfinaprouv, $appchef, $appfin);

	// Exécuter la requête
	if ($stmt->execute()) {
		$resexecution = "Note de frais ajoutée avec succès";
	} else {
		$resexecution = "Erreur lors de l'ajout de la note de frais : " . $stmt->error;
	}

    // Fermer la requête
    $stmt->close();
}

// Affichez le formulaire pour la demande de notes de frais
echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande de Notes de Frais</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>';

echo '<div class="menu-container">';
echo '<div class="top-left" align="left">
        <a href="menu.php" class="back-link">↩ Retour au menu</a>
      </div>';
echo '<div class="top-right" align="right">';
echo "Bienvenue $prenom $nom <br> $nomTypeEmploye <br>";
echo '<a href="deconnexion.php" class="logout-link">Déconnexion</a>';
echo '<br></div>';

// Affichage du formulaire de demande de notes de frais
echo '<h2>Demande de Notes de Frais</h2>';
echo '<form method="post" action="">
	<table>
	<tr>
        <td>Montant</td>
	<td><input type="text" name="montant" required></td>
	</tr>
	<tr>
        <td>Date du frais</td>
	<td><input type="date" name="date_du_frais" required></td>
	</tr>
	<tr>
        <td>Motif</td>
	<td><input type="text" name="motif" required></td>
	</tr>
        <!-- Ajoutez d\'autres champs du formulaire ici -->
	</table>
        <br>
	<input type="submit" name="submitNoteFrais" value="Introduire une note de frais">
	<br><br>';

	if (isset($resexecution)) {
	    echo "<h4> $resexecution </h4>";
	}	
	echo '
      </form>';
// Affichage de l'inventaire des demandes de notes de frais
echo '<h2>Inventaire des Demandes de Notes de Frais</h2>';

// Récupérer les demandes de notes de frais
$queryInventaire = 'SELECT * FROM notedefrais WHERE FKIdEmploye = ?';
$stmtInventaire = $connexion->prepare($queryInventaire);

if ($stmtInventaire) {
    $stmtInventaire->bind_param("i", $idemploye);
    $stmtInventaire->execute();
    $resultInventaire = $stmtInventaire->get_result();

		    echo '<div class="table-container">
			<table align="center">
			    <thead>
			        <tr>
			            <th>Date note de frais</th>
						<th>Date demande</th>
			            <th>Montant</th>
			            <th>Motif</th>
						<th>Statut</th>
			        </tr>
			    </thead>
			    <tbody>
			';
while ($rowInventaire = $resultInventaire->fetch_assoc()) {
    echo '<tr>' ;
    echo '<td>'  . $rowInventaire['DateNoteDeFrais'] . '</td>';
    echo '<td>' . $rowInventaire['DateDemande'] . '</td>';
	echo '<td>' . $rowInventaire['Montant'] . '</td>';
	echo '<td>' . $rowInventaire['Motif'] . '</td>';

	echo '<td>';
    // Affichage de l'état
    if ($rowInventaire['estApprouveChef'] === null && $rowInventaire['estApprouveDirFin'] === null) {
        echo '<img src="img/ongoing.png" height=20px width=20px />Notes de frais en attente'; 
	} elseif ($rowInventaire['estApprouveChef'] == 0 ) {
        echo '<img src="img/refuse.png" height=20px width=20px />Note de frais refusée par le chef de service';
    } elseif ($rowInventaire['estApprouveChef'] == 1 && $rowInventaire['estApprouveDirFin'] === null) {
        echo '<img src="img/ongoing.png" height=20px width=20px />Note de frais approuvée par le chef de service et en attente du directeur financier';
    } elseif ($rowInventaire['estApprouveChef'] == 1 && $rowInventaire['estApprouveDirFin'] == 0) {
        echo '<img src="img/refuse.png" height=20px width=20px />Notes de frais approuvée par le chef de service et refusée par le directeur financier';
    } elseif ($rowInventaire['estApprouveChef'] == 1 && $rowInventaire['estApprouveDirFin'] == 1) {
        echo '<img src="img/accepte.png" height=20px width=20px />Notes de frais approuvée par le chef de service et par le directeur financier';
    }
	echo '</td>';
	echo '</tr>';
    
}

    $stmtInventaire->close();
}

echo '</div></tbody></table>';
echo '</div>';

echo '</body>';
echo '</html>';
?>
<?php
session_start();

$idemploye = $_SESSION['idemploye'];
$nom = $_SESSION['nom'];
$prenom = $_SESSION['prenom'];
$estChef = $_SESSION['estChef'];
$estRH = $_SESSION['estRH'];
$estDirecteurFinancier = $_SESSION['estDirecteurFinancier'];
$nomTypeEmploye = $_SESSION['nomTypeEmploye'];

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



// Gestion de l'approbation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Récupérer l'ID de la note de frais à approuver
	$idNoteDeFrais = $_POST['idNoteDeFrais'];
	$estApprouve = $_POST['estApprouve'];

	// Mettre à jour la colonne FKIdEmployeManagerApprouve avec l'ID du chef
	$queryUpdate = "UPDATE notedefrais SET FKIdEmployeManagerApprouve = ?, estApprouveChef = ? WHERE IdNoteDeFrais = ?";
	$stmtUpdate = $connexion->prepare($queryUpdate);
	$stmtUpdate->bind_param("iii", $idemploye, $estApprouve, $idNoteDeFrais);
	$stmtUpdate->execute();

	$stmtUpdate->close();

	// Message de confirmation
	$_SESSION['message'] = "Note de frais mise à jour avec succès.";
}




// Récupérer les notes de frais
$query = 'SELECT nf.*, e.Nom as NomEmploye, e.Prenom as PrenomEmploye, manager.Nom as NomManager, manager.Prenom as PrenomManager
          FROM notedefrais nf
          JOIN employe e ON nf.FKIdEmploye = e.IdEmploye
          LEFT JOIN employe manager ON e.FKIdEmployeManager = manager.IdEmploye
          WHERE e.FKIdEmployeManager = ?
          AND nf.FKIdEmployeManagerApprouve IS NULL
          AND manager.IdEmploye IS NOT NULL';

// Préparation de la requête
$stmt = $connexion->prepare($query);

// Vérification de la préparation de la requête
if (!$stmt) {
    die("Erreur de préparation de la requête: " . $connexion->error);
}

// Liaison des paramètres
$stmt->bind_param("i", $idemploye);

// Exécution de la requête
$stmt->execute();

// Récupération des résultats
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approuver Notes de Frais</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>

    <div class="menu-container">
	<div class="top-left" align="left">
        <a href="menu.php" class="back-link">↩ Retour au menu</a>
    </div>
        <div class="top-right" align="right">
            <?php echo "Bienvenue $prenom $nom <br> $nomTypeEmploye <br>"; ?>
            <a href="deconnexion.php" class="logout-link">Déconnexion</a>
            <br>
        </div>

        <h2>Approuver Notes de Frais</h2>

        <?php
        // Afficher la liste des demandes de note de frais à approuver
        while ($row = $result->fetch_assoc()) {
            echo '<div class="submenu-container">
			<form method="post" action="approuveNoteFrais.php">
			<table>
			<tr>	
			<td>
            <strong>Employé:</strong></td>
			<td>' . $row['NomEmploye'] . ' ' . $row['PrenomEmploye'] . '</td></tr>';
            echo '<tr>	
			<td><strong>Motif:</strong></td><td> ' . $row['Motif'] . '</td></tr>';
            echo '<tr><td><strong>Montant:</strong></td><td> ' . $row['Montant'] . '</td></tr>';
            echo '<tr><td><strong>Date de Demande:</strong></td><td> ' . $row['DateDemande'] . '</td></tr>';
			echo'<tr><td><strong>
				Approuver
			</strong></td>
			<td>
				<select id="estApprouve" name="estApprouve">
				<option value="1" selected>Oui</option>
				<option value="0">Non</option>
			</td></tr>
			</table>';
            echo '<tr><td><input type="hidden" name="idNoteDeFrais" value="' . $row['IdNoteDeFrais'] . '">';
            echo '<button type="submit">Mettre à jour</button>';
            echo '</form>';
            echo '</div>';
        }

// Afficher le message de confirmation s'il existe
if (isset($_SESSION['message'])) {
	echo '<div class="confirmation-message">' . $_SESSION['message'] . '</div>';
	// Supprimer le message de la session pour qu'il ne s'affiche pas à chaque rafraîchissement
	unset($_SESSION['message']);
}
echo '
    </div>
</body>

</html>';

// Fermeture du statement
$stmt->close();

// Fermeture de la connexion à la base de données
$connexion->close();
?>


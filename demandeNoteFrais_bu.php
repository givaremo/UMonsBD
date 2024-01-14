<?php
session_start();
$idemploye = $_SESSION['idemploye'];
$nom = $_SESSION['nom'];
$prenom = $_SESSION['prenom'];
$estChef = $_SESSION['estChef'];
$estRH = $_SESSION['estRH'];
$estDirecteurFinancier = $_SESSION['estDirecteurFinancier'];
$nomTypeEmploye = $_SESSION['nomTypeEmploye'];

// Inclure le fichier de connexion à la base de données
// require_once 'connexion_bdd.php';

// Traitement du formulaire de note de frais
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitNoteFrais'])) {
    // Récupérer les données du formulaire
    $montant = $_POST['montant'];
    $dateDuFrais = $_POST['date_du_frais'];
    $motif = $_POST['motif'];
    // Ajoutez les autres champs nécessaires

    // Assurez-vous de valider et de sécuriser les données avant de les insérer dans la base de données
    // Utilisez des requêtes préparées pour éviter les attaques par injection SQL

    // Etablir la connexion à la base de données
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
$query = "CALL usp_ajoutNotesdeFrais(?, ?, ?, ?, ?, ?, ?)";
$stmt = $connexion->prepare($query);

// Vérifier si la préparation de la requête a réussi
if ($stmt === FALSE) {
    die("Erreur lors de la préparation de la requête : " . $connexion->error);
}

// Initialiser les champs d'approbation à NULL
$FKIdEmploye = $idemploye;
$fkemplaprouv = null;
$fkdirfinaprouv = null;

// Binder les paramètres à la requête
$stmt->bind_param("ssdssss", $dateDuFrais, $dateDuFrais, $montant, $motif, $FKIdEmploye, $fkemplaprouv, $fkdirfinaprouv);

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
echo '<div class="logo"></div>' ;
echo  '<div class="menu-container">';
// Ajout de la flèche de retour vers la page menu.php
echo '<div class="top-left" align="left">
        <a href="menu.php" class="back-link">↩ Retour au menu</a>
      </div>';
echo '<div class="menu-container">';
echo '<div class="top-right" align="right">';
echo "Bienvenue $prenom $nom <br> $nomTypeEmploye <br>";
echo '<a href="deconnexion.php" class="logout-link">Déconnexion</a>';
echo '<br></div>';

echo '<h2>Demande de Notes de Frais</h2>';

echo '<div class="submenu-container" width="100%">';
// Affichez le formulaire
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
echo '</div>';

echo '</div>
</body>
</html>';
?>


<?php

session_unset();
session_start();

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

// Déclarer les paramètres d'entrée
$parametre1 = $_POST['email'];
$parametre2 = $_POST['password'];

// Déclarer le paramètre de sortie
$idemp = null;

// Préparer et exécuter la procédure stockée
$stmt = $conn->prepare("CALL usp_connectUser(?, ?)");
$stmt->bind_param("ss", $parametre1, $parametre2);
$stmt->execute();

// Récupérer le résultat de la procédure stockée
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Vérifier si une ligne a été retournée
if ($row) {
    // Récupérer la valeur de la colonne idemploye
    $idemploye = $row['idemploye'];
    $estChef = $row['estChef'];
    $estRH = $row['estRH'];
    $estDirecteurFinancier = $row['estDirecteurFinancier'];
    $nom = $row['nom'];
    $prenom = $row['prenom'];
    
    echo "Bienvenue " . $nom . $prenom;
    $_SESSION['idemploye'] = $idemploye;
    $_SESSION['estChef'] = $estChef;
    $_SESSION['estRH'] = $estRH;
    $_SESSION['estDirecteurFinancier'] = $estDirecteurFinancier;
    $_SESSION['nom'] = $nom;
    $_SESSION['prenom'] = $prenom;

    // Rediriger vers une autre page
    header("Location: menu.php");
    exit(); 

} else {
    echo "Aucun employé correspondant aux informations fournies.";
}


// Fermer la connexion à la base de données
$conn->close();

?>

<?php
session_start();
include 'menu.html';

if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour accéder à cette page.");
}

// Inclure la connexion à la base de données
include '../entrepreneur/db.php';

$investor_id = $_SESSION['user_id']; // ID de l'investisseur connecté

// Vérifier si l'ID du projet est passé dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID du projet invalide.");
}

$project_id = $_GET['id'];

// Récupérer les informations sur le projet
$stmt = $conn->prepare("SELECT * FROM projects WHERE id = :project_id");
$stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
$stmt->execute();
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    die("Le projet demandé n'existe pas.");
}

// Vérifier si une demande existe déjà pour cet investisseur et ce projet
$checkStmt = $conn->prepare("SELECT * FROM investment_requests WHERE investor_id = :investor_id AND project_id = :project_id");
$checkStmt->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
$checkStmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
$checkStmt->execute();

if ($checkStmt->rowCount() > 0) {
    die("Vous avez déjà soumis une demande pour ce projet. Veuillez patienter pour la réponse.");
}

// Insérer une nouvelle demande d'investissement
$insertStmt = $conn->prepare("INSERT INTO investment_requests (investor_id, entrepreneur_id, project_id, status, created_at) 
                              VALUES (:investor_id, :entrepreneur_id, :project_id, 'pending', NOW())");
$insertStmt->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
$insertStmt->bindParam(':entrepreneur_id', $project['entrepreneur_id'], PDO::PARAM_INT); // ID de l'entrepreneur associé au projet
$insertStmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);

if ($insertStmt->execute()) {
    echo "<p>Votre demande d'investissement a été soumise avec succès. Vous pouvez suivre son statut depuis votre tableau de bord.</p>";
} else {
    echo "<p>Une erreur s'est produite lors de l'envoi de votre demande. Veuillez réessayer plus tard.</p>";
}
?>

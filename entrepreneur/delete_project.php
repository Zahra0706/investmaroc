<?php
ob_start(); // Démarre la mise en mémoire tampon de sortie

session_start();
include 'menu.php';

if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour accéder à cette page.");
}

include 'db.php';

// Vérifier si l'ID du projet est passé via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        die("ID du projet invalide.");
    }

    $project_id = $_POST['id'];

    // Supprimer le projet de la base de données
    $stmt = $conn->prepare("DELETE FROM projects WHERE id = :project_id");
    $stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);

    // Essayer d'exécuter la suppression
    if ($stmt->execute()) {
        // Redirection après suppression
        header("Location: list_projects.php"); // Redirige vers la liste des projets après suppression
        exit; // Toujours inclure un `exit` après `header()` pour éviter l'exécution du reste du script
    } else {
        echo "Une erreur est survenue lors de la suppression du projet.";
    }
} else {
    die("Requête invalide.");
}

ob_end_flush(); // Vide et arrête la mise en mémoire tampon de sortie
?>

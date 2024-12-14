<?php
ob_start();

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

    try {
        // Démarrer une transaction
        $conn->beginTransaction();

        // 1. Supprimer les enregistrements liés dans la table collaborations
        $stmt = $conn->prepare("DELETE FROM collaborations WHERE project_id = :project_id");
        $stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
        $stmt->execute();

        // 2. Supprimer les enregistrements liés dans la table investment_requests
        $stmt = $conn->prepare("DELETE FROM investment_requests WHERE project_id = :project_id");
        $stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
        $stmt->execute();

        // 3. Supprimer les enregistrements liés dans la table saved_projects
        $stmt = $conn->prepare("DELETE FROM saved_projects WHERE project_id = :project_id");
        $stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
        $stmt->execute();

        // 4. Supprimer le projet de la table projects
        $stmt = $conn->prepare("DELETE FROM projects WHERE id = :project_id");
        $stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
        $stmt->execute();

        // Valider la transaction
        $conn->commit();

        // Redirection après suppression
        header("Location: list_projects.php");
        exit;
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $conn->rollBack();
        echo "Une erreur est survenue lors de la suppression : " . $e->getMessage();
    }
} else {
    die("Requête invalide.");
}

ob_end_flush();
?>
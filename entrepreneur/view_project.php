<?php
// Inclure la connexion à la base de données
include 'db.php';

// Vérifie que l'ID a été transmis
if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Aucun ID fourni']);
    exit;
}

$project_id = $_GET['id'];

try {
    $stmt = $conn->prepare("SELECT title, description, budget, category, created_at FROM projects WHERE id = :id");
    $stmt->bindParam(':id', $project_id, PDO::PARAM_INT);
    $stmt->execute();
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($project) {
        echo json_encode($project);
    } else {
        echo json_encode(['error' => 'Projet introuvable']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

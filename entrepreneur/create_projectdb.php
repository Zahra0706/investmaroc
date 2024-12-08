<?php
include 'db.php';
session_start(); // Démarrage de la session pour récupérer l'utilisateur connecté

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $budget = $_POST['budget'];
    $category = $_POST['category'];

    // Gestion des fichiers média
    $media_path = null;
    if (isset($_FILES['media']) && $_FILES['media']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["media"]["name"]);
        if (move_uploaded_file($_FILES["media"]["tmp_name"], $target_file)) {
            $media_path = $target_file;
        }
    }

    // Récupérer l'ID de l'utilisateur connecté
    $user_id = $_SESSION['user_id'];

    // Insertion dans la base de données
    $sql = "INSERT INTO projects (title, description, budget, category, image,  user_id) 
            VALUES (:title, :description, :budget, :category, :image, : user_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':budget', $budget);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':image', $media_path);
    $stmt->bindParam(': user_id', $user_id);

    if ($stmt->execute()) {
        echo "Projet créé avec succès!";
    } else {
        echo "Erreur lors de la création du projet.";
    }
}
?>

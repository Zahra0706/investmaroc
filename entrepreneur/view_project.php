<?php
include 'db.php';
session_start(); // Démarrage de la session pour récupérer l'utilisateur connecté

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $budget = $_POST['budget'];
    $category = $_POST['category'];

    // Gestion des fichiers média
    $media_paths = []; // Tableau pour stocker les chemins des images

    if (isset($_FILES['media']) && $_FILES['media']['error'][0] == 0) {
        $target_dir = "uploads/";

        // Parcourir les fichiers téléchargés
        foreach ($_FILES['media']['name'] as $key => $filename) {
            $target_file = $target_dir . basename($filename);

            // Vérifier si le fichier a été déplacé correctement
            if (move_uploaded_file($_FILES['media']['tmp_name'][$key], $target_file)) {
                $media_paths[] = $target_file; // Ajouter le chemin de l'image au tableau
            }
        }
    }

    // Récupérer l'ID de l'utilisateur connecté
    $user_id = $_SESSION['user_id'];

    // Insertion du projet dans la base de données
    $sql = "INSERT INTO projects (title, description, budget, category, user_id) 
            VALUES (:title, :description, :budget, :category, :user_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':budget', $budget);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':user_id', $user_id);

    if ($stmt->execute()) {
        // Récupérer l'ID du projet créé
        $project_id = $conn->lastInsertId();

        // Insertion des chemins des images dans la table project_images
        if (!empty($media_paths)) {
            foreach ($media_paths as $media_path) {
                $sql_images = "INSERT INTO project_images (project_id, image_path) VALUES (:project_id, :image_path)";
                $stmt_images = $conn->prepare($sql_images);
                $stmt_images->bindParam(':project_id', $project_id);
                $stmt_images->bindParam(':image_path', $media_path);
                $stmt_images->execute();
            }
        }

        echo "Projet créé avec succès!";
    } else {
        echo "Erreur lors de la création du projet.";
    }
}
?>

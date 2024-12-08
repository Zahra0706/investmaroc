<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour accéder à cette page.");
}

// Inclure la connexion à la base de données
include 'db.php';

// Vérifie que l'ID a été transmis
if (!isset($_GET['id'])) {
    die("Aucun ID de projet fourni.");
}

$project_id = $_GET['id'];

// Récupération des détails du projet
$stmt = $conn->prepare("SELECT title, description, budget, category, created_at FROM projects WHERE id = :id");
$stmt->bindParam(':id', $project_id, PDO::PARAM_INT);
$stmt->execute();
$project = $stmt->fetch(PDO::FETCH_ASSOC);

// Récupérer les images du projet
$imageStmt = $conn->prepare("SELECT image_path FROM project_images WHERE project_id = :id");
$imageStmt->bindParam(':id', $project_id, PDO::PARAM_INT);
$imageStmt->execute();
$images = $imageStmt->fetchAll(PDO::FETCH_ASSOC);

if (!$project) {
    die("Le projet n'existe pas.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Projet</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .project-details-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .project-details {
            background-color: #f4f4f4;
            border-radius: 8px;
            padding: 20px;
            width: 70%;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .project-details h2 {
            margin-bottom: 15px;
        }

        .project-details p {
            margin: 10px 0;
        }

        .images-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .images-gallery img {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100%;
            background-color: #2c3e50;
            color: white;
            padding-top: 20px;
        }

        .menu li {
            list-style: none;
            padding: 10px 20px;
        }

        .menu li a {
            color: white;
            text-decoration: none;
        }

        .menu li a:hover {
            background-color: #34495e;
        }

        .main-content {
            margin-left: 260px;
            padding: 20px;
        }

    </style>
</head>
<body>

    <!-- Menu latéral -->
    <div class="sidebar">
        <div class="logo">
            <h2>Entrepreneur</h2>
        </div>
        <ul class="menu">
            <li><a href="../profil.php"><i class="fas fa-user"></i> Mon Profil</a></li>
            <li><a href="create_project.php"><i class="fas fa-plus-circle"></i> Créer un Projet</a></li>
            <li><a href="list_projects.php"><i class="fas fa-list"></i> Mes Projets</a></li>
            <li><a href="#"><i class="fas fa-envelope"></i> Messagerie</a></li>
            <li><a href="../deconnexion.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1>Détails du Projet</h1>

        <div class="project-details-container">
            <div class="project-details">
                <h2><?= htmlspecialchars($project['title']) ?></h2>
                <p><strong>Description :</strong> <?= htmlspecialchars($project['description']) ?></p>
                <p><strong>Budget :</strong> <?= htmlspecialchars($project['budget']) ?> DH</p>
                <p><strong>Catégorie :</strong> <?= htmlspecialchars($project['category']) ?></p>
                <p><strong>Date de création :</strong> <?= htmlspecialchars($project['created_at']) ?></p>
            </div>

            <!-- Galerie d'images -->
            <div class="images-gallery">
                <?php foreach ($images as $image): ?>
                    <img src="<?= htmlspecialchars($image['image_path']) ?>" alt="Image du projet">
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</body>
</html>

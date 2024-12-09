<?php
session_start();
include 'menu.html'; 
if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour accéder à cette page.");
}

// Inclure la connexion à la base de données
include '../entrepreneur/db.php';

// Vérifier si l'ID du projet est passé dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID du projet invalide.");
}

$project_id = $_GET['id'];

// Récupérer les informations du projet
$stmt = $conn->prepare("SELECT * FROM projects WHERE id = :project_id");
$stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
$stmt->execute();
$project = $stmt->fetch(PDO::FETCH_ASSOC);

// Si le projet n'existe pas
if (!$project) {
    die("Le projet demandé n'existe pas.");
}

// Décoder la chaîne JSON des images
$images = json_decode($project['image'], true); // Convertir la chaîne JSON en tableau PHP
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Projet - Investisseur</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .main-content {
            max-width: 1200px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        
        display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 20px;
    margin-top: -70px;
    margin-left: 300px; /* Ajoute cette ligne */

}

        .project-title {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .project-description {
            font-size: 16px;
            color: #555;
            margin-bottom: 20px;
        }

        .project-details p {
            font-size: 16px;
            color: #666;
        }

        .project-images {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .project-images img {
            width: 300px;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ddd;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .project-images img:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .project-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        .project-buttons button {
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-edit {
            background-color: #28a745;
        }

        .btn-edit:hover {
            background-color: #218838;
        }

        .btn-invest {
            background-color: #007bff;
        }

        .btn-invest:hover {
            background-color: #0056b3;
        }

        .btn-delete {
            background-color: #dc3545;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        .btn-delete i, .btn-edit i, .btn-invest i {
            font-size: 18px;
        }
    </style>
</head>
<body>

    <div class="main-content">
        <div class="project-details">
            <h1 class="project-title"><?= htmlspecialchars($project['title']) ?></h1>
            <p><strong>Description :</strong></p>
            <p class="project-description"><?= nl2br(htmlspecialchars($project['description'])) ?></p>
            <p><strong>Budget nécessaire :</strong> <?= htmlspecialchars($project['capital_needed']) ?> MAD</p> <!-- Affichage du budget -->
            <p><strong>Catégorie :</strong> <?= htmlspecialchars($project['category']) ?></p> <!-- Affichage de la catégorie -->
            <p><strong>Date de création :</strong> <?= date('d/m/Y', strtotime($project['created_at'])) ?></p>

            <h2>Images du projet</h2>
            <div class="project-images">
                <?php if (isset($project['image']) && !empty($project['image'])): ?>
                    <?php if (is_array($images)): ?>
                        <?php foreach ($images as $image): ?>
                            <img src="../entrepreneur/<?= htmlspecialchars($image) ?>" alt="Image du projet">
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Aucune image disponible.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Aucune image disponible pour ce projet.</p>
                <?php endif; ?>
            </div>
            <div class="project-buttons">
                <!-- Bouton Investir -->
                <button class="btn-invest" onclick="window.location.href='invest_project.php?id=<?= $project_id ?>'">
                    <i class="fas fa-money-check-alt"></i> Investir
                </button>

                <!-- Bouton Supprimer (optionnel selon les droits de l'investisseur) -->
               
            </div>

        </div>
    </div>

</body>
</html>

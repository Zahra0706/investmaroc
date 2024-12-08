<?php
session_start();
include 'menu.php'; 
if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour accéder à cette page.");
}

// Inclure la connexion à la base de données
include 'db.php';

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
    <title>Détails du Projet</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .project-details {
            padding: 20px;
        }

        .project-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .project-description {
            margin-bottom: 20px;
        }

        .project-details p {
            font-size: 16px;
        }

        .project-images {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .project-images img {
            width: 300px;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
</head>
<body>

   

    <div class="main-content">
        <div class="project-details">
            <h1 class="project-title"><?= htmlspecialchars($project['title']) ?></h1>
            <p><strong>Description:</strong></p>
            <p class="project-description"><?= nl2br(htmlspecialchars($project['description'])) ?></p>
            <p><strong>Budget:</strong> <?= htmlspecialchars($project['capital_needed']) ?> MAD</p> <!-- Affichage du budget -->
            <p><strong>Catégorie:</strong> <?= htmlspecialchars($project['category']) ?></p> <!-- Affichage de la catégorie -->
            <p><strong>Statut:</strong> <?= htmlspecialchars($project['status']) ?></p> <!-- Affichage du statut -->

            <p><strong>Date de création:</strong> <?= date('d/m/Y', strtotime($project['created_at'])) ?></p>

            <h2>Images du projet</h2>
            <div class="project-images">
                <?php if (isset($project['image']) && !empty($project['image'])): ?>
                    <?php
                    if (is_array($images)): ?>
                        <?php foreach ($images as $image): ?>
                            <img src="<?= htmlspecialchars($image) ?>" alt="Image du projet">
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Aucune image disponible.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Aucune image disponible pour ce projet.</p>
                <?php endif; ?>
            </div>

        </div>
    </div>

</body>
</html>

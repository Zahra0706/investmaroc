<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour accéder à cette page.");
}

// Inclure la connexion à la base de données
include 'db.php';

// Récupération de tous les projets de l'utilisateur connecté
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, title, image FROM projects WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Projets</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .project-list {
            display: flex;
            flex-wrap: wrap;
        }

        .project-item {
            width: 200px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin: 10px;
            overflow: hidden;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease-in-out;
        }

        .project-item:hover {
            transform: translateY(-5px);
        }

        .project-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .project-title {
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0;
        }

        .btn-view-details {
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 10px;
            transition: background-color 0.3s;
        }

        .btn-view-details:hover {
            background-color: #0056b3;
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
        <h1>Mes Projets</h1>

        <?php if (empty($projects)): ?>
            <p>Aucun projet publié pour le moment.</p>
        <?php else: ?>
            <div class="project-list">
                <?php foreach ($projects as $project): ?>
                    <div class="project-item">
                        <img src="<?= $project['image'] ?? 'placeholder.jpg' ?>" alt="Image du projet" class="project-image">
                        <h2 class="project-title"><?= htmlspecialchars($project['title']) ?></h2>
                        <a href="project_details.php?id=<?= $project['id'] ?>" class="btn-view-details">
                            <i class="fas fa-eye"></i> Afficher détails
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>

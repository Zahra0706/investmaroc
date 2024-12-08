<?php
session_start();
include 'menu.php'; // Inclure le menu ici
if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour accéder à cette page.");
}

// Inclure la connexion à la base de données
include 'db.php';

// Récupération de tous les projets de l'utilisateur connecté
$user_id = $_SESSION['user_id'];

// Requête SQL pour récupérer les projets et leurs dates de création
$stmt = $conn->prepare("
    SELECT p.id, p.title, p.created_at
    FROM projects p
    WHERE p.entrepreneur_id = :user_id
    ORDER BY p.created_at DESC
");
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
        /* Styles de la sidebar */
        .sidebar {
            width: 250px;
            height: 100%;
            background-color: #333;
            position: fixed;
            top: 0;
            left: -250px;
            padding: 20px;
            color: white;
            transition: transform 0.3s ease-in-out;
        }

        .sidebar.open {
            left: 0;
        }

        /* Styles pour les éléments du menu */
        .menu {
            list-style: none;
            padding: 0;
        }

        .menu li {
            padding: 10px 0;
        }

        .menu a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            display: flex;
            align-items: center;
        }

        .menu a i {
            margin-right: 10px;
        }

        /* Bouton de bascule */
        .toggle-btn {
            background-color: #333;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            display: block;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            font-size: 24px; /* Taille de l'icône du bouton */
        }

        /* Affichage du bouton de bascule sur mobile */
        @media (max-width: 768px) {
            .toggle-btn {
                display: block;
            }
            /* Réduire la largeur du menu sur mobile */
            .sidebar {
                left: -250px; /* Menu caché au départ */
            }
            .sidebar.open {
                left: 0;
            }
        }

        /* Styles pour la page principale */
        .main-content {
            margin-left: 260px;
            padding: 20px;
        }

        .project-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .project-item {
            width: 250px;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
        }

        .project-item:hover {
            transform: translateY(-5px);
        }

        .project-title {
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
        }

        .project-date {
            font-size: 14px;
            color: #777;
            margin-bottom: 10px;
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
    <!-- Le menu est inclus ici -->
    <!-- Sidebar est inclus via 'menu.php' -->

    <div class="main-content">
        <h1>Mes Projets</h1>

        <?php if (empty($projects)): ?>
            <p>Aucun projet publié pour le moment.</p>
        <?php else: ?>
            <div class="project-list">
                <?php foreach ($projects as $project): ?>
                    <div class="project-item">
                        <h2 class="project-title"><?= htmlspecialchars($project['title']) ?></h2>
                        <p class="project-date"><?= date('d/m/Y', strtotime($project['created_at'])) ?></p>
                        <a href="project_details.php?id=<?= $project['id'] ?>" class="btn-view-details">
                            <i class="fas fa-eye"></i> Afficher détails
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        const toggleBtn = document.getElementById('toggle-btn');
        const sidebar = document.getElementById('sidebar');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    </script>
</body>
</html>

<?php
session_start();
include 'menu.php'; // Inclure le menu
include 'db.php';
if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour accéder à cette page.");
}

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
    <!-- Lien vers Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style> body{
            background-color:white;
        }
        /* Conteneur principal */
        .main-content {
            margin-left: 260px; /* Ajusté pour laisser de la place à la sidebar */
            padding: 30px;
            background-color: #f8f9fa; /* Fond clair */
            min-height: 100vh;
        }

        /* Titre principal */
        .main-content h1 {
            font-size: 2.5rem;
            color: #072A40;
            margin-bottom: 20px;
        }

        /* Liste des projets */
        .project-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        /* Carte individuelle pour chaque projet */
        .project-item {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .project-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        /* Titre des projets */
        .project-title {
            font-size: 1.25rem;
            font-weight: bold;
            margin-top: 15px;
            color: #072A40;
        }

        /* Date de création */
        .project-date {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 15px;
        }

        /* Bouton pour afficher les détails */
        .btn-view-details {
            display: inline-block;
            background-color: #18B7BE;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            margin-bottom: 15px;
            transition: background-color 0.3s ease;
        }

        .btn-view-details:hover {
            background-color: #0d6efd; /* Couleur bleue au survol */
        }

        /* Message lorsqu'il n'y a pas de projets */
        .no-projects {
            text-align: center;
            font-size: 1.2rem;
            color: #777;
            margin-top: 50px;
        }

        /* Responsivité */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar incluse via menu.php -->

    <!-- Contenu principal -->
    <div class="main-content">
        <h1>Mes Projets</h1>

        <?php if (empty($projects)): ?>
            <p class="no-projects">Aucun projet publié pour le moment.</p>
        <?php else: ?>
            <div class="project-list">
                <?php foreach ($projects as $project): ?>
                    <div class="project-item">
                        <h2 class="project-title"><?= htmlspecialchars($project['title']) ?></h2>
                        <p class="project-date">Créé le : <?= date('d/m/Y', strtotime($project['created_at'])) ?></p>
                        <a href="project_details.php?id=<?= $project['id'] ?>" class="btn-view-details">
                            <i class="fas fa-eye"></i> Afficher détails
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Gestion de la sidebar toggle
        const toggleBtn = document.getElementById('toggle-btn');
        const sidebar = document.getElementById('sidebar');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    </script>
</body>
</html>
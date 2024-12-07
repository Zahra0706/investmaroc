<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour accéder à cette page.");
}

// Inclure la connexion à la base de données
include 'db.php';

// Récupération de tous les projets de l'utilisateur connecté
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id, title FROM projects WHERE user_id = :user_id ORDER BY created_at DESC");
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
    <!-- Lien vers une bibliothèque d'icônes comme Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                        <?php
                        // Récupérer toutes les images du projet
                        $stmt_images = $conn->prepare("SELECT image_path FROM project_images WHERE project_id = :project_id");
                        $stmt_images->bindParam(':project_id', $project['id'], PDO::PARAM_INT);
                        $stmt_images->execute();
                        $images = $stmt_images->fetchAll(PDO::FETCH_ASSOC);

                        // Vérifier si des images existent pour ce projet
                        if ($images):
                            foreach ($images as $image):
                                // Vérifier que le chemin de l'image est valide
                                $image_path = htmlspecialchars($image['image_path']);
                                if (file_exists($image_path)):
                                    echo "<img src='" . $image_path . "' alt='Image du projet' class='project-image' style='width: 150px; height: 150px; object-fit: cover; margin-right: 10px;'>";
                                else:
                                    echo "<img src='placeholder.jpg' alt='Aucune image disponible' class='project-image' style='width: 150px; height: 150px; object-fit: cover; margin-right: 10px;'>";
                                endif;
                            endforeach;
                        else:
                            echo "<img src='placeholder.jpg' alt='Aucune image disponible' class='project-image' style='width: 150px; height: 150px; object-fit: cover; margin-right: 10px;'>";
                        endif;
                        ?>

                        <h2><?= htmlspecialchars($project['title']) ?></h2>
                        <a href="view_project.php?id=<?= $project['id'] ?>" class="btn-view-details">Voir les détails</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>

<?php
session_start();
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

// Récupérer les informations du projet avec le nom de l'entrepreneur
$stmt = $conn->prepare("
    SELECT projects.*, users.name AS entrepreneur_name
    FROM projects
    JOIN users ON projects.entrepreneur_id = users.id
    WHERE projects.id = :project_id
");
$stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
$stmt->execute();
$project = $stmt->fetch(PDO::FETCH_ASSOC);

// Si le projet n'existe pas
if (!$project) {
    die("Le projet demandé n'existe pas.");
}

// Décoder la chaîne JSON des images
$images = json_decode($project['image'], true);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Projet</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
              .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #072A40;
            color: #fff;
            padding-top: 20px;
            position: fixed;
        }
        .menu {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .menu li {
            border-bottom: 1px solid #073a50;
        }
        .menu a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: #ecf0f1;
            text-decoration: none;
            font-size: 1rem;
            transition: background-color 0.3s;
        }
        .menu a:hover {
            background-color: #18B7BE;
        }
       
       

        body {
            font-family: Arial, sans-serif;
            background-color: white;
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
            margin-left:280px;
            height:600px;

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
        .menu i{
            padding-right:20px;
            font-size:20px;
        }
        .menu a.active {
      background-color: #18B7BE !important; /* Bleu pour l'élément actif */
      color: white !important; /* Texte en blanc */
    }
    #menu-toggle {
        display: none; /* Masqué par défaut */
        position: fixed; /* Fixé à l'écran */
        top: 20px; /* Ajustez la position verticale */
        left: 20px; /* Positionné à gauche */
        width: 50px; /* Largeur du bouton */
        height: 50px; /* Hauteur du bouton */
        background-color: #18B7BE; /* Couleur de fond */
        color: white; /* Couleur de l'icône */
        border: none; /* Pas de bordure */
        border-radius: 50%; /* Forme circulaire */
        cursor: pointer; /* Curseur en forme de main */
        display: flex; /* Flex pour centrer l'icône */
        justify-content: center; /* Centrer horizontalement */
        align-items: center; /* Centrer verticalement */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Ombre du bouton */
        z-index: 1000; /* Pour s'assurer qu'il est au-dessus des autres éléments */
    }
        @media (max-width: 600px) {
    .sidebar {
        display: none; /* Masquer la sidebar par défaut */
    }
    .sidebar.active {
        display: block; /* Afficher la sidebar quand active */
    }
    .main-content {
        margin:0;
        height: 930px;
    }
}
    </style>
</head>
<body>
<button id="menu-toggle" onclick="toggleMenu()"><i class="fas fa-bars"></i></button>

    <!-- Barre latérale -->
    <div class="sidebar">
    <div class="logo">
        <img src="logo.png" alt="Logo" style="width: 100%; height: auto;">
    </div>
        <ul class="menu">
            <li><a href="profil.php"><i class="fas fa-user-circle"></i> Profil</a></li>
            <li><a href="investisseurs.php"><i class="fas fa-handshake"></i> Investisseurs</a></li>
            <li><a href="entrepreneurs.php"><i class="fas fa-briefcase"></i> Entrepreneurs</a></li>
            <li><a href="projets.php"><i class="fas fa-list"></i> Projets</a></li>
            <li>
                <a href="demande_investissement.php">
                <i class="fas fa-clipboard-list"></i> Demandes d'Investissement
                </a>
            </li>
            <li><a href="collaborations.php"><i class="fas fa-users"></i> Collaborations</a></li>
            <li><a href="../deconnexion.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="project-details">
            <h1 class="project-title"><?= htmlspecialchars($project['title']) ?></h1>
            <p><strong>Description :</strong></p>
            <p class="project-description"><?= nl2br(htmlspecialchars($project['description'])) ?></p>
            <p><strong>Budget :</strong> <?= htmlspecialchars($project['capital_needed']) ?> DHS</p>
            <p><strong>Status :</strong> <?= htmlspecialchars($project['status']) ?></p> <!-- Affichage du budget -->
            <p><strong>Catégorie :</strong> <?= htmlspecialchars($project['category']) ?></p>
            <p><strong>Entrepreneur :</strong> <?= htmlspecialchars($project['entrepreneur_name']) ?></p> <!-- Affichage du nom -->
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
        </div>
    </div>
    <script>
    // Récupérer tous les liens du menu
    const menuLinks = document.querySelectorAll('.menu a');

    // Fonction pour vérifier l'URL actuelle
    function setActiveLink() {
      const currentPath = window.location.pathname;
      menuLinks.forEach(link => {
        const linkPath = new URL(link.href).pathname;
        if (currentPath === linkPath) {
          link.classList.add('active');
        } else {
          link.classList.remove('active');
        }
      });
    }

    // Exécuter la fonction lors du chargement de la page
    window.addEventListener('load', setActiveLink);
  </script>
  <script>
        function toggleMenu() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('active'); 
            if (sidebar.style.display === 'block') {
                sidebar.style.display = 'none';
            } else {
                sidebar.style.display = 'block';
            }
        }
    </script>
</body>
</html>
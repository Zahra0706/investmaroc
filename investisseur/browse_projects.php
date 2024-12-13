<?php
session_start();
include 'menu.html'; 

// Configuration de la base de données
$host = 'localhost';
$dbname = 'investmaroc';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connexion échouée : " . $e->getMessage());
}

// Récupérer les catégories pour le filtre
$categories_stmt = $pdo->prepare("SELECT DISTINCT category FROM projects");
$categories_stmt->execute();
$categories = $categories_stmt->fetchAll(PDO::FETCH_COLUMN);

// Récupérer les projets validés avec filtrage et recherche
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Construction de la requête SQL
$sql = "SELECT * FROM projects WHERE status = 'validé'";
if ($category_filter) {
    $sql .= " AND category = :category";
}
if ($search_query) {
    $sql .= " AND title LIKE :search";
}
$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);

// Liaison des paramètres
if ($category_filter) {
    $stmt->bindParam(':category', $category_filter);
}
if ($search_query) {
    $search_param = "%" . $search_query . "%";
    $stmt->bindParam(':search', $search_param);
}

$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projets Disponibles</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Style personnalisé -->
    <style>
        .container {
            display: block; /* Change de flex à block pour que les éléments soient empilés */
            justify-content: center;
            align-items: center;
            padding: 20px;
            margin-left: 250px; /* Ajoute cette ligne */
        }

        .header {
            display: flex;
            flex-direction: column; /* Alignement vertical */
            justify-content: center;
            align-items: center;
            padding: 30px 0;
        }  
        
        .title {
            font-size: 2.5rem;
            color: #072A40;
            text-align: center;
        }

        .left-align {
            align-self: flex-end;
            background-color: #072A40;
            color: #fff;
            padding: 10px 25px;
            font-size: 1rem;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .left-align i {
            margin-right: 10px; /* Espace entre l'icône et le texte */
        }

        .left-align:hover {
            border: 1px solid #072A40;
        }

        /* ======== Style des cartes des projets ======== */
        .project-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            margin: 20px 0; /* Espacement entre les cartes */
        }

        .project-card:hover {
            transform: scale(1.05);
        }

        .project-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .project-card h3 {
            margin: 15px 0;
            color: #072A40;
            font-size: 1.3rem;
            text-align: center;
        }

        /* ======== Style des boutons des projets ======== */
        .btn-project {
            display: block;
            text-align: center;
            padding: 10px 20px;
            background-color: #18B7BE;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            margin: 15px auto;
            width: 80%;
            transition: background-color 0.3s;
        }

        .btn-project:hover {
            background-color: #16a7b8;
        }

        @media (max-width: 600px) {
            .container {
                margin-left: 0px; /* Ajoute un léger margin-left en mode téléphone */
            }
        }
    </style>
</head>
<body>

<!-- Contenu principal -->
<div class="container">
    <!-- Header -->
    <div class="header">
        <h1 class="title"><b>Projets Disponibles</b></h1>
        
        <form method="GET" class="d-flex mb-4" id="filterForm">
            <select name="category" class="form-select me-2" id="categorySelect">
                <option value="">Toutes les catégories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo htmlspecialchars($category); ?>" <?php echo $category_filter == $category ? 'selected' : ''; ?>><?php echo htmlspecialchars($category); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="search" class="form-control me-2" placeholder="Rechercher un projet" id="searchInput" value="<?php echo htmlspecialchars($search_query); ?>">
        </form>

        <a href="saved_projects.php" class="btn left-align">
            <i class="fas fa-bookmark"></i> Voir les projets enregistrés
        </a>
    </div>

    <!-- Liste des projets -->
    <div class="row row-cols-1 row-cols-md-3 g-4" id="projectList">
        <?php foreach ($projects as $project): ?>
            <div class="col">
                <div class="project-card">
                    <div class="p-3">
                        <!-- Affichage du titre du projet -->
                        <h3><?php echo htmlspecialchars($project['title']); ?></h3>

                        <!-- Affichage de la date de création -->
                        <p class="text-muted">Créé le : <?php echo date('d-m-Y', strtotime($project['created_at'])); ?></p>

                        <a href="project_details.php?id=<?php echo $project['id']; ?>" class="btn-project">Voir Détails</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Fonction pour mettre à jour les projets affichés
    function updateProjects() {
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        const queryString = new URLSearchParams(formData).toString();

        fetch('projects.php?' + queryString)
    .then(response => {
        console.log(response);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(data => {
        document.getElementById('projectList').innerHTML = data;
    })
    .catch(error => console.error('Erreur:', error));
    }

    // Écoutez les changements sur le select et le champ de recherche
    document.getElementById('categorySelect').addEventListener('change', updateProjects);
    document.getElementById('searchInput').addEventListener('input', updateProjects);
</script>

</body>
</html>
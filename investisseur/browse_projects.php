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

// Récupérer les projets validés
$stmt = $pdo->prepare("SELECT * FROM projects WHERE status = 'validé'");
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .header {
            text-align: center;
            padding: 30px 0;
            position: relative;
        }
        .header h1 {
            font-size: 2.5rem;
            color: #072A40;
        }
        .header .btn {
            background-color: #18B7BE;
            color: #fff;
            padding: 10px 25px;
            font-size: 1rem;
            border-radius: 5px;
            transition: background-color 0.3s;
            position: absolute;
            right:0;
            top : 30px;
        }
        .header .btn:hover {
            background-color: #16a7b8;
        }
        .project-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s;
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
        }
        .btn-project {
            display: inline-block;
            padding: 10px 20px;
            background-color: #18B7BE;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 15px;
            transition: background-color 0.3s;
        }
        .btn-project:hover {
            background-color: #16a7b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><b>Projets Disponibles</b></h1>
            <a href="saved_projects.php" class="btn"><img src="../images/save-instagram.png" alt="" style="width: 20px; height: 20px; margin-right:10px"> Voir les projets enregistrés</a>
        </div>

        <!-- Liste des projets -->
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($projects as $project): ?>
                <div class="col">
                    <div class="project-card">
                        <img src="images/<?php echo htmlspecialchars($project['image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                        <div class="p-3">
                            <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                            <a href="project_details.php?id=<?php echo $project['id']; ?>" class="btn-project">Voir Détails</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

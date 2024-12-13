<?php
session_start();
include 'menu.html'; 
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Rediriger si l'utilisateur n'est pas connecté
    exit();
}

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

// Récupérer les projets enregistrés par l'utilisateur
$userId = $_SESSION['user_id']; // L'ID de l'utilisateur connecté
$stmt = $pdo->prepare("SELECT p.* FROM projects p
                       JOIN saved_projects sp ON p.id = sp.project_id
                       WHERE sp.investor_id = :user_id");
$stmt->execute(['user_id' => $userId]);
$savedProjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projets Enregistrés</title>
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
            right: 0;
            top: 30px;
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
        .container{
            margin-left:270px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><b>Projets Enregistrés</b></h1>
            <a href="browse_projects.php" class="btn">Retour aux projets</a>
        </div>

        <!-- Liste des projets enregistrés -->
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php if (count($savedProjects) > 0): ?>
                <?php foreach ($savedProjects as $project): ?>
                    <div class="col">
                        <div class="project-card">
                            <div class="p-3">
                                <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                                <a href="project_details.php?id=<?php echo $project['id']; ?>" class="btn-project">Voir Détails</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; padding: 20px;">Aucun projet enregistré.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

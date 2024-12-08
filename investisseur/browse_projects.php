<?php
include 'menu.html'; 
session_start();

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
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .projects-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .project-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: center;
            transition: transform 0.3s;
        }
        .project-card:hover {
            transform: scale(1.05);
        }
        .project-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .project-card h3 {
            margin: 0;
            padding: 15px;
            font-size: 1.2rem;
            color: #072A40;
        }
        .btn {
            display: inline-block;
            margin: 10px 0;
            padding: 10px 20px;
            background-color: #18B7BE;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #16a7b8;
        }
    </style>
</head>
<body>
    <h1 style="text-align:center; margin-top: 20px;">Projets Disponibles</h1>
    <div class="projects-container">
        <?php foreach ($projects as $project): ?>
            <div class="project-card">
                <img src="images/<?php echo htmlspecialchars($project['image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
                <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                <a href="project_details.php?id=<?php echo $project['id']; ?>" class="btn">Voir Détails</a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>

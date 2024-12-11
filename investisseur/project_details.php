<?php
include 'menu.html'; 
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour voir cette page.");
}

$investorId = $_SESSION['user_id'];

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

// Récupérer le projet
if (!isset($_GET['id'])) {
    die("Projet non spécifié.");
}
$projectId = $_GET['id'];

// Requête pour récupérer les détails du projet
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = :id");
$stmt->bindParam(':id', $projectId);
$stmt->execute();
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    die("Projet introuvable.");
}

// Vérifier si le projet est déjà enregistré
$stmt = $pdo->prepare("SELECT * FROM saved_projects WHERE investor_id = :investor_id AND project_id = :project_id");
$stmt->execute([':investor_id' => $investorId, ':project_id' => $projectId]);
$isSaved = $stmt->fetch();

// Récupérer les images du projet
$query = "SELECT image FROM projects WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id', $projectId);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier si des images sont disponibles
$images = !empty($row['image']) ? explode(",", $row['image']) : [];

// Récupérer les informations de l'entrepreneur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $project['entrepreneur_id']);
$stmt->execute();
$entrepreneur = $stmt->fetch(PDO::FETCH_ASSOC);

// Traitement du formulaire (enregistrer ou désenregistrer le projet)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$isSaved) {
        // Enregistrer le projet
        $stmt = $pdo->prepare("INSERT INTO saved_projects (investor_id, project_id) VALUES (:investor_id, :project_id)");
        $stmt->execute([':investor_id' => $investorId, ':project_id' => $projectId]);
    } else {
        // Désenregistrer le projet
        $stmt = $pdo->prepare("DELETE FROM saved_projects WHERE investor_id = :investor_id AND project_id = :project_id");
        $stmt->execute([':investor_id' => $investorId, ':project_id' => $projectId]);
    }
    header("Location: project_details.php?id=$projectId");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Projet</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
   
    .container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        margin-left: 300px;
    }
    h1 {
        color: #072A40;
        text-align: center;
        margin-bottom: 20px;
    }
    .project-details {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
    }
    .project-images {
        flex: 1;
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: space-between;
    }
    .project-images img {
        max-width: 48%;
        border-radius: 8px;
        box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease-in-out;
    }
    .project-images img:hover {
        transform: scale(1.05);
    }
    .project-info {
        flex: 1;
        max-width: 600px;
    }
    .project-info p {
        font-size: 1rem;
        color: #555;
        line-height: 1.6;
    }
    .project-info h3 {
        font-size: 1.5rem;
        color: #18B7BE;
    }
    .contact-section {
        margin-top: 30px;
        background-color: #e8f5f3;
        padding: 20px;
        border-radius: 8px;
    }
    .contact-section h3 {
        font-size: 1.2rem;
        color: #072A40;
    }
    .contact-btn {
        display: inline-block;
        background-color: #18B7BE;
        color: white;
        padding: 12px 24px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 1rem;
        transition: background-color 0.3s ease;
        margin-top: 15px;
    }
    .contact-btn:hover {
        background-color: #16a7b8;
    }
    .saved-btn, .unsaved-btn {
        border: none;
        background-color: transparent;
        cursor: pointer;
        transition: color 0.3s ease;
        padding: 10px;
        display: inline-block;
        margin-top: 20px;
    }
    .saved-btn img, .unsaved-btn img {
        width: 30px;
        height: 30px;
        transition: filter 0.3s ease;
    }
    .saved-btn:hover img {
        filter: brightness(1.2);
    }
    .unsaved-btn:hover img {
        filter: brightness(0.8);
    }
</style>

    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($project['title']); ?></h1>
        <form method="post">
            <button type="submit" class="<?php echo $isSaved ? 'unsaved-btn' : 'saved-btn'; ?>">
                <img src="<?php echo $isSaved ? '../images/save-instagram (1).png' : '../images/save-instagram.png'; ?>" alt="Enregistrer" style="width: 30px; height: 30px;">
            </button>
        </form>

        <div class="project-details">
            <div class="project-images">
            <?php

// Afficher les images du projet
if (!empty($images)) {
    foreach ($images as $image) {
        // Nettoyer le chemin de l'image en supprimant les crochets, guillemets et autres caractères indésirables
        $imagePath = trim($image, ' "[]'); // Supprimer les guillemets, crochets et espaces autour du chemin
        $imagePath = stripslashes($imagePath); // Supprimer les antislashs échappés

        // Afficher le chemin complet de l'image pour vérifier

        // Vérification de l'existence de l'image
        $fullImagePath = '../entrepreneur/' . $imagePath;
        if (file_exists($fullImagePath)) {
            echo '<img src="' . htmlspecialchars($fullImagePath) . '" alt="Image">';
        } else {
            echo "L'image n'existe pas : " . $fullImagePath . "<br>"; // Afficher un message d'erreur si l'image n'existe pas
        }
    }
} else {
    echo '<p>Aucune image disponible pour ce projet.</p>';
}
?>




            </div>

            <div class="project-info">
                <h3>Description</h3>
                <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                <h3>Capital Nécessaire</h3>
                <p><?php echo htmlspecialchars($project['capital_needed']); ?> DH</p>
            </div>
        </div>

            <a href="invest_project.php?id=<?php echo $projectId; ?>" class="contact-btn">Investir</a>
    </div>
</body>
</html>
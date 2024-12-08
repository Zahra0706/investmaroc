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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Enregistrer ou désenregistrer le projet
    if (!$isSaved) {
        $stmt = $pdo->prepare("INSERT INTO saved_projects (investor_id, project_id) VALUES (:investor_id, :project_id)");
        $stmt->execute([':investor_id' => $investorId, ':project_id' => $projectId]);
        header("Location: project_details.php?id=$projectId");
        exit;
    } else {
        $stmt = $pdo->prepare("DELETE FROM saved_projects WHERE investor_id = :investor_id AND project_id = :project_id");
        $stmt->execute([':investor_id' => $investorId, ':project_id' => $projectId]);
        header("Location: project_details.php?id=$projectId");
        exit;
    }
}

// Récupérer les informations de l'entrepreneur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $project['entrepreneur_id']); // Utiliser 'id_entrepreneur' au lieu de 'user_id'

$stmt->execute();
$entrepreneur = $stmt->fetch(PDO::FETCH_ASSOC);
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
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #072A40;
            text-align: center;
        }
        .project-details {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }
        .project-details img {
            max-width: 500px;
            width: 100%;
            border-radius: 8px;
        }
        .project-info {
            flex: 1;
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
        }
        .contact-btn:hover {
            background-color: #16a7b8;
        }
        .saved-btn {
            background-color: #ff9800;
        }
        .saved-btn:hover {
            background-color: #f57c00;
        }
        .unsaved-btn {
            background-color: #e64a19;
        }
        .unsaved-btn:hover {
            background-color: #d84315;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($project['title']); ?></h1>
        <div class="project-details">
            <img src="images/<?php echo htmlspecialchars($project['image']); ?>" alt="Image du projet">
            <div class="project-info">
                <h3>Description</h3>
                <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                <h3>Capital Nécessaire</h3>
                <p><?php echo htmlspecialchars($project['capital_needed']); ?> DH</p>
                <form method="post">
                    <button type="submit" class="<?php echo $isSaved ? 'unsaved-btn' : 'saved-btn'; ?>">
                        <?php echo $isSaved ? '<i class="fas fa-trash"></i> Désenregistrer ce Projet' : '<i class="fas fa-save"></i> Enregistrer ce Projet'; ?>
                    </button>
                </form>
            </div>
        </div>

        <div class="contact-section">
            <h3>Contacter l'entrepreneur</h3>
            <p><strong>Nom : </strong><?php echo htmlspecialchars($entrepreneur['name']); ?></p>
            <p><strong>Email : </strong><a href="mailto:<?php echo htmlspecialchars($entrepreneur['email']); ?>"><?php echo htmlspecialchars($entrepreneur['email']); ?></a></p>
            <a href="mailto:<?php echo htmlspecialchars($entrepreneur['email']); ?>" class="contact-btn">
                <i class="fas fa-envelope"></i> Envoyer un message
            </a>
        </div>
    </div>
</body>
</html>

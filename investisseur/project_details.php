<?php
ob_start(); // Commence la mise en mémoire tampon
session_start();
// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour voir cette page.");
}
include 'menu.html';

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
    ob_end_flush(); // Envoie le contenu tamponné
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
        height:700px;
        position: relative;
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
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: center; /* Aligner les images au centre */
}

.project-images img {
    border-radius: 8px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease-in-out;
}

.project-images img:hover {
    transform: scale(1.05);
}

/* Cas où il y a 1 image */
.project-images.single img {
    width: 100%; /* Prend toute la largeur */
    max-width: 500px; /* Limiter la taille pour une meilleure lisibilité */
}

/* Cas où il y a 2 images */
.project-images.double img {
    width: 48%; /* Les images prennent chacune la moitié de la largeur */
}

/* Cas où il y a 3 images */
.project-images.triple img {
    width: 32%; /* Les images prennent chacune un tiers de la largeur */
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
        background-color: #072A40;
        color: white;
        padding: 12px 24px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 1rem;
        transition: background-color 0.3s ease;
        margin-top: 15px;
        margin-bottom:20px ;
    }
    .contact-btn:hover {
        background-color: #16a7b8;
    }
    .saved-btn, .unsaved-btn {
        border: none;
        position:absolute;
        right: 0px;
        top: -20px;
        margin: 0;
        padding: 0;
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
    @media (max-width: 600px) {
            .container {
                margin-left: 0px; /* Ajoute un léger margin-left en mode téléphone */
            }}
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

        <div class="project-images <?php echo count($images) === 1 ? 'single' : (count($images) === 2 ? 'double' : 'triple'); ?>">
    <?php
    // Vérifiez si le champ image est null ou un tableau vide
    if ($row['image'] === null || $row['image'] === '[]') {
        echo '<p>Le projet n\'a pas d\'images.</p>';
    } else {
        // Vérifiez si des images sont disponibles
        if (!empty($images)) {
            foreach ($images as $image) {
                // Nettoyez l'image pour éviter les erreurs
                $imagePath = trim($image, ' "[]');
                $imagePath = stripslashes($imagePath);
                $fullImagePath = '../entrepreneur/' . $imagePath;

                // Vérifiez si le fichier image existe
                if (file_exists($fullImagePath)) {
                    echo '<img src="' . htmlspecialchars($fullImagePath) . '" alt="Image du projet">';
                } else {
                    echo '<p>L\'image n\'existe pas : ' . htmlspecialchars($fullImagePath) . '</p>';
                }
            }
        } else {
            echo '<p>Le projet n\'a pas d\'images.</p>';
        }
    }
    ?>
</div>
            <div class="project-info">
                <h3><strong>Description :</strong></h3>
                <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                <h3><strong> Capital Nécessaire :</strong></h3>
                <p><?php echo htmlspecialchars($project['capital_needed']); ?> DH</p>
                <h3><strong>Catégorie :</strong></h3>
                <p> <?= htmlspecialchars($project['category']) ?></p>

            </div>
            <a href="invest_project.php?id=<?php echo $projectId; ?>" class="contact-btn">
    <i class="fas fa-coins"></i> Investir
</a>

        </div>

    </div>
</body>
</html>
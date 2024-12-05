<?php
// Démarrage de la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour accéder à cette page.");
}

// Configuration de la base de données
$host = 'localhost';
$dbname = 'investmaroc';
$user = 'root';
$pass = '';

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connexion à la base de données échouée : " . $e->getMessage());
}

// Récupérer l'ID de l'utilisateur connecté
$userId = $_SESSION['user_id'];

// Récupérer les projets publiés par l'utilisateur connecté
$stmt = $pdo->prepare("SELECT * FROM projects WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vérifier si l'utilisateur a des projets
if (empty($projects)) {
    echo "<p>Aucun projet publié pour le moment.</p>";
} else {
    echo "<h1>Mes Projets</h1>";
    echo "<ul>";

    // Affichage des projets
    foreach ($projects as $project) {
        echo "<li>";
        echo "<h2>" . htmlspecialchars($project['title']) . "</h2>";
        echo "<p><strong>Description :</strong> " . htmlspecialchars($project['description']) . "</p>";
        echo "<p><strong>Budget nécessaire :</strong> " . htmlspecialchars($project['budget']) . " €</p>";
        echo "<p><strong>Catégorie :</strong> " . htmlspecialchars($project['category']) . "</p>";
        echo "<p><strong>Créé le :</strong> " . htmlspecialchars($project['created_at']) . "</p>";
        echo "<a href='view_project.php?id=" . $project['id'] . "'>Voir les détails</a>";
        echo "</li>";
    }

    echo "</ul>";
}
?>

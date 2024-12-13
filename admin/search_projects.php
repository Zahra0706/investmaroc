<?php
session_start();

// Vérifier si l'utilisateur est administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Vous devez être connecté en tant qu'administrateur pour accéder à cette page.");
}

// Configuration de la base de données
$host = 'localhost';
$dbname = 'investmaroc';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connexion à la base de données échouée : " . $e->getMessage());
}

// Récupérer les projets avec recherche
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$stmt = $pdo->prepare("SELECT * FROM projects WHERE title LIKE :search ORDER BY created_at DESC");
$stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Générer le tableau des résultats
foreach ($projects as $project) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($project['title']) . '</td>';
    echo '<td>' . htmlspecialchars($project['capital_needed']) . ' DH</td>';
    echo '<td>' . htmlspecialchars($project['status']) . '</td>';
    echo '<td class="action-buttons">';
    if ($project['status'] == 'en attent') {
        echo '<a href="?action=change_status&id=' . $project['id'] . '" class="btn btn-validate">
                <i class="fas fa-check-circle"></i> Valider
              </a>';
    }
    echo '<a href="project_details.php?id=' . $project['id'] . '" class="btn">
            <i class="fas fa-info-circle"></i> Afficher Détails
          </a>';
    echo '<a href="?action=delete&id=' . $project['id'] . '" class="btn btn-danger" onclick="confirmDeletion(event)">
            <i class="fas fa-trash-alt"></i> Supprimer
          </a>';
    echo '</td>';
    echo '</tr>';
}
?>
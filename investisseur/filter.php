<?php
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

// Récupérer les projets validés avec filtrage et recherche
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Construction de la requête SQL
$sql = "SELECT p.* FROM projects p
        LEFT JOIN investment_requests ir ON p.id = ir.project_id AND ir.status IN ('pending', 'accepted')
        LEFT JOIN collaborations c ON p.id = c.project_id
        WHERE p.status = 'validé' 
        AND ir.project_id IS NULL 
        AND c.project_id IS NULL";
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

// Affichage des projets sous forme de HTML
foreach ($projects as $project) {
    echo '<div class="col">';
    echo '    <div class="project-card">';
    echo '        <div class="p-3">';
    echo '            <h3>' . htmlspecialchars($project['title']) . '</h3>';
    echo '            <p class="text-muted">Créé le : ' . date('d-m-Y', strtotime($project['created_at'])) . '</p>';
    echo '            <a href="project_details.php?id=' . $project['id'] . '" class="btn-project">Voir Détails</a>';
    echo '        </div>';
    echo '    </div>';
    echo '</div>';
}
?>
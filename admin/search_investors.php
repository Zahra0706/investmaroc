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

// Récupérer les investisseurs avec recherche
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'investor' AND name LIKE :search");
$stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
$stmt->execute();
$investors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Générer le tableau des résultats
foreach ($investors as $investor) {
    echo '<tr>';
    echo '<td><img src="' . htmlspecialchars('../' . $investor['image'] ?: 'default-avatar.png') . '" alt="Image"></td>';
    echo '<td>' . htmlspecialchars($investor['name']) . '</td>';
    echo '<td>' . htmlspecialchars($investor['email']) . '</td>';
    echo '<td>' . htmlspecialchars($investor['telephone']) . '</td>';
    echo '</tr>';
}
?>
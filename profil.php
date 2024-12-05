<?php
// Démarrage de la session
session_start();

// Vérifier si l'utilisateur est connecté (par exemple, en vérifiant la session)
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

// Récupérer les informations de l'utilisateur depuis la base de données
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $userId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier si l'utilisateur existe
if (!$user) {
    die("Utilisateur introuvable.");
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Utilisateur</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Bienvenue, <?php echo htmlspecialchars($user['name']); ?> !</h1>

        <!-- Photo de profil -->
        <div class="profile-photo">
            <?php if (!empty($user['photo'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($user['photo']); ?>" alt="Photo de profil" width="150" height="150">
            <?php else: ?>
                <img src="default-profile.png" alt="Photo de profil par défaut" width="150" height="150">
            <?php endif; ?>
        </div>

        <!-- Informations de l'utilisateur -->
        <div class="profile-info">
            <p><strong>Nom :</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Email :</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($user['telephone']); ?></p>
            <p><strong>Rôle :</strong> <?php echo htmlspecialchars($user['role']); ?></p>
        </div>

        <!-- Option pour modifier le profil -->
        <div class="edit-profile">
            <a href="edit_profile.php">Modifier le profil</a>
        </div>
    </div>
</body>
</html>

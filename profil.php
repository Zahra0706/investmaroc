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
    <link rel="stylesheet" href="admin/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
/* Container pour le contenu */
.container {
    margin-left: 250px; /* Laisse de l'espace pour la barre latérale */
    width: calc(100% - 250px); /* Prend toute la largeur sauf celle de la barre latérale */
    height: 100vh; /* Prend toute la hauteur de l'écran */
    display: flex;
    justify-content: center; /* Centre horizontalement */
    align-items: center; /* Centre verticalement */
    background: #f9f9f9; /* Couleur d'arrière-plan */
    padding: 20px;
    box-sizing: border-box;
}

/* Si vous voulez forcer l'élément de profil à être au centre, vous pouvez ajouter un conteneur supplémentaire */
.profile-container {
    display: flex;
    flex-direction: column;
    align-items: center; /* Centrer les éléments à l'intérieur */
    text-align: center; /* Centre le texte */
    background: #fff; /* Couleur de fond du profil */
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); /* Ombre douce autour du profil */
    width: 100%;
    max-width: 600px; /* Limite la largeur pour que le profil ne soit pas trop large */
}



        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100%;
            color: white;
            padding-top: 20px;
        }
        .sidebar .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar .logo h2 {
            color: #fff;
            text-align:center ;
        }
       
        .profile-photo img {
            border-radius: 50%;
            border: 2px solid #4CAF50;
        }
        .profile-info p {
            font-size: 16px;
        }
    </style>
</head>
<body>
    <!-- Barre latérale -->
    <div class="sidebar">
        <div class="logo">
            <h2>Admin Dashboard</h2>
        </div>
        <ul class="menu">
            <li>
                <a href="../profil.php">
                    <i class="fas fa-user-circle"></i> Profil
                </a>
            </li>
            <li>
                <a href="#investisseurs">
                    <i class="fas fa-handshake"></i> Investisseurs
                </a>
            </li>
            <li>
                <a href="#entrepreneurs">
                    <i class="fas fa-briefcase"></i> Entrepreneurs
                </a>
            </li>
            <li>
                <a href="#projets">
                    <i class="fas fa-list"></i> Projets
                </a>
            </li>
            <li>
                <a href="#collaborations">
                    <i class="fas fa-users"></i> Collaborations
                </a>
            </li>
            <li>
                <a href="#logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </li>
        </ul>
    </div>

    <!-- Contenu principal -->
    <div class="container">
    <div class="profile-container">
        <h1>Bienvenue, <?php echo htmlspecialchars($user['name']); ?> !</h1>

        <!-- Photo de profil -->
        <div class="profile-photo">
            <img src="<?php echo !empty($user['image']) ? htmlspecialchars($user['image']) : 'default-profile.png'; ?>" alt="Photo de profil" width="150" height="150">
        </div>

        <!-- Informations de l'utilisateur -->
        <div class="profile-info">
            <p><strong>Nom :</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Email :</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($user['telephone']); ?></p>
            <p><strong>Rôle :</strong> <?php echo htmlspecialchars($user['role']); ?></p>
        </div>

        <!-- Formulaire pour modifier l'image -->
        <div class="upload-section">
            <h3>Modifier votre photo de profil</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="profile_image" accept="image/*" required>
                <button type="submit">Mettre à jour</button>
            </form>
            <?php if (isset($error)) : ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>


</body>
</html>

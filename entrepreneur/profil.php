<?php
// Démarrage de la session
session_start();
include 'menu.php'; 

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

// Récupérer les informations de l'utilisateur depuis la base de données
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $userId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier si l'utilisateur existe
if (!$user) {
    die("Utilisateur introuvable.");
}

// Traiter la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $profileImage = $_FILES['profile_image'];

    // Traitement de l'image
    if ($profileImage['error'] === UPLOAD_ERR_OK) {
        $targetDir = '../entrepreneur/uploads/';
        $targetFile = $targetDir . basename($profileImage['name']);
        move_uploaded_file($profileImage['tmp_name'], $targetFile);
    } else {
        $targetFile = $user['image']; // Conserver l'ancienne image si aucune nouvelle n'est téléchargée
    }

    // Mettre à jour les informations de l'utilisateur
    $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, telephone = :telephone, image = :image WHERE id = :id");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telephone', $telephone);
    $stmt->bindParam(':image', $targetFile);
    $stmt->bindParam(':id', $userId);
    
    if ($stmt->execute()) {
        echo "<p style='color: green;'>Profil mis à jour avec succès.</p>";
        // Récupérer à nouveau les informations mises à jour
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "<p style='color: red;'>Erreur lors de la mise à jour.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Utilisateur</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Styles ici... */
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-container">
            <h1>Bienvenue, <?php echo htmlspecialchars($user['name']); ?> !</h1>

            <div class="profile-info" id="profileInfo">
                <div class="profile-photo">
                    <img src="<?php echo !empty($user['image']) ? htmlspecialchars($user['image']) : 'default-profile.png'; ?>" alt="Photo de profil" width="150" height="150">
                </div>
                <p><strong>Nom :</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                <p><strong>Email :</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($user['telephone']); ?></p>
            </div>

            <button id="editProfileBtn">Modifier</button>

            <div class="upload-section" id="uploadSection" style="display:none;">
                <h3>Modifier votre profil</h3>
                <form id="editProfileForm" method="POST" enctype="multipart/form-data">
                    <label for="name">Nom :</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required><br>
                    <label for="email">Email :</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>
                    <label for="telephone">Téléphone :</label>
                    <input type="text" name="telephone" value="<?php echo htmlspecialchars($user['telephone']); ?>" required><br>
                    <label for="profile_image">Photo de profil :</label>
                    <input type="file" name="profile_image" accept="image/*"><br>
                    <button type="submit">Mettre à jour</button>
                </form>
            </div>

            <script>
                const editProfileBtn = document.getElementById('editProfileBtn');
                const profileInfo = document.getElementById('profileInfo');
                const uploadSection = document.getElementById('uploadSection');

                editProfileBtn.addEventListener('click', () => {
                    profileInfo.style.display = 'none'; // Masquer les informations de profil
                    uploadSection.style.display = 'block'; // Afficher le formulaire de modification
                });
            </script>
        </div>
    </div>
</body>
</html>
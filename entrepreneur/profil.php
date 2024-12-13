<?php
// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Démarrage de la session
ob_start();
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

// Récupérer les informations de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $userId, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier si l'utilisateur existe
if (!$user) {
    die("Utilisateur introuvable.");
}

// Gestion de la mise à jour des informations de l'utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $date_naissance = $_POST['date_naissance'] ?? '';
    $imagePath = $user['image']; // Par défaut, garder l'image actuelle

    // Vérification et validation des champs
    if (!empty($_FILES['profile_image']['name'])) {
        $imageName = time() . '_' . basename($_FILES['profile_image']['name']);
        $targetDirectory = '../entrepreneur/uploads/';
        $targetFilePath = $targetDirectory . $imageName;

        // Vérifie que le fichier est une image
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($fileType), $allowedTypes)) {
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFilePath)) {
                $imagePath = 'entrepreneur/uploads/' . $imageName;
            } else {
                $error = "Erreur lors du téléchargement de l'image.";
            }
        } else {
            $error = "Format de fichier non pris en charge (uniquement jpg, jpeg, png, gif).";
        }
    }

    // Mettre à jour les informations dans la base de données
    $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, telephone = :telephone, genre = :genre, date_naissance = :date_naissance, image = :image WHERE id = :id");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telephone', $telephone);
    $stmt->bindParam(':genre', $genre);
    $stmt->bindParam(':date_naissance', $date_naissance);
    $stmt->bindParam(':image', $imagePath);
    $stmt->bindParam(':id', $userId);

    if ($stmt->execute()) {
        header("Location: profil.php");
        exit;
    } else {
        $error = "Une erreur est survenue lors de la mise à jour de votre profil.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil Utilisateur</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .edit-form-container {
            display: none;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 130vh;
            padding: 20px;
            margin-top: -70px;
            margin-left: 300px;
        }

        .profile-container {
            border-radius: 12px;
            padding: 40px;
            transition: all 0.3s ease-in-out;
            width: 1000px;
        }

        .profile-container h1 {
            font-size: 30px;
            color: #072A40;
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .profile-photo {
            text-align: center;
            margin-bottom: 25px;
        }

        .profile-photo img {
            border-radius: 50%;
            width: 160px;
            height: 160px;
            object-fit: cover;
            border: 4px solid #072A40;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-info {
            margin-bottom: 30px;
        }

        .profile-info p {
            font-size: 16px;
            color: #072A40;
            margin-bottom: 15px;
        }

        button {
            background-color: #072A40;
            color: #fff;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 8px;
            width: 100%;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #072A40;
        }

        form input[type="text"],
        form input[type="email"],
        form input[type="file"],
        form select,
        form input[type="date"] {
            width: 100%;
            padding: 14px 18px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
            background-color: #f9f9f9;
            transition: all 0.3s ease;
        }

        form input[type="text"]:focus,
        form input[type="email"]:focus,
        form select:focus,
        form input[type="date"]:focus {
            border-color: #007bff;
            background-color: #ffffff;
        }

        .error {
            color: red;
            font-size: 14px;
            text-align: center;
            margin-top: 15px;
        }

        #edit-btn {
            margin-top: 25px;
            background-color: #18B7BE;
            color: #072A40;
            border-radius: 8px;
            width: 100%;
            padding: 12px 0;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        #edit-btn:hover {
            background-color: #072A40;
            color: #18B7BE;
        }

        @media (max-width: 600px) {
           .container, .profile-container {
                padding: 20px;
                width: 100%;
                max-width: none;
                margin-left:0px;
            }

            form input[type="text"],
            form input[type="email"],
            form input[type="file"],
            form button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="profile-container">
        <h1>Profil</h1>

        <!-- Affiche l'image de profil -->
        <div class="profile-photo">
            <img src="<?php echo !empty($user['image']) ? '../' . htmlspecialchars($user['image']) : 'default-profile.png'; ?>" alt="Photo de profil" width="150" height="150">
        </div>

        <!-- Affichage des informations -->
        <div class="profile-info" id="info-view">
            <p><strong>Nom:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Téléphone:</strong> <?php echo htmlspecialchars($user['telephone']); ?></p>
            <p><strong>Genre:</strong> <?php echo htmlspecialchars($user['genre']); ?></p>
            <p><strong>Date de Naissance:</strong> <?php echo htmlspecialchars($user['date_naissance']); ?></p>
        </div>

        <!-- Bouton Modifier pour afficher le formulaire -->
        <button id="edit-btn" onclick="showEditForm()">
            <i class="fas fa-edit"></i> Modifier
        </button>

        <!-- Formulaire de modification -->
        <div class="edit-form-container" id="edit-form-container">
            <form id="edit-form" method="POST" enctype="multipart/form-data">
                <input type="text" name="name" placeholder="Nom" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                <input type="text" name="telephone" placeholder="Téléphone" value="<?php echo htmlspecialchars($user['telephone']); ?>" required>
                <select name="genre" required>
                    <option value="" disabled <?= empty($user['genre']) ? 'selected' : '' ?>>Sélectionnez votre genre</option>
                    <option value="Homme" <?= $user['genre'] === 'Homme' ? 'selected' : '' ?>>Homme</option>
                    <option value="Femme" <?= $user['genre'] === 'Femme' ? 'selected' : '' ?>>Femme</option>
                </select>
                <input type="date" name="date_naissance" placeholder="Date de Naissance" value="<?php echo htmlspecialchars($user['date_naissance']); ?>" required>
                <h3>Modifier la photo de profil</h3>
                <input type="file" name="profile_image" accept="image/*">
                <button type="submit">Enregistrer les modifications</button>
            </form>
        </div>

        <!-- Affiche les erreurs -->
        <?php if (isset($error)) : ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>
</div>

<script>
    // Fonction pour afficher le formulaire de modification
    function showEditForm() {
        document.getElementById("info-view").style.display = "none";  // Masquer les informations actuelles
        document.getElementById("edit-btn").style.display = "none";  // Masquer le bouton Modifier
        document.getElementById("edit-form-container").style.display = "block";  // Afficher le formulaire de modification
    }
</script>
<script src="sidebar.js"></script>

</body>
</html>
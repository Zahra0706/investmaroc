<?php
// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Récupérer les informations de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $userId, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier si l'utilisateur existe
if (!$user) {
    die("Utilisateur introuvable.");
}

// Récupérer la date de naissance de l'utilisateur
$dateNaissance = $user['date_naissance'];

// Calculer l'âge
$datetimeNaissance = new DateTime($dateNaissance);
$today = new DateTime();
$age = $today->diff($datetimeNaissance)->y;

// Gestion de la mise à jour des informations de l'utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $date_naissance = $_POST['date_naissance'] ?? '';
    $imagePath = $user['image']; // Conserver l'image actuelle par défaut

    // Vérification et validation des champs
    if (!empty($_FILES['profile_image']['name'])) {
        $imageName = time() . '_' . basename($_FILES['profile_image']['name']);
        $targetDirectory = '../entrepreneur/uploads/';
        $targetFilePath = $targetDirectory . $imageName;

        // Vérifie que le fichier est une image
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($fileType), $allowedTypes)) {
            // Déplacer l'image dans le dossier "uploads"
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFilePath)) {
                $imagePath = 'entrepreneur/uploads/' . $imageName; // Chemin relatif
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Utilisateur</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
     
            background-color:white;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #072A40;
            color: #fff;
            padding-top: 20px;
            position: fixed;
        }
        .menu {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .menu li {
            border-bottom: 1px solid #073a50;
        }
        .menu a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: #ecf0f1;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .menu a:hover {
            background-color: #18B7BE;
        }
        .menu a.active {
            background-color: #18B7BE !important;
            color: white !important;
        }
        .menu i{
            padding-right: 10px;
            font-size: 20px;
        }
        .container {
            margin-left: 250px;
            width: calc(100% - 250px);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f9f9f9;
            padding: 20px;
        }
        .profile-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }
        .profile-photo img {
            border-radius: 50%;
            width: 160px;
            height: 160px;
            object-fit: cover;
            border: 4px solid #072A40;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
            background-color: #18B7BE;
        }
        .edit-form-container {
            display: none;
            margin-top: -30px;
        }
        form input[type="text"],
        form input[type="email"],
        form select,
        form input[type="date"],
        form input[type="file"] {
            width: 100%;
            padding: 14px 18px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
            background-color: #f9f9f9;
        }
        form button {
            background-color: #072A40;
            border-radius: 8px;
            width: 100%;
            padding: 14px;
            margin-top: 20px;
            color: #18B7BE;
        }
        form button:hover {
            background-color: #18B7BE;
            color: #072A40;
        }
        .error {
            color: red;
            font-size: 14px;
            text-align: center;
            margin-top: 15px;
        }
        #edit-btn {
            background-color: #18B7BE;
            color: #072A40;
        }
        #edit-btn:hover {
            background-color: #072A40;
            color: #18B7BE;
        }
        /* Responsive */
        @media (max-width: 600px) {
            .sidebar {
                display: none;
            }
            .container {
                margin-left: 0;
                width: 100%;
            }
            .profile-container {
                padding: 20px;
                max-width: none;
            }
            form input[type="text"],
            form input[type="email"],
            form input[type="file"],
            form input[type="date"],
            form select,
            form button {
                font-size: 14px;
            }


            #menu-toggle {
                display: block;
                position: absolute;
                top: 10px;
                left: 10px;
                background-color: #18B7BE;
                color: white;
                border: none;
                padding: 10px 15px;
                border-radius: 5px;
                cursor: pointer;
            }
        }
        #menu-toggle {
    display: none; /* Masqué par défaut */
    position: fixed; /* Fixé à l'écran */
    top: 20px; /* Ajustez la position verticale */
    left: 20px; /* Positionné à gauche */
    width: 50px; /* Largeur du bouton */
    height: 50px; /* Hauteur du bouton */
    background-color: #18B7BE; /* Couleur de fond */
    color: white; /* Couleur de l'icône */
    border: none; /* Pas de bordure */
    border-radius: 50%; /* Forme circulaire */
    cursor: pointer; /* Curseur en forme de main */
    display: flex; /* Flex pour centrer l'icône */
    justify-content: center; /* Centrer horizontalement */
    align-items: center; /* Centrer verticalement */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Ombre du bouton */
    z-index: 1000; /* Pour s'assurer qu'il est au-dessus des autres éléments */
}

        @media (max-width: 600px) {
            .sidebar {
                display: none; /* Masquer le menu par défaut */
            }
            #menu-toggle {
                display: block; /* Afficher le bouton sur mobile */
            } #menu-toggle {
        display: flex; /* Afficher le bouton sur mobile */
    }
            
        }
    </style>
</head>
<body>
<button class="toggle-btn" id="menu-toggle" onclick="toggleMenu()"><i class="fas fa-bars"></i></button>

<div class="sidebar">
        <div class="logo">
            <img src="logo.png" alt="Logo" style="width: 100%; height: auto;">
        </div>
        <ul class="menu">
            <li><a href="profil.php"><i class="fas fa-user-circle"></i> Profil</a></li>
            <li><a href="investisseurs.php"><i class="fas fa-handshake"></i> Investisseurs</a></li>
            <li><a href="entrepreneurs.php"><i class="fas fa-briefcase"></i> Entrepreneurs</a></li>
            <li><a href="projets.php"><i class="fas fa-list"></i> Projets</a></li>
            <li><a href="demande_investissement.php"><i class="fas fa-clipboard-list"></i> Demandes d'Investissement</a></li>
            <li><a href="collaborations.php"><i class="fas fa-users"></i> Collaborations</a></li>
            <li><a href="../deconnexion.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
        </ul>
    </div>
    <div class="container">
        <div class="profile-container">
            <h1>Profil</h1>
            <div class="profile-photo">
                <img src="<?php echo !empty($user['image']) ? '../' . htmlspecialchars($user['image']) : 'default-profile.png'; ?>" alt="Photo de profil">
            </div>
            <div class="profile-info" id="info-view">
                <p><strong>Nom:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Téléphone:</strong> <?php echo htmlspecialchars($user['telephone']); ?></p>
                <p><strong>Genre:</strong> <?php echo htmlspecialchars($user['genre']); ?></p>
                <p><strong>Date de Naissance:</strong> <?php echo htmlspecialchars($user['date_naissance']); ?></p>
                <p><strong>Âge:</strong> <?php echo $age; ?> ans</p>
                <p><strong>Rôle:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
            </div>
            <button id="edit-btn" onclick="showEditForm()">
                <i class="fas fa-edit"></i> Modifier
            </button>
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
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function showEditForm() {
            document.getElementById("info-view").style.display = "none";
            document.getElementById("edit-btn").style.display = "none";
            document.getElementById("edit-form-container").style.display = "block";
        }

        function toggleMenu() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('active'); // Toggle la classe active pour afficher/masquer le menu
            sidebar.style.display = sidebar.style.display === 'none' || !sidebar.style.display ? 'block' : 'none'; // Affiche ou masque la sidebar
        }

        const menuLinks = document.querySelectorAll('.menu a');
        function setActiveLink() {
            const currentPath = window.location.pathname;
            menuLinks.forEach(link => {
                const linkPath = new URL(link.href).pathname;
                if (currentPath === linkPath) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        }

        window.addEventListener('load', setActiveLink);
    </script>
</body>
</html>
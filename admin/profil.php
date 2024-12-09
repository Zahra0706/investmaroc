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


        .edit-form-container {
            display: none;
       }

  

.container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 20px;
    margin-top: -150px;
    margin-left: 300px; /* Ajoute cette ligne */

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

.edit-form-container {
    display: none;
    margin-top: -30px;
}

form input[type="text"],
form input[type="email"],
form input[type="file"] {
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
form input[type="email"]:focus {
    border-color: #007bff;
    background-color: #ffffff;
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
    margin-top: 25px;
    background-color:  #18B7BE;
    color:#072A40;
    border-radius: 8px;
    width: 100%;
    padding: 12px 0;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

#edit-btn i {
    margin-right: 10px;  /* Espacement entre l'icône et le texte */
    font-size: 18px;
}

#edit-btn:hover {
    background-color: #072A40;
    color: #18B7BE;
}

/* Responsive pour mobile */
@media (max-width: 600px) {
    .profile-container {
        padding: 20px;
        width: 100%;  /* Rendre le profil réactif sur mobile */
        max-width: none; /* Supprimer la limite de 900px */
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
    <!-- Barre latérale -->
    <div class="sidebar">
        <div class="logo">
            <h2>Admin Dashboard</h2>
        </div>
        <ul class="menu">
            <li>
                <a href="profil.php">
                    <i class="fas fa-user-circle"></i> Profil
                </a>
            </li>
            <li>
                <a href="investisseurs.php">
                    <i class="fas fa-handshake"></i> Investisseurs
                </a>
            </li>
            <li>
                <a href="entrepreneurs.php">
                    <i class="fas fa-briefcase"></i> Entrepreneurs
                </a>
            </li>
            <li>
                <a href="projets.php">
                    <i class="fas fa-list"></i> Projets
                </a>
            </li>
            <li>
                <a href="collaborations.php">
                    <i class="fas fa-users"></i> Collaborations
                </a>
            </li>
            <li>
                <a href="../deconnexion.php">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </li>
        </ul>
    </div>

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

</body>
</html>

<?php
session_start();

// Vérifier si l'utilisateur est administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Vous devez être connecté en tant qu'administrateur pour accéder à cette page.");
    exit;
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
    exit;
}

// Récupérer l'ID de l'investisseur
$investor_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Récupérer les détails de l'investisseur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id AND role = 'investor'");
$stmt->bindParam(':id', $investor_id, PDO::PARAM_INT);
$stmt->execute();
$investor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$investor) {
    die("Investisseur non trouvé.");
    exit;
}

// Calculer l'âge
$date_naissance = new DateTime($investor['date_naissance']);
$aujourdhui = new DateTime();
$age = $aujourdhui->diff($date_naissance)->y;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'Investisseur</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            margin: 0;
            padding: 20px;
        }
        .menu i{
            padding-right:20px;
            font-size:20px;
        }
        h1 {
            color: #072A40;
            text-align: center;
            margin-bottom: 20px;
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
            font-size: 1rem;
            transition: background-color 0.3s;
        }
        .menu a:hover {
            background-color: #18B7BE;
        }
    
        .details-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        .details-container p {
            font-size: 1.1rem;
            margin: 10px 0;
            line-height: 1.5;
        }
        .details-container strong {
            color: #18B7BE;
        }
        .details-container img {
            border-radius: 50%;
            margin-top: 10px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #18B7BE;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
            transition: background-color 0.3s;
        }
        .back-link:hover {
            background-color: #16a7b8;
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
        display: none; /* Masquer la sidebar par défaut */
    }
    .sidebar.active {
        display: block; /* Afficher la sidebar quand active */
    }
    h1{
        text-align:center;
        margin-top:55px;
    }
}
    </style>
</head>
<body>
<button id="menu-toggle" onclick="toggleMenu()"><i class="fas fa-bars"></i></button>

   <!-- Barre latérale -->
   <div class="sidebar">
    <div class="logo">
        <img src="logo.png" alt="Logo" style="width: 100%; height: auto;">
    </div>
        <ul class="menu">
            <li><a href="profil.php"><i class="fas fa-user-circle"></i> Profil</a></li>
            <li><a href="investisseurs.php"><i class="fas fa-handshake"></i> Investisseurs</a></li>
            <li><a href="entrepreneurs.php"><i class="fas fa-briefcase"></i> Entrepreneurs</a></li>
            <li><a href="projets.php"><i class="fas fa-list"></i> Projets</a></li>
            <li>
                <a href="demande_investissement.php">
                <i class="fas fa-clipboard-list"></i> Demandes d'Investissement
                </a>
            </li>
            <li><a href="collaborations.php"><i class="fas fa-users"></i> Collaborations</a></li>
            <li><a href="../deconnexion.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
        </ul>
    </div>

    <h1>Détails de l'Investisseur</h1>
    <div class="details-container">
    <?php if (!empty($investor['image'])): ?>
            <img src="<?= htmlspecialchars('../' . $investor['image']) ?>" alt="Image" style="width: 100px; height: 100px;">
        <?php else: ?>
            <img src="default-avatar.png" alt="Default Avatar" style="width: 100px; height: 100px;">
        <?php endif; ?>
        <p><strong>Nom :</strong> <?= htmlspecialchars($investor['name']) ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars($investor['email']) ?></p>
        <p><strong>Téléphone :</strong> <?= htmlspecialchars($investor['telephone']) ?></p>
        <p><strong>Genre :</strong> <?= htmlspecialchars($investor['genre']) ?></p>
        <p><strong>Âge :</strong> <?= $age ?> ans</p>
        
        
        <a href="investisseurs.php" class="back-link">Retour à la liste des investisseurs</a>
    </div>
    <script>
        function toggleMenu() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('active'); 
            if (sidebar.style.display === 'block') {
                sidebar.style.display = 'none';
            } else {
                sidebar.style.display = 'block';
            }
        }
    </script>
</body>
</html>
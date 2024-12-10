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
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connexion à la base de données échouée : " . $e->getMessage());
}

// Récupérer les entrepreneurs
$stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'entrepreneur'");
$stmt->execute();
$entrepreneurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Entrepreneurs</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
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
        .container {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            background-color: #f9f9f9;
        }
        .table-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #072A40;
            color: white;
        }
        table img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 8px 15px;
            background-color: #18B7BE;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
        }
        .btn:hover {
            background-color: #16a7b8;
        }
    </style>
</head>
<body>
    <!-- Barre latérale -->
    <div class="sidebar">
    <div class="logo">
        <img src="logo.png" alt="Logo" style="width: 100%; height: auto;">
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
                <a href="demande_investissement.php">
                <i class="fas fa-clipboard-list"></i> Demandes d'Investissement
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

    <!-- Contenu principal -->
    <div class="container">
        <h1>Liste des Entrepreneurs</h1>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>E-mail</th>
                        <th>Téléphone</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($entrepreneurs as $entrepreneur): ?>
                        <tr>
                            <td>
                            <img src="<?php echo htmlspecialchars('../' . $entrepreneur['image']); ?>" alt="Image">
                            </td>
                            <td><?php echo htmlspecialchars($entrepreneur['name']); ?></td>
                            <td><?php echo htmlspecialchars($entrepreneur['email']); ?></td>
                            <td><?php echo htmlspecialchars($entrepreneur['telephone']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

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

// Récupérer les investisseurs
$stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'investor' ORDER BY id DESC");
$stmt->execute();
$investors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Investisseurs</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            display: flex;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #072A40;
            color: #fff;
            padding-top: 20px;
            position: fixed;
            transition: transform 0.3s ease;
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

        h1 {
            margin: 20px 0;
        }

        .container {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            background-color: #f9f9f9;
            transition: margin-left 0.3s ease;
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
            text-decoration: none;
        }

        .btn:hover {
            background-color: #16a7b8;
        }

        .menu i {
            padding-right: 20px;
            font-size: 20px;
        }

        .menu a.active {
            background-color: #18B7BE !important;
            color: white !important;
        }

        .search-container {
            position: relative;
            margin-bottom: 20px;
        }

        #search {
            padding: 10px 40px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        #search:focus {
            border-color: #18B7BE;
            outline: none;
        }

        #search::placeholder {
            color: #888;
        }

        .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            font-size: 18px;
            pointer-events: none;
        }

        #menu-toggle {
            display: none; /* Masqué par défaut sur PC */
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
                display: none; /* Masquer la sidebar par défaut sur mobile */
            }
            .sidebar.active {
                display: block; /* Afficher la sidebar quand active */
            }
            .container {
                margin-left: 0; /* Pas de marge lorsque la sidebar est masquée */
                width: 700px; /* Largeur complète */
            }
            h1 {
                text-align: center; /* Centrer le titre */
            }
            #menu-toggle {
                display: flex; /* Afficher le bouton sur mobile */
            }
        }
    </style>
</head>
<body>
    <!-- Bouton de basculement -->
    <button id="menu-toggle" onclick="toggleMenu()"><i class="fas fa-bars"></i></button>
  
    <!-- Barre latérale -->
    <div class="sidebar" id="sidebar">
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

    <!-- Contenu principal -->
    <div class="container" id="container">
        <h1>Liste des Investisseurs</h1>
        <div class="search-container">
            <input type="text" id="search" placeholder="Rechercher par nom" oninput="searchInvestors()">
            <i class="fas fa-search search-icon"></i>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>E-mail</th>
                        <th>Téléphone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="results">
                    <?php foreach ($investors as $investor): ?>
                        <tr>
                            <td>
                                <?php if (!empty($investor['image'])): ?>
                                    <img src="<?php echo htmlspecialchars('../' . $investor['image']); ?>" alt="Image">
                                <?php else: ?>
                                    <img src="default-avatar.png" alt="Default Avatar">
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($investor['name']); ?></td>
                            <td><?php echo htmlspecialchars($investor['email']); ?></td>
                            <td><?php echo htmlspecialchars($investor['telephone']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="details_investor.php?id=<?= $investor['id'] ?>" class="btn">
                                        <i class="fas fa-info-circle"></i> Afficher les détails
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Fonction pour basculer le menu
        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active'); // Ajoute ou enlève la classe active
        }

        // Fonction pour rechercher les investisseurs
        function searchInvestors() {
            const searchTerm = document.getElementById('search').value;

            // Créer une requête AJAX
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'search_investors.php?search=' + encodeURIComponent(searchTerm), true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Mettre à jour le tableau avec les résultats
                    document.getElementById('results').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Récupérer tous les liens du menu
        const menuLinks = document.querySelectorAll('.menu a');

        // Fonction pour vérifier l'URL actuelle
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

        // Exécuter la fonction lors du chargement de la page
        window.addEventListener('load', setActiveLink);
    </script>
</body>
</html>
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

// Récupérer les collaborations
$query = "
    SELECT 
        collaborations.id AS collaboration_id,
        investors.name AS investor_name,
        entrepreneurs.name AS entrepreneur_name,
        projects.title AS project_title,
        collaborations.date_collaboration
    FROM collaborations
    JOIN users AS investors ON collaborations.investor_id = investors.id
    JOIN users AS entrepreneurs ON collaborations.entrepreneur_id = entrepreneurs.id
    JOIN projects ON collaborations.project_id = projects.id
    ORDER BY collaborations.date_collaboration DESC
";
$stmt = $pdo->prepare($query);
$stmt->execute();
$collaborations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Collaborations</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
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
        .date {
            font-size: 0.9rem;
            color: gray;
        }
        .menu i{
            padding-right:20px;
            font-size:20px;
        }
        .menu a.active {
      background-color: #18B7BE !important; /* Bleu pour l'élément actif */
      color: white !important; /* Texte en blanc */
    }
    .btn {
    display: inline-flex;
    align-items: center;
    padding: 10px 15px;
    background-color: #072A40; /* Couleur de fond */
    color: #fff; /* Couleur du texte */
    text-decoration: none; /* Supprime le soulignement */
    border-radius: 5px; /* Coins arrondis */
    font-size: 1rem; /* Taille de la police */
    transition: background-color 0.3s, transform 0.2s; /* Animation au survol */
}

.btn:hover {
    background-color: #18B7BE; /* Couleur au survol */
    transform: translateY(-2px); /* Effet d'élévation */
}

.btn i {
    margin-right: 8px; /* Espace entre l'icône et le texte */
    font-size: 1.2rem; /* Taille de l'icône */
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
    .container {
        margin:0;
        width:700px
    }
    h1{
        text-align:center;
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
                <a href="collaborations.php" class="active">
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
        <h1>Liste des Collaborations</h1>

        <div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Nom de l'Investisseur</th>
                <th>Nom de l'Entrepreneur</th>
                <th>Projet</th>
                <th>Date</th>
                <th>Actions</th> <!-- Nouvelle colonne pour les actions -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($collaborations as $collab): ?>
                <tr>
                    <td><?php echo htmlspecialchars($collab['investor_name']); ?></td>
                    <td><?php echo htmlspecialchars($collab['entrepreneur_name']); ?></td>
                    <td><?php echo htmlspecialchars($collab['project_title']); ?></td>
                    <td class="date"><?php echo htmlspecialchars($collab['date_collaboration']); ?></td>
                    <td>
                        <a href="details_collaboration.php?id=<?php echo $collab['collaboration_id']; ?>" class="btn">
                            <i class="fas fa-info-circle"></i> Afficher les détails
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</div>

    <script>
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

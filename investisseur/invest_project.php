<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour accéder à cette page.");
}

// Inclure la connexion à la base de données
include '../entrepreneur/db.php';

$investor_id = $_SESSION['user_id']; // ID de l'investisseur connecté

// Vérifier si l'ID du projet est passé dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID du projet invalide.");
}

$project_id = $_GET['id'];

// Récupérer les informations sur le projet
$stmt = $conn->prepare("SELECT * FROM projects WHERE id = :project_id");
$stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
$stmt->execute();
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    die("Le projet demandé n'existe pas.");
}

// Vérifier si une demande existe déjà pour cet investisseur et ce projet
$checkStmt = $conn->prepare("SELECT * FROM investment_requests WHERE investor_id = :investor_id AND project_id = :project_id");
$checkStmt->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
$checkStmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
$checkStmt->execute();

if ($checkStmt->rowCount() > 0) {
    die("Vous avez déjà soumis une demande pour ce projet. Veuillez patienter pour la réponse.");
}

// Insérer une nouvelle demande d'investissement
$insertStmt = $conn->prepare("INSERT INTO investment_requests (investor_id, entrepreneur_id, project_id, status, created_at) 
                              VALUES (:investor_id, :entrepreneur_id, :project_id, 'pending', NOW())");
$insertStmt->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
$insertStmt->bindParam(':entrepreneur_id', $project['entrepreneur_id'], PDO::PARAM_INT); // ID de l'entrepreneur associé au projet
$insertStmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);

if ($insertStmt->execute()) {
    echo "<p>Votre demande d'investissement a été soumise avec succès. Vous pouvez suivre son statut depuis votre tableau de bord.</p>";
} else {
    echo "<p>Une erreur s'est produite lors de l'envoi de votre demande. Veuillez réessayer plus tard.</p>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Demande d'investissement</title>
  <link rel="stylesheet" href="styles.css">
  <!-- Lien vers Font Awesome pour les icônes -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
      height: 100vh;
    }

    .sidebar {
      width: 250px;
      background-color: #072A40;
      color: #ecf0f1;
      display: flex;
      flex-direction: column;
      height: 100vh; 
      position: fixed; 
      top: 0;
      left: 0;
    }

    .logo {
      text-align: center;
      padding: 20px 0;
      background-color: #072A40;
    }

    .logo img {
      width: 100%;
      height: 100%;
    }

    .menu {
      list-style: none;
      margin: 0;
      padding: 0;
      flex-grow: 1;
    }

    .menu li {
      border-bottom: 1px solid #072A40;
    }

    .menu a {
      display: flex;
      align-items: center;
      padding: 15px 20px;
      text-decoration: none;
      color: #ecf0f1;
      font-size: 1rem;
      transition: background-color 0.3s;
    }

    .menu a:hover {
      background-color: #18B7BE;
    }

    .menu a i {
      margin-right: 15px;
      font-size: 1.2rem;
    }

    .content {
      flex-grow: 1;
      padding: 20px;
      background-color: #f9f9f9;
      margin-left: 250px; 
    }

    h1 {
      color: #333;
    }

    p {
      color: #555;
    }
  </style>
</head>

<body>
  <!-- Barre latérale -->
  <div class="sidebar">
    <div class="logo">
      <img src="logo.png" alt="Logo">
    </div>
    <ul class="menu">
      <li>
        <a href="profil.php">
          <i class="fas fa-user"></i> Mon Profil
        </a>
      </li>
      <li>
        <a href="browse_projects.php">
          <i class="fas fa-hand-holding-usd"></i> Investir dans un Projet
        </a>
      </li>
      <li>
        <a href="mes_demandes.php">
          <i class="fas fa-file-signature"></i> Mes demandes d'investissement
        </a>
      </li>
      <li>
        <a href="my_investments.php">
          <i class="fas fa-chart-line"></i> Mes Investissements
        </a>
      </li>
      <li>
        <a href="resources.php">
          <i class="fas fa-book"></i> Ressources & Conseils
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
  <div class="content">
    <h1>Demande d'investissement</h1>
    <p>Votre demande d'investissement a été soumise avec succès. Vous pouvez suivre son statut depuis votre tableau de bord.</p>
  </div>
</body>
</html>

<?php
session_start();
include 'db.php';

// Vérifier si l'utilisateur est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Accès refusé. Cette page est réservée aux administrateurs.");
}

// Vérifier si l'ID de la demande est passé
if (!isset($_POST['request_id'])) {
    die("ID de la demande non spécifié.");
}

$request_id = $_POST['request_id'];

// Récupérer les détails de la demande
$stmt = $conn->prepare("
    SELECT ir.id AS request_id, 
           inv.id AS investor_id, inv.name AS investor_name, inv.email AS investor_email, inv.telephone AS investor_telephone, inv.image AS investor_image,
           ent.id AS entrepreneur_id, ent.name AS entrepreneur_name, ent.email AS entrepreneur_email, ent.telephone AS entrepreneur_telephone, ent.image AS entrepreneur_image,
           p.id AS project_id, p.title AS project_title, p.description AS project_description, p.category AS project_category, p.capital_needed, p.status AS project_status, p.image AS project_image, p.created_at AS project_created_at
    FROM investment_requests ir
    JOIN users inv ON ir.investor_id = inv.id
    JOIN users ent ON ir.entrepreneur_id = ent.id
    JOIN projects p ON ir.project_id = p.id
    WHERE ir.id = :request_id
");
$stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
$stmt->execute();
$details = $stmt->fetch(PDO::FETCH_ASSOC);
$images = !empty($details['project_image']) ? explode(",", $details['project_image']) : [];

// Vérifier si les détails existent
if (!$details) {
    die("Détails introuvables pour cette demande.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Demande</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background-color: #f4f4f4;
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
        .menu i{
            padding-right:20px;
            font-size:20px;
        }
        .details-container {
            margin-left: 260px; /* Space for sidebar */
            padding: 20px;
            width: calc(100% - 260px);
        }
        .details {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
        }
        .section {
            flex: 1 1 calc(50% - 20px);
            padding: 20px;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .section:hover {
            transform: translateY(-2px);
        }
        h2 {
            margin-top: 0;
            color: #072A40;
            font-size: 1.5rem;
        }
        img {
            width: 30%;
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .back-btn {
            padding: 10px 15px;
            background-color: #072A40;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1rem;
            margin-top: 20px;
            display: inline-block;
            transition: background-color 0.3s;
        }
        .back-btn:hover {
            background-color: #18B7BE;
        }
        .project_informations{
            margin-top:20px
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
            <li><a href="profil.php"><i class="fas fa-user-circle"></i> Profil</a></li>
            <li><a href="investisseurs.php"><i class="fas fa-handshake"></i> Investisseurs</a></li>
            <li><a href="entrepreneurs.php"><i class="fas fa-briefcase"></i> Entrepreneurs</a></li>
            <li><a href="projets.php"><i class="fas fa-list"></i> Projets</a></li>
            <li><a href="demande_investissement.php"><i class="fas fa-clipboard-list"></i> Demandes d'Investissement</a></li>
            <li><a href="collaborations.php"><i class="fas fa-users"></i> Collaborations</a></li>
            <li><a href="../deconnexion.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
        </ul>
    </div>

    <div class="details-container">
        <h1>Détails de la Demande</h1>
        <div class="details">
            <div class="section">
                <h2>Informations sur l'Investisseur</h2>
                <img src="<?= htmlspecialchars($details['investor_image']) ?>" alt="Image de l'investisseur">
                <p><strong>Nom :</strong> <?= htmlspecialchars($details['investor_name']) ?></p>
                <p><strong>Email :</strong> <?= htmlspecialchars($details['investor_email']) ?></p>
                <p><strong>Téléphone :</strong> <?= htmlspecialchars($details['investor_telephone']) ?></p>
            </div>
            <div class="section">
                <h2>Informations sur l'Entrepreneur</h2>
                <img src="<?= htmlspecialchars($details['entrepreneur_image']) ?>" alt="Image de l'entrepreneur">
                <p><strong>Nom :</strong> <?= htmlspecialchars($details['entrepreneur_name']) ?></p>
                <p><strong>Email :</strong> <?= htmlspecialchars($details['entrepreneur_email']) ?></p>
                <p><strong>Téléphone :</strong> <?= htmlspecialchars($details['entrepreneur_telephone']) ?></p>
            </div>
        </div>
        <div class="section project_informations">
            <h2>Informations sur le Projet</h2>
            <div class="project-details">
            <div class="project-images">
            <?php

// Afficher les images du projet
if (!empty($images)) {
    foreach ($images as $image) {
        // Nettoyer le chemin de l'image en supprimant les crochets, guillemets et autres caractères indésirables
        $imagePath = trim($image, ' "[]'); // Supprimer les guillemets, crochets et espaces autour du chemin
        $imagePath = stripslashes($imagePath); // Supprimer les antislashs échappés

        // Afficher le chemin complet de l'image pour vérifier

        // Vérification de l'existence de l'image
        $fullImagePath = '../entrepreneur/' . $imagePath;
        if (file_exists($fullImagePath)) {
            echo '<img src="' . htmlspecialchars($fullImagePath) . '" alt="Image">';
        } else {
            echo "L'image n'existe pas : " . $fullImagePath . "<br>"; // Afficher un message d'erreur si l'image n'existe pas
        }
    }
} else {
    echo '<p>Aucune image disponible pour ce projet.</p>';
}
?>




            </div>            <p><strong>Titre :</strong> <?= htmlspecialchars($details['project_title']) ?></p>
            <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($details['project_description'])) ?></p>
            <p><strong>Catégorie :</strong> <?= htmlspecialchars($details['project_category']) ?></p>
            <p><strong>Capital Requis :</strong> <?= htmlspecialchars($details['capital_needed']) ?> €</p>
            <p><strong>Status :</strong> <?= htmlspecialchars($details['project_status']) ?></p>
            <p><strong>Date de Création :</strong> <?= htmlspecialchars($details['project_created_at']) ?></p>
        </div>
        <a href="demande_investissement.php" class="back-btn">Retour</a>
    </div>
</body>
</html>
<?php
session_start();
include 'menu.html';

if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour accéder à cette page.");
}

// Inclure la connexion à la base de données
include '../entrepreneur/db.php';

$investor_id = $_SESSION['user_id'];

// Récupérer toutes les demandes de l'investisseur
$stmt = $conn->prepare("SELECT ir.*, p.title AS project_title, p.category, p.capital_needed, u.name AS entrepreneur_name
                        FROM investment_requests ir
                        JOIN projects p ON ir.project_id = p.id
                        JOIN users u ON ir.entrepreneur_id = u.id
                        WHERE ir.investor_id = :investor_id
                            ORDER BY ir.created_at DESC
");
$stmt->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
$stmt->execute();

$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mapping des statuts en français
$status_mapping = [
    'accepted' => 'Accepté',
    'pending' => 'En attente',
    'rejected' => 'Rejeté'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi de mes Demandes d'Investissement</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5; /* Couleur de fond douce */
            padding-top: 80px; /* Ajustement pour le menu fixe */
        }

        .container {
            max-width: 1000px; /* Largeur maximale pour centrer */
            margin: 0 auto; /* Centrer le conteneur */
            padding: 30px; /* Espacement interne */
            margin-left:240px;
        }

        .card {
            margin-bottom: 20px; /* Espacement entre les cartes */
            border: 1px solid #dee2e6; /* Bordure douce */
            border-radius: 10px; /* Coins arrondis */
        }

        .card-header {
            background-color: #468FAF; /* Couleur d'en-tête */
            color: white; /* Texte blanc */
            font-weight: bold; /* Texte en gras */
        }

        .status-accepted {
            color: #28a745; /* Vert pour accepté */
            font-weight: bold;
        }

        .status-pending {
            color: #ffc107; /* Jaune pour en attente */
            font-weight: bold;
        }

        .status-rejected {
            color: #dc3545; /* Rouge pour rejeté */
            font-weight: bold;
        }
        @media (max-width: 600px) {
            .container {
          
            margin-left:0px;
        }
}
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Suivi de mes Demandes d'Investissement</h1>
        <?php if (count($requests) > 0): ?>
            <?php foreach ($requests as $request): ?>
                <div class="card">
                    <div class="card-header">
                        <?= htmlspecialchars($request['project_title']) ?>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($request['category']) ?></h5>
                        <p class="card-text">Budget Nécessaire: <?= htmlspecialchars($request['capital_needed']) ?> MAD</p>
                        <p class="card-text status-<?= strtolower($request['status']) ?>">
                            Statut: <?= htmlspecialchars($status_mapping[strtolower($request['status'])]) ?>
                        </p>
                        <p class="card-text">Date de Soumission: <?= date('d/m/Y', strtotime($request['created_at'])) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-muted">Vous n'avez soumis aucune demande d'investissement pour le moment.</p>
        <?php endif; ?>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
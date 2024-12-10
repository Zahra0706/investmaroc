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
                        WHERE ir.investor_id = :investor_id");
$stmt->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
$stmt->execute();

$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            background-color: #f8f9fa;
            padding-top: 80px; /* Ajustez si nécessaire pour le menu fixe */
        }

        .table-container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-left:260px;
        }

        .table thead {
            background-color: #007bff;
            color: white;
        }

        .status-approved {
            color: #28a745;
            font-weight: bold;
        }

        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }

        .status-rejected {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container table-container">
        <h1 class="text-center mb-4">Suivi de mes Demandes d'Investissement</h1>
        <?php if (count($requests) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Projet</th>
                            <th>Catégorie</th>
                            <th>Budget Nécessaire</th>
                            
                            <th>Statut</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $request): ?>
                            <tr>
                                <td><?= htmlspecialchars($request['project_title']) ?></td>
                                <td><?= htmlspecialchars($request['category']) ?></td>
                                <td><?= htmlspecialchars($request['capital_needed']) ?> MAD</td>
                                <td class="<?= 'status-' . strtolower($request['status']) ?>">
                                    <?= ucfirst($request['status']) ?>
                                </td>
                                <td><?= date('d/m/Y', strtotime($request['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center text-muted">Vous n'avez soumis aucune demande d'investissement pour le moment.</p>
        <?php endif; ?>
    </div>
    
    <!-- Bootstrap JS (Optionnel si vous utilisez des composants interactifs comme des dropdowns) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

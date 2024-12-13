<?php
session_start();
include 'db.php';

// Vérifier si l'utilisateur est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Accès refusé. Cette page est réservée aux administrateurs.");
}

// Récupérer toutes les demandes
$stmt = $conn->prepare("
    SELECT ir.id AS request_id, 
           inv.name AS investor_name, 
           ent.name AS entrepreneur_name, 
           p.title AS project_title, 
           ir.status, 
           ir.created_at 
    FROM investment_requests ir
    JOIN users inv ON ir.investor_id = inv.id
    JOIN users ent ON ir.entrepreneur_id = ent.id
    JOIN projects p ON ir.project_id = p.id
    ORDER BY ir.created_at DESC
");
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Définir le mapping des statuts
$status_mapping = [
    'pending' => 'En attente',
    'accepted' => 'Accepté',
    'rejected' => 'Rejeté',
];

// Fonction pour traduire le statut
function translateStatus($status) {
    switch ($status) {
        case 'pending':
            return 'En attente';
        case 'accepted':
            return 'Accepté';
        case 'rejected':
            return 'Rejeté';
        default:
            return 'Inconnu';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Demandes d'Investissement</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #072A40;
            color: #fff;
            padding-top: 20px;
            position: fixed;
        }

        .sidebar .logo img {
            width: 100%;
            height: auto;
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

        .menu i {
            padding-right: 20px;
        }

        .menu a:hover {
            background-color: #18B7BE;
        }

        .main-content {
            margin-left: 270px;
            padding: 20px;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #072A40;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            text-align: left;
            padding: 12px 15px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #072A40;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .status-accepted {
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

        p {
            font-size: 1rem;
            color: #555;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
            }

            .main-content {
                margin-left: 0;
                padding: 10px;
            }

            h1 {
                font-size: 1.5rem;
            }

            table {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="logo.png" alt="Logo">
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

    <div class="main-content">
        <h1>Historique des Demandes d'Investissement</h1>
        <?php if (count($requests) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nom de l'Investisseur</th>
                        <th>Projet</th>
                        <th>Entrepreneur</th>
                        <th>Date de Demande</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td><?= htmlspecialchars($request['investor_name']) ?></td>
                            <td><?= htmlspecialchars($request['project_title']) ?></td>
                            <td><?= htmlspecialchars($request['entrepreneur_name']) ?></td>
                            <td><?= htmlspecialchars($request['created_at']) ?></td>
                            <td class="<?= 'status-' . strtolower($request['status']) ?>">
                                <?= htmlspecialchars($status_mapping[strtolower($request['status'])]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune demande trouvée.</p>
        <?php endif; ?>
    </div>
</body>
</html>
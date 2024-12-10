<?php
session_start();
include 'db.php';

// Vérifier si l'utilisateur est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Accès refusé. Cette page est réservée aux administrateurs.");
}

// Récupérer les demandes en attente
$stmt = $conn->prepare("
    SELECT ir.id AS request_id, 
           inv.name AS investor_name, 
           ent.name AS entrepreneur_name, 
           p.title AS project_title, 
           ir.created_at 
    FROM investment_requests ir
    JOIN users inv ON ir.investor_id = inv.id
    JOIN users ent ON ir.entrepreneur_id = ent.id
    JOIN projects p ON ir.project_id = p.id
    WHERE ir.status = 'pending'
");
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Gérer les actions Accepter et Annuler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    if ($action === 'accept') {
        try {
            $conn->beginTransaction();

            // Ajouter à la table collaboration
            $accept_stmt = $conn->prepare("
                INSERT INTO collaborations (investor_id, entrepreneur_id, project_id, date_collaboration)
                SELECT investor_id, entrepreneur_id, project_id, NOW()
                FROM investment_requests WHERE id = :request_id
            ");
            $accept_stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
            $accept_stmt->execute();

            // Mettre à jour le statut de la demande
            $update_stmt = $conn->prepare("UPDATE investment_requests SET status = 'accepted' WHERE id = :request_id");
            $update_stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
            $update_stmt->execute();

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            die("Erreur lors de l'acceptation de la demande : " . $e->getMessage());
        }
    } elseif ($action === 'reject') {
        // Rejeter la demande
        $reject_stmt = $conn->prepare("UPDATE investment_requests SET status = 'rejected' WHERE id = :request_id");
        $reject_stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
        $reject_stmt->execute();
    }

    // Recharger la page après action
    header("Location: demande_investissement.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demandes d'Investissement</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Style pour la barre latérale */
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #072A40;
            color: #fff;
            padding-top: 20px;
            position: fixed;
            font-family: Arial, sans-serif;
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

        /* Style pour le contenu principal */
        .main-content {
            margin-left: 270px;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        h1 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #072A40;
        }

        /* Style pour le tableau */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #f9f9f9;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
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
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f5f5f5;
        }

        /* Style amélioré pour les boutons */
button {
    padding: 10px 15px;
    font-size: 0.9rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s;
    margin-right: 10px; /* Espacement entre les boutons */
}
button:hover {
    transform: translateY(-2px); /* Légère élévation au survol */
}
button[name="action"][value="accept"] {
    background-color: #18B7BE;
    color: white;
}
button[name="action"][value="accept"]:hover {
    background-color: #0e8c93;
}
button[name="action"][value="reject"] {
    background-color: #e74c3c;
    color: white;
}
button[name="action"][value="reject"]:hover {
    background-color: #c0392b;
}
button[name="action"][value="details"] {
    background-color: #007BFF;
    color: white;
}
button[name="action"][value="details"]:hover {
    background-color: #0056b3;
}

        /* Style pour le message d'absence de demandes */
        p {
            font-size: 1rem;
            color: #555;
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
    <div class="main-content">
        <h1>Demandes d'Investissement</h1>
        <?php if (count($requests) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nom de l'Investisseur</th>
                        <th>Projet</th>
                        <th>Entrepreneur</th>
                        <th>Date de Demande</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td><?= htmlspecialchars($request['investor_name']) ?></td>
                            <td><?= htmlspecialchars($request['project_title']) ?></td>
                            <td><?= htmlspecialchars($request['entrepreneur_name']) ?></td>
                            <td><?= htmlspecialchars($request['created_at']) ?></td>
                            <td>
                                <form method="POST" style="display: inline;" action="details_demande.php">
                                    <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
                                    <button type="submit" name="action" value="details">
                                        <i class="fas fa-eye"></i> Afficher les détails
                                    </button>
                                </form>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
                                    <button type="submit" name="action" value="accept">
                                        <i class="fas fa-check"></i> Accepter
                                    </button>
                                </form>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
                                    <button type="submit" name="action" value="reject">
                                        <i class="fas fa-times"></i> Annuler
                                    </button>
                                </form>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune demande en attente.</p>
        <?php endif; ?>
    </div>
</body>
</html>

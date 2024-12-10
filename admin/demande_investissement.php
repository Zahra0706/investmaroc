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
        // Accepter la demande : Ajouter à la table collaboration
        $accept_stmt = $conn->prepare("
            INSERT INTO collaboration (investor_id, entrepreneur_id, project_id, date_collaboration)
            SELECT investor_id, entrepreneur_id, project_id, NOW()
            FROM investment_requests WHERE id = :request_id
        ");
        $accept_stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
        $accept_stmt->execute();

        // Mettre à jour le statut de la demande
        $update_stmt = $conn->prepare("UPDATE investment_requests SET status = 'accepted' WHERE id = :request_id");
        $update_stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
        $update_stmt->execute();
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
</head>
<body>
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
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
                                    <button type="submit" name="action" value="accept">Accepter</button>
                                </form>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
                                    <button type="submit" name="action" value="reject">Annuler</button>
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

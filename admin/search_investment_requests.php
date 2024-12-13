<?php
session_start();
include 'db.php';

// Vérifier si l'utilisateur est un administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Accès refusé. Cette page est réservée aux administrateurs.");
}

// Récupérer les demandes avec recherche
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
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
    AND (inv.name LIKE :search OR ent.name LIKE :search OR p.title LIKE :search)
");
$stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Générer le tableau des résultats
foreach ($requests as $request) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($request['investor_name']) . '</td>';
    echo '<td>' . htmlspecialchars($request['project_title']) . '</td>';
    echo '<td>' . htmlspecialchars($request['entrepreneur_name']) . '</td>';
    echo '<td>' . htmlspecialchars($request['created_at']) . '</td>';
    echo '<td>';
    echo '<div style="display: flex; gap: 10px; justify-content: flex-start;">';
    echo '<form method="POST" action="details_demande.php">';
    echo '<input type="hidden" name="request_id" value="' . $request['request_id'] . '">';
    echo '<button type="submit" name="action" value="details">';
    echo '<i class="fas fa-eye"></i> Afficher les détails';
    echo '</button>';
    echo '</form>';
    echo '<form method="POST">';
    echo '<input type="hidden" name="request_id" value="' . $request['request_id'] . '">';
    echo '<button type="submit" name="action" value="accept">';
    echo '<i class="fas fa-check"></i> Accepter';
    echo '</button>';
    echo '</form>';
    echo '<form method="POST">';
    echo '<input type="hidden" name="request_id" value="' . $request['request_id'] . '">';
    echo '<button type="submit" name="action" value="reject">';
    echo '<i class="fas fa-times"></i> Annuler';
    echo '</button>';
    echo '</form>';
    echo '</div>';
    echo '</td>';
    echo '</tr>';
}
?>
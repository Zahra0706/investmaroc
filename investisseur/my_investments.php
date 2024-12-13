<?php
session_start();
include '../entrepreneur/db.php';
include 'menu.html';

// Vérifier si l'utilisateur est connecté et est un investisseur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'investor') {
    die("Accès refusé. Cette page est réservée aux investisseurs.");
}

$investor_id = $_SESSION['user_id']; // ID de l'investisseur connecté

// Récupérer les collaborations pour cet investisseur
$stmt = $conn->prepare("SELECT 
        c.id AS collaboration_id,
        ent.name AS entrepreneur_name,
        p.title AS project_title,
        c.date_collaboration
    FROM collaborations c
    JOIN users ent ON c.entrepreneur_id = ent.id
    JOIN projects p ON c.project_id = p.id
    WHERE c.investor_id = :investor_id
    ORDER BY c.date_collaboration DESC");
$stmt->bindParam(':investor_id', $investor_id, PDO::PARAM_INT);
$stmt->execute();
$collaborations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Investissements</title>
    <link rel="stylesheet" href="styles.css"> <!-- Lien vers votre fichier CSS -->
    <style>
        /* styles.css */

/* Style général */
body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f8f9fa;
    color: #333;
}


 h1 {
    margin: 0;
    font-size: 24px;
}

main {
    max-width: 900px;
    margin: 20px auto;
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}
h1{
    text-align:center;
}

/* Table styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);

}

table th, table td {
    text-align: left;
    padding: 10px;
    border: 1px solid #ddd;
}

table th {
    background-color: #072A40;
    color: white;
    font-weight: bold;
}

table tr:nth-child(even) {
    background-color: #f2f2f2;
}

table tr:hover {
    background-color: #e9ecef;
}

/* Bouton de retour */
a.retour {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 20px;
    background-color: #072A40;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    text-align: center;
}

a.retour:hover {
    background-color: #073a50;
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

/* Responsiveness */
@media (max-width: 600px) {
    header h1 {
        font-size: 20px;
    }

    table th, table td {
        font-size: 14px;
    }

    a.retour {
        font-size: 14px;
    }
}
    </style>
</head>
<body>
 
       


    <main>
    <h1>Mes Collaborations</h1>
        <?php if (count($collaborations) > 0): ?>
            <table>
                <thead>
                    <tr>
                        
                        <th>Titre du Projet</th>
                        <th>Date de Collaboration</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($collaborations as $index => $collaboration): ?>
                        <tr>
                            
                            <td><?= htmlspecialchars($collaboration['project_title']) ?></td>
                            <td><?= htmlspecialchars($collaboration['date_collaboration']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune collaboration trouvée.</p>
        <?php endif; ?>

    </main>
</body>
</html>

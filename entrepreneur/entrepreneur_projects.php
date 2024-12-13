<?php

session_start();
include 'menu.php';
// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour voir cette page.");
}

$entrepreneurId = $_SESSION['user_id'];

// Configuration de la base de données
$host = 'localhost';
$dbname = 'investmaroc';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connexion échouée : " . $e->getMessage());
}

// Requête pour récupérer les projets validés d'un entrepreneur
$query = "
    SELECT p.title, c.date_collaboration, c.project_id 
    FROM collaborations c 
    JOIN projects p ON c.project_id = p.id 
    WHERE c.entrepreneur_id = :entrepreneur_id
";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':entrepreneur_id', $entrepreneurId);
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projets Validés</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #072A40;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #18B7BE;
            color: white;
        }
        table tr:hover {
            background-color: #f1f1f1;
        }
        .btn-view-details {
            background-color: #18B7BE;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .btn-view-details:hover {
            background-color: #16a7b8;
        }
        .no-projects {
            text-align: center;
            font-size: 18px;
            color: #555;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Projets Validés à Investir</h1>

    <?php if (!empty($projects)) : ?>
        <table>
            <thead>
                <tr>
                    <th>Nom du Projet</th>
                    <th>Date de Collaboration</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($project['title']); ?></td>
                        <td><?php echo date('d-m-Y', strtotime($project['date_collaboration'])); ?></td>
                        <td>
                            <a href="project_details.php?id=<?php echo $project['project_id']; ?>" class="btn-view-details">
                                Voir Détails
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p class="no-projects">Aucun projet validé pour investissement.</p>
    <?php endif; ?>
</div>

</body>
</html>
<?php
include 'db.php';

$sql = "SELECT * FROM projects ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Liste des Projets</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="content">
    <h1>Liste des Projets</h1>
    <table border="1">
      <tr>
        <th>Titre</th>
        <th>Description</th>
        <th>Budget (€)</th>
        <th>Catégorie</th>
        <th>Créé le</th>
      </tr>
      <?php foreach ($projects as $project): ?>
        <tr>
          <td><?= htmlspecialchars($project['title']) ?></td>
          <td><?= htmlspecialchars($project['description']) ?></td>
          <td><?= htmlspecialchars($project['budget']) ?></td>
          <td><?= htmlspecialchars($project['category']) ?></td>
          <td><?= htmlspecialchars($project['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</body>
</html>

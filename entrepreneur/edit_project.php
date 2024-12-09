<?php
ob_start(); // Activer le tampon de sortie
session_start();
include 'menu.php';

if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour accéder à cette page.");
}

include 'db.php';

// Vérifier si l'ID du projet est passé
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID du projet invalide.");
}

$project_id = $_GET['id'];

// Récupérer les informations du projet actuel
$stmt = $conn->prepare("SELECT * FROM projects WHERE id = :project_id");
$stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
$stmt->execute();
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    die("Le projet demandé n'existe pas.");
}

// Gestion de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? $project['title'];
    $description = $_POST['description'] ?? $project['description'];
    $capital_needed = $_POST['capital_needed'] ?? $project['capital_needed'];
    $category = $_POST['category'];
    $custom_category = $_POST['custom_category'] ?? '';

    if ($category === 'autre' && !empty($custom_category)) {
        $category = $custom_category;
    }

    $images = [];
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
        if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
            $image_name = basename($_FILES['images']['name'][$i]);
            $target_file = $upload_dir . $image_name;
            if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $target_file)) {
                $images[] = $target_file;
            }
        }
    }

    $images_json = !empty($images) ? json_encode($images) : $project['image'];

    $update_stmt = $conn->prepare("
        UPDATE projects 
        SET title = :title, description = :description, capital_needed = :capital_needed, category = :category, image = :image
        WHERE id = :project_id
    ");

    $update_stmt->bindParam(':title', $title);
    $update_stmt->bindParam(':description', $description);
    $update_stmt->bindParam(':capital_needed', $capital_needed);
    $update_stmt->bindParam(':category', $category);
    $update_stmt->bindParam(':image', $images_json);
    $update_stmt->bindParam(':project_id', $project_id);

    if ($update_stmt->execute()) {
        header("Location: project_details.php?id=$project_id");
        exit;
    } else {
        $error_info = $update_stmt->errorInfo();
        echo "<p>Erreur SQL : {$error_info[2]}</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Projet</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function showInputField(select) {
            const customCategoryContainer = document.getElementById('custom-category-container');
            if (select.value === 'autre') {
                customCategoryContainer.style.display = 'block';
            } else {
                customCategoryContainer.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <div class="main-content">
        <h1>Modifier le Projet</h1>
        <form method="POST" enctype="multipart/form-data">
            <label for="title">Titre :</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($project['title']) ?>" required>
            
            <label for="description">Description :</label>
            <textarea id="description" name="description" required><?= htmlspecialchars($project['description']) ?></textarea>
            
            <label for="capital_needed">Budget :</label>
            <input type="number" id="capital_needed" name="capital_needed" value="<?= htmlspecialchars($project['capital_needed']) ?>" required>
            
            <label for="category">Catégorie :</label>
            <select id="category" name="category" required onchange="showInputField(this)">
                <option value="technologie" <?= $project['category'] === 'technologie' ? 'selected' : '' ?>>Technologie</option>
                <option value="santé" <?= $project['category'] === 'santé' ? 'selected' : '' ?>>Santé</option>
                <option value="éducation" <?= $project['category'] === 'éducation' ? 'selected' : '' ?>>Éducation</option>
                <option value="autre">Autre</option>
            </select>
            <div id="custom-category-container" style="display: none; margin-top: 10px;">
                <label for="custom-category">Veuillez préciser la catégorie :</label>
                <input type="text" id="custom-category" name="custom_category" placeholder="Saisissez la catégorie ici">
            </div>
            
            <label for="images">Images du projet (max 3) :</label>
            <input type="file" id="images" name="images[]" accept="image/*" multiple>
            <p>Images actuelles :</p>
            <?php 
            $current_images = json_decode($project['image'], true);
            if ($current_images) {
                foreach ($current_images as $image) {
                    echo "<img src='$image' alt='Image actuelle' style='max-width: 100px; margin-right: 10px;'>";
                }
            }
            ?>

            <button type="submit">Enregistrer les modifications</button>
        </form>
    </div>
</body>
</html>

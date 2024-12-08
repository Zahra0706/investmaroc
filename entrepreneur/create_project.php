<?php 
include 'menu.php'; 
include 'db.php';
session_start();

$message = ""; // Variable pour afficher le message

// Afficher un message basé sur le paramètre dans l'URL
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = "Projet créé avec succès !";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $budget = $_POST['capital_needed'];
    $category = $_POST['category'];

    // Gestion des fichiers médias (plusieurs fichiers)
    $media_paths = [];
    if (isset($_FILES['media']) && count($_FILES['media']['name']) > 0) {
        $target_dir = "uploads/";
        
        foreach ($_FILES['media']['name'] as $key => $file_name) {
            if ($_FILES['media']['error'][$key] == 0) {
                $target_file = $target_dir . basename($file_name);
                if (move_uploaded_file($_FILES['media']['tmp_name'][$key], $target_file)) {
                    $media_paths[] = $target_file; // Ajoute le chemin au tableau
                } else {
                    $message = "Erreur lors du téléchargement du fichier : " . htmlspecialchars($file_name);
                }
            }
        }
    }

    // Convertir les chemins des fichiers en format JSON (pour les enregistrer dans la base)
    $media_paths_json = json_encode($media_paths);

    // Récupérer l'ID de l'utilisateur connecté
    $user_id = $_SESSION['user_id'];

    // Insertion dans la base de données
    try {
        $sql = "INSERT INTO projects (title, description, capital_needed, category, image, entrepreneur_id) 
        VALUES (:title, :description, :capital_needed, :category, :image, :user_id)";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':capital_needed', $budget);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':image', $media_paths_json);
        $stmt->bindParam(':user_id', $user_id); // Correction ici


        if ($stmt->execute()) {
            // Redirection pour éviter la resoumission du formulaire
            header("Location: create_project.php?success=1");
            exit(); // Stopper l'exécution après redirection
        } else {
            $message = "Erreur lors de la création du projet.";
        }
    } catch (PDOException $e) {
        $message = "Erreur : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Créer un Projet</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    .message {
        margin: 20px 0;
        padding: 15px;
        border-radius: 5px;
        font-size: 16px;
        font-weight: bold;
        text-align: center;
    }
    .message.success {
        background-color: #e9f7ef;
        border: 1px solid #27ae60;
        color: #27ae60;
    }
    .message.error {
        background-color: #fbe4e6;
        border: 1px solid #e74c3c;
        color: #e74c3c;
    }
  </style>
</head>
<body>
  <div class="content">
      <h1>Créer un Projet</h1>
      
      <!-- Afficher le message de succès ou d'erreur -->
      <?php if (!empty($message)): ?>
          <div class="message <?php echo strpos($message, 'succès') !== false ? 'success' : 'error'; ?>">
              <?php echo htmlspecialchars($message); ?>
          </div>
      <?php endif; ?>
      
      <!-- Formulaire -->
      <form action="" method="POST" enctype="multipart/form-data">
          <label for="title">Titre du projet :</label>
          <input type="text" id="title" name="title" placeholder="Titre" required>

          <label for="description">Description :</label>
          <textarea id="description" name="description" rows="5" placeholder="Décrivez votre projet" required></textarea>

          <label for="budget">Budget nécessaire (dh) :</label>
          <input type="number" id="budget" name="capital_needed" placeholder="Montant recherché" required>

          <label for="category">Catégorie :</label>
          <select id="category" name="category" required>
              <option value="technologie">Technologie</option>
              <option value="santé">Santé</option>
              <option value="éducation">Éducation</option>
              <option value="autre">Autre</option>
          </select>

          <label for="media">Ajouter des médias (images/vidéos) :</label>
          <input type="file" id="media" name="media[]" accept="image/*,video/*" multiple> <!-- 🚀 Multiple fichiers -->

          <button type="submit" class="btn-submit">Publier</button>
          <button type="button" class="btn-draft">Enregistrer en Brouillon</button>
      </form>
  </div>
</body>
</html>

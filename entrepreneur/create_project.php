<?php include 'menu.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menu Latéral</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  
</head>
<body>
  <div class="content">
    
        <h1>Créer un Projet</h1>
        <form action="create_projectdb.php" method="POST" enctype="multipart/form-data">
          <label for="title">Titre du projet :</label>
          <input type="text" id="title" name="title" placeholder="Titre" required>

          <label for="description">Description :</label>
          <textarea id="description" name="description" rows="5" placeholder="Décrivez votre projet" required></textarea>

          <label for="budget">Budget nécessaire (dh) :</label>
          <input type="number" id="budget" name="budget" placeholder="Montant recherché" required>

          <label for="category">Catégorie :</label>
          <select id="category" name="category" required>
            <option value="technologie">Technologie</option>
            <option value="santé">Santé</option>
            <option value="éducation">Éducation</option>
            <option value="autre">Autre</option>
          </select>

          <label for="media">Ajouter des médias (images/vidéos) :</label>
          <input type="file" id="media"  name="media[]" accept="image/*,video/*" multiple>

          <button type="submit" class="btn-submit">Publier</button>
          <button type="button" class="btn-draft">Enregistrer en Brouillon</button>
        </form>
   
  </div>
</body>
</html>

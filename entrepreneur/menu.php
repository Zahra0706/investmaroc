<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menu Latéral</title>

  <!-- Lien vers Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <!-- Lien vers Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <style>
    /* Styles de la sidebar */
    .sidebar {
      width: 250px;
      height: 100vh;
      background-color: #072A40;
      color: #ecf0f1;
      transition: width 0.3s ease;
      position: relative;
      list-style-type: none; /* Supprime les puces des <li> */

    }

    /* Réduit la largeur de la sidebar lorsqu'elle est réduite */
    .sidebar.collapsed {
      width: 60px;
    }

    /* Styles de la logo */
    .logo {
      text-align: center;
      padding: 20px 0;
      background-color: #072A40;
    }

    .logo img {
      width: 100%;
      height: 100%;
    }

    /* Styles du menu */
    .menu a {
      display: flex;
      align-items: center;
      padding: 15px 20px;
      text-decoration: none;
      color: #ecf0f1;
      transition: background-color 0.3s;
    }

    .menu a:hover {
      background-color: #18B7BE;
    }

    .menu a i {
      margin-right: 15px;
      font-size: 1.2rem;
    }

    .menu span {
      display: inline-block;
      transition: opacity 0.3s ease;
    }

    /* Masquer les textes lorsque la sidebar est réduite */
    .sidebar.collapsed .menu span {
      display: none;
    }

    .sidebar.collapsed .menu a {
      justify-content: center;
    }

    /* Bouton de bascule (toggle) */
    .toggle-btn {
      position: absolute;
      top: 10px;
      right: -20px;
      background-color: #18B7BE;
      color: white;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      cursor: pointer;
      transition: transform 0.3s ease;
    }

    .toggle-btn:hover {
      transform: rotate(180deg);
    }

    .content {
      padding: 20px;
      background-color: #f9f9f9;
      flex-grow: 1;
    }

    /* Styles pour mobile */
    @media (max-width: 768px) {
      .sidebar {
        width: 100%;
        height: auto;
      }

      .sidebar.collapsed {
        width: 100%;
      }

      .content {
        padding-left: 10px;
        padding-right: 10px;
      }

      .toggle-btn {
        top: 10px;
        right: 10px;
      }

      /* Sur mobile, afficher les textes et les icônes par défaut */
      .sidebar .menu span {
        display: inline-block;
      }

      /* Masquer les textes et icônes lorsque la sidebar est réduite sur mobile */
      .sidebar.collapsed .menu span,
      .sidebar.collapsed .menu i {
        display: none;
      }

      /* Masquer le logo lorsque la sidebar est réduite sur mobile */
      .sidebar.collapsed .logo {
        display: none;
      }
    }

    /* Styles supplémentaires pour les éléments du menu sur petits écrans */
    @media (max-width: 576px) {
      .menu a i {
        font-size: 1.5rem;
      }

      .menu a span {
        display: none; /* Masque les textes sur très petit écran */
      }

      .sidebar.collapsed .menu a {
        justify-content: center;
      }
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="d-flex">
    <div class="sidebar" id="sidebar">
      <!-- Bouton de bascule (toggle) -->
    
      <button class="toggle-btn" id="toggle-btn"><i class="fas fa-bars"></i></button>

      <div class="logo">
        <!-- Remplace l'URL de l'image par le chemin de ton logo -->
        <img src="logo.png" alt="Logo">
      </div>
      
      <ul class="menu">
        <li><a href="profil.php"><i class="fas fa-user"></i> <span>Mon Profil</span></a></li>
        <li><a href="create_project.php"><i class="fas fa-plus-circle"></i> <span>Créer un Projet</span></a></li>
        <li><a href="list_projects.php"><i class="fas fa-list"></i> <span>Mes Projets</span></a></li>
        <li><a href="../deconnexion.php"><i class="fas fa-sign-out-alt"></i> <span>Déconnexion</span></a></li>
    </ul>
    </div>

    <!-- Contenu principal -->
    <div class="content">
      <!-- Ici tu peux ajouter ton contenu -->
    </div>
  </div>

  <!-- Lien vers Bootstrap Bundle (inclut Popper.js) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Récupérer les éléments de la page
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggle-btn');
    const icon = toggleBtn.querySelector('i');

    toggleBtn.addEventListener('click', () => {
      // Basculer entre les classes collapsed et expanded
      sidebar.classList.toggle('collapsed');
      
      // Changer l'icône du bouton (flèche de gauche/droite)
      if (sidebar.classList.contains('collapsed')) {
        icon.classList.remove('fa-arrow-left');
        icon.classList.add('fa-arrow-right');
      } else {
        icon.classList.remove('fa-arrow-right');
        icon.classList.add('fa-arrow-left');
      }
    });
  </script>

</body>
</html>

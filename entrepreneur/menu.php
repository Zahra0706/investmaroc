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
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      width: 250px;
      background-color: #072A40;
      color: #ecf0f1;
      overflow-y: auto;
      transition: width 0.3s ease;
      z-index: 1000; /* Toujours au-dessus du contenu principal */
    }

    .sidebar.collapsed {
      width: 60px;
    }

    .sidebar .logo {
      text-align: center;
      padding: 20px 0;
    }

    .sidebar .logo img {
      width: 80%;
      max-width: 100px;
    }

    .menu {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .menu a {
      display: flex;
      align-items: center;
      padding: 15px 20px;
      text-decoration: none;
      color: #ecf0f1;
      transition: background-color 0.3s ease;
    }

    .menu a:hover {
      background-color: #18B7BE;
    }

    .menu a i {
      margin-right: 15px;
      font-size: 1.2rem;
    }

    .menu span {
      transition: opacity 0.3s ease;
    }

    /* Masquer les textes lorsque la sidebar est réduite */
    .sidebar.collapsed .menu span {
      display: none;
    }

    /* Centrer les icônes lorsque la sidebar est réduite */
    .sidebar.collapsed .menu a {
      justify-content: center;
    }

    /* Bouton de bascule (toggle) */
    .toggle-btn {
      position: fixed;
      top: 20px;
      left: 230px;
      z-index: 1100;
      background-color: #18B7BE;
      color: white;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      cursor: pointer;
      transition: left 0.3s ease;
    }

    .sidebar.collapsed + .toggle-btn {
      left: 70px;
    }

    .toggle-btn:hover {
      transform: rotate(180deg);
    }

    /* Styles pour mobile */
    @media (max-width: 768px) {
      .sidebar {
        width: 100%;
        height: auto;
        position: relative;
      }

      .sidebar.collapsed {
        width: 100%;
      }

      .toggle-btn {
        top: 10px;
        left: auto;
        right: 10px;
      }

      .menu a {
        justify-content: flex-start;
      }

      .menu span {
        display: inline-block;
      }

      .sidebar.collapsed .menu span {
        display: none;
      }
    }
  </style>
</head>
<body>
  <div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
      <div class="logo">
        <img src="logo.png" alt="Logo">
      </div>
      <ul class="menu">
        <li><a href="profil.php"><i class="fas fa-user"></i> <span>Mon Profil</span></a></li>
        <li><a href="create_project.php"><i class="fas fa-plus-circle"></i> <span>Créer un Projet</span></a></li>
        <li><a href="list_projects.php"><i class="fas fa-list"></i> <span>Mes Projets</span></a></li>
        <li><a href="entrepreneur_projects.php"><i class="fas fa-briefcase"></i> Projets Validés à Investir</a></li> 

        <li><a href="../deconnexion.php"><i class="fas fa-sign-out-alt"></i> <span>Déconnexion</span></a></li>
      </ul>
    </div>

    <!-- Bouton de bascule (toggle) -->
    <button class="toggle-btn" id="toggle-btn"><i class="fas fa-bars"></i></button>

    
  </div>

  <!-- Lien vers Bootstrap Bundle (inclut Popper.js) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Récupérer les éléments
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggle-btn');
    const icon = toggleBtn.querySelector('i');

    // Ajouter un événement au bouton
    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('collapsed');
      
      // Basculer l'icône entre "bars" et "arrow-right"
      if (sidebar.classList.contains('collapsed')) {
        icon.classList.remove('fa-bars');
        icon.classList.add('fa-arrow-right');
      } else {
        icon.classList.remove('fa-arrow-right');
        icon.classList.add('fa-bars');
      }
    });
  </script>
</body>
</html>
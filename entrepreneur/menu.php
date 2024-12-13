<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Entrepreneur</title>

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
      width: 250px; /* Largeur en mode PC */
      background-color: #072A40;
      color: #ecf0f1;
      overflow-y: auto;
      transition: transform 0.3s ease;
      z-index: 1000;
    }

    .sidebar.collapsed {
      transform: translateY(-100%); /* Masquer vers le haut */
    }

    .sidebar .logo {
    text-align: center;
    padding: 20px 0;
}

.sidebar .logo img {
    width: 100%; /* Ajustez à 100% ou à la taille fixe souhaitée */
    max-width: none; /* Supprime la limite de largeur maximale */
    height: auto; /* Conserve le ratio de l'image */
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

    .menu a.active {
      background-color: #18B7BE;
      color: white;
    }

    .menu a:hover {
      background-color: #18B7BE;
    }

    .menu a i {
      margin-right: 15px;
      font-size: 1.2rem;
    }

    .toggle-btn {
      position: fixed;
      top: 20px;
      left: 10px;
      z-index: 1100;
      background-color: #18B7BE;
      color: white;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: none; /* Masquer par défaut */
      justify-content: center;
      align-items: center;
    }
    .menu a.active {
      background-color: #18B7BE !important; /* Bleu pour l'élément actif */
      color: white !important; /* Texte en blanc */
    }

    @media (max-width: 768px) {
      .sidebar {
        width: 100%; /* Largeur pleine sur mobile */
        transform: translateY(-100%); /* Masquer par défaut */
      }

      .sidebar.active {
        transform: translateY(0); /* Afficher en descendant */
      }

      .toggle-btn {
        display: flex; /* Afficher sur mobile */
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
        <li><a href="profil.php" onclick="hideSidebar()"><i class="fas fa-user"></i> <span>Mon Profil</span></a></li>
        <li><a href="create_project.php" onclick="hideSidebar()"><i class="fas fa-plus-circle"></i> <span>Créer un Projet</span></a></li>
        <li><a href="list_projects.php" onclick="hideSidebar()"><i class="fas fa-list"></i> <span>Mes Projets</span></a></li>
        <li><a href="entrepreneur_projects.php" onclick="hideSidebar()"><i class="fas fa-briefcase"></i> <span>Projets Validés à Investir</span></a></li>
        <li><a href="../deconnexion.php" onclick="hideSidebar()"><i class="fas fa-sign-out-alt"></i> <span>Déconnexion</span></a></li>
      </ul>
    </div>

    <!-- Bouton de bascule (toggle) -->
    <button class="toggle-btn" id="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
  </div>

  <!-- Lien vers Bootstrap Bundle (inclut Popper.js) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const sidebar = document.getElementById('sidebar');

    function toggleSidebar() {
      sidebar.classList.toggle('active');
    }

    function hideSidebar() {
      sidebar.classList.remove('active');
    }

    // Afficher le bouton de toggle seulement sur mobile
    if (window.innerWidth <= 768) {
      document.getElementById('toggle-btn').style.display = 'flex';
    }
  </script>
  <script>
     // Récupérer tous les liens du menu
     const menuLinks = document.querySelectorAll('.menu a');

// Fonction pour vérifier l'URL actuelle
function setActiveLink() {
  const currentPath = window.location.pathname;
  menuLinks.forEach(link => {
    const linkPath = new URL(link.href).pathname;
    if (currentPath === linkPath) {
      link.classList.add('active');
    } else {
      link.classList.remove('active');
    }
  });
}

// Exécuter la fonction lors du chargement de la page
window.addEventListener('load', setActiveLink);
</script>
</body>
</html>
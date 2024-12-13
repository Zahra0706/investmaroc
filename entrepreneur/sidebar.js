// Récupérer les éléments
const sidebar = document.getElementById('sidebar');
const toggleBtn = document.getElementById('toggle-btn');
const menuLinks = document.querySelectorAll('.menu a');

// Vérifie si l'écran est un mobile ou une tablette
function isMobileScreen() {
  return window.innerWidth <= 1024; // Écran de 1024px ou moins
}

// Restaurer l'état de la sidebar au chargement
if (localStorage.getItem('sidebarState') === 'collapsed' && isMobileScreen()) {
  sidebar.classList.add('collapsed');
}

// Basculer la sidebar (fonction active uniquement sur mobile)
toggleBtn.addEventListener('click', () => {
  if (!isMobileScreen()) return; // Empêche le basculement sur PC
  sidebar.classList.toggle('collapsed');
  // Enregistrer l'état dans localStorage
  localStorage.setItem('sidebarState', sidebar.classList.contains('collapsed') ? 'collapsed' : '');
});

// Fonction pour fermer la sidebar lorsqu'un lien est cliqué (seulement sur mobile)
function closeSidebarOnMobile() {
  if (isMobileScreen()) {
    sidebar.classList.add('collapsed');
    localStorage.setItem('sidebarState', 'collapsed');
  }
}

// Appliquer la fermeture automatique au clic sur un lien
menuLinks.forEach(link => {
  link.addEventListener('click', closeSidebarOnMobile);
});

// Fonction pour vérifier l'URL actuelle et activer le lien correspondant
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

// Exécuter la fonction au chargement de la page
window.addEventListener('load', setActiveLink);

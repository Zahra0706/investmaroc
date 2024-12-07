document.addEventListener("DOMContentLoaded", function () {
    // Références des éléments du formulaire
    const form = document.getElementById("signup-form"); // Formulaire d'inscription
    const password = document.getElementById("password"); // Champ mot de passe
    const confirmPassword = document.getElementById("confirmPassword"); // Champ confirmation mot de passe
    const errorMessage = document.getElementById("error-message"); // Message d'erreur

    // Ajout d'un écouteur pour la soumission du formulaire
    form.addEventListener("submit", function (event) {
        // Vérifier si les mots de passe correspondent
        if (password.value.trim() !== confirmPassword.value.trim()) {
            errorMessage.textContent = "Les mots de passe ne correspondent pas."; // Message d'erreur clair
            errorMessage.style.display = "block"; // Affiche le message d'erreur
            event.preventDefault(); // Empêche l'envoi du formulaire
        } else {
            errorMessage.style.display = "none"; // Cache le message d'erreur
        }
    });
});

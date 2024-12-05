document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("signupForm");
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirmPassword");
    const errorMessage = document.getElementById("error-message");

    form.addEventListener("submit", function (event) {
        // Vérifier si les mots de passe correspondent
        if (password.value !== confirmPassword.value) {
            errorMessage.style.display = "block"; // Afficher le message d'erreur
            event.preventDefault(); // Empêcher l'envoi du formulaire
        } else {
            errorMessage.style.display = "none"; // Cacher le message d'erreur
        }
    });
});

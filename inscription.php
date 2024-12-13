<?php
// Configuration de la base de données
$host = 'localhost';
$dbname = 'investmaroc';
$user = 'root';
$pass = '';

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connexion à la base de données échouée : " . $e->getMessage());
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $telephone = htmlspecialchars($_POST['telephone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hachage du mot de passe
    $role = htmlspecialchars($_POST['role']);
    $genre = htmlspecialchars($_POST['genre']);
    $dateNaissance = htmlspecialchars($_POST['date_naissance']);
    $defaultImage = 'images/profil.jpg'; // Chemin par défaut pour l'image

    // Vérifier si l'email existe déjà
    $checkEmail = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $checkEmail->bindParam(':email', $email);
    $checkEmail->execute();

    if ($checkEmail->rowCount() > 0) {
        // Redirection avec un message d'erreur
        header("Location: inscription.html?error=email_taken");
        exit();
    }

    // Insérer dans la base de données avec les nouveaux champs
    $stmt = $pdo->prepare("INSERT INTO users (name, email, telephone, password, role, genre, date_naissance, image) VALUES (:name, :email, :telephone, :password, :role, :genre, :date_naissance, :image)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telephone', $telephone);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':genre', $genre);
    $stmt->bindParam(':date_naissance', $dateNaissance);
    $stmt->bindParam(':image', $defaultImage);

    if ($stmt->execute()) {
        // Redirection avec un message de succès
        header("Location: inscription.html?success=registered");
        exit();
    } else {
        // Redirection avec un message d'erreur générique
        header("Location: inscription.html?error=registration_failed");
        exit();
    }
}
?>
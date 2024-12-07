<?php
require_once 'config.php'; // Fichier contenant la connexion à la BDD

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Vérifier si le code existe et que l'email n'est pas déjà vérifié
    $stmt = $pdo->prepare("SELECT * FROM users WHERE verification_code = :code AND email_verified = 0");
    $stmt->execute([':code' => $code]);

    if ($stmt->rowCount() > 0) {
        // Marquer l'email comme vérifié
        $update = $pdo->prepare("UPDATE users SET email_verified = 1 WHERE verification_code = :code");
        $update->execute([':code' => $code]);
        echo "Votre email a été vérifié avec succès !";
    } else {
        echo "Code invalide ou email déjà vérifié.";
    }
} else {
    echo "Code de vérification manquant.";
}
?>

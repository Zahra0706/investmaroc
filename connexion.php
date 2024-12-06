<?php
// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "investmaroc");

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérification des données soumises
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (!empty($email) && !empty($password)) {
        // Requête pour vérifier l'utilisateur
        $stmt = $conn->prepare("SELECT id, role, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $role, $hashed_password);
            $stmt->fetch();

            // Vérification du mot de passe
            if (password_verify($password, $hashed_password)) {
                // Connexion réussie
                session_start();
                $_SESSION["user_id"] = $id;
                $_SESSION["role"] = $role;

                // Redirection selon le rôle
                if ($role === "admin") {
                    header("Location: admin/admin.html");
                } elseif ($role === "entrepreneur") {
                    header("Location: entrepreneur\menu.php");
                } else {
                    header("Location: investor-dashboard.php");
                }
                exit();
            } else {
                $error_message = "Mot de passe incorrect.";
            }
        } else {
            $error_message = "Aucun compte trouvé avec cet email.";
        }
        $stmt->close();
    } else {
        $error_message = "Veuillez remplir tous les champs.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Invest Maroc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-8 col-sm-12">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="text-center text-danger mb-4">Erreur de connexion</h2>
                        <p class="text-center text-muted"><?= isset($error_message) ? $error_message : ''; ?></p>
                        <div class="text-center mt-3">
                            <a href="connexion.html" class="btn btn-primary">Réessayer</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

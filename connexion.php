<?php
session_start();

// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "investmaroc");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$error_message = "";

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, role, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $role, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION["user_id"] = $id;
                $_SESSION["role"] = $role;

                // Rediriger selon le rôle
                if ($role === "admin") {
                    header("Location: admin/admin.html");
                } elseif ($role === "entrepreneur") {
                    header("Location: entrepreneur/menu.php");
                } else {
                    header("Location: investisseur/menu.html");
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

    // Redirection pour éviter la resoumission
    header("Location: connexion.php?error=" . urlencode($error_message));
    exit();
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
    <style>
        div .form-control {
            padding: 0.8rem;
            font-size: 1rem;
            border: 1px solid #3d4a76;
            border-radius: 20px;
            outline: none;
            transition: 0.3s ease;
        }
        /* Bouton modernisé avec animation */
button[type="submit"] {
    background-color: #072A40; /* Couleur principale */
    color: white;
    border: none;
    border-radius: 25px; /* Coins arrondis */
    padding: 0.8rem 1.5rem; /* Espace intérieur */
    font-size: 1.1rem;
    font-weight: bold;
    cursor: pointer;
    margin-top: 10px;
    transition: all 0.3s ease; /* Transition pour les animations */
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Ombre */
}

/* Effet au survol */
button[type="submit"]:hover {
    background-color: #073a50; /* Couleur plus claire au survol */
    transform: translateY(-3px); /* Légère élévation */
    box-shadow: 0px 6px 8px rgba(0, 0, 0, 0.15); /* Accentuer l'ombre */
}

/* Effet d'appui */
button[type="submit"]:active {
    background-color: #2b365e; /* Couleur plus foncée à l'appui */
    transform: translateY(1px); /* Réduction d'élévation */
    box-shadow: 0px 3px 5px rgba(0, 0, 0, 0.2); /* Réduction de l'ombre */
}

body {
    background-image: url('images/investissement_maroc.png'); /* Remplacez par le chemin de votre image */
    background-size: cover; /* Ajuste l'image pour couvrir toute la page */
    background-position: center; /* Centrer l'image */
    position: relative;
    height: 100vh; /* Assurez-vous que le body prend toute la hauteur */
    overflow: hidden; /* Pour éviter les barres de défilement */
}

body::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    /*background: rgba(0, 0, 0, 0.5);  Ajoute une couleur sombre semi-transparente */
    z-index: 1; /* Place la couleur au-dessus de l'image mais en dessous du contenu */
}


.container {
    position: relative;
    z-index: 2; /* Place le contenu par-dessus l'image floue */
}


    </style>
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-8 col-sm-12">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4"><b>Se connecter</b></h2>

                        

                        <form action="connexion.php" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label"><b>Adresse email</b></label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="Entrez votre email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label"><b>Mot de passe</b></label>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Entrez votre mot de passe" required>
                            </div>
                                <!-- Affichage du message d'erreur -->
                        <?php if (isset($_GET['error']) && !empty($_GET['error'])): ?>
                            <p class="text-danger text-center"><b><?= htmlspecialchars($_GET['error']); ?></b></p>
                            <?php endif; ?>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary w-100">Connexion</button>
                            </div>
                        
                            <div class="text-center mt-3">
                                <p>Pas encore inscrit ? <a href="inscription.html" class="text-primary">Créer un compte</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


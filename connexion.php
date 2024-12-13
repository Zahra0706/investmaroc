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
                    header("Location: admin/profil.php");
                } elseif ($role === "entrepreneur") {
                    header("Location: entrepreneur/profil.php");
                } else {
                    header("Location: investisseur/profil.php");
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Ajout de Font Awesome -->
    <style>
        div .form-control {
            padding: 0.8rem;
            font-size: 1rem;
            border: 1px solid #3d4a76;
            border-radius: 20px;
            outline: none;
            transition: 0.3s ease;
        }

        button[type="submit"] {
            background-color: #072A40; 
            color: white;
            border: none;
            border-radius: 25px; 
            padding: 0.8rem 1.5rem; 
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            transition: all 0.3s ease; 
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); 
        }

        button[type="submit"]:hover {
            background-color: #073a50; 
            transform: translateY(-3px); 
            box-shadow: 0px 6px 8px rgba(0, 0, 0, 0.15); 
        }

        button[type="submit"]:active {
            background-color: #2b365e; 
            transform: translateY(1px); 
            box-shadow: 0px 3px 5px rgba(0, 0, 0, 0.2); 
        }


        body {
            background-image: url('images/investissement_maroc.png'); 
            background-size: cover; 
            background-position: center; 
            position: relative;
            height: 100vh; 
            overflow: hidden; 
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1; 
        }

        .container {
            position: relative;
            z-index: 2; 
        }

        @media (max-width: 768px) {
            button[type="submit"] {
                font-size: 1rem; 
                padding: 0.6rem 1.2rem; 
            }

            div .form-control {
                padding: 0.6rem;
                font-size: 0.9rem; 
            }
        }

        @media (max-width: 576px) {
            button[type="submit"] {
                font-size: 0.9rem; 
                padding: 0.5rem 1rem; 
            }

            div .form-control {
                padding: 0.5rem;
                font-size: 0.8rem; 
            }

            .container {
                padding: 0.5rem; 
            }
        }

        .toggle-password {
    position: absolute;
    right: 20px;
    top: 45px; /* Ajustez cette valeur selon la hauteur de votre champ */
    cursor: pointer;
    color: #073a50; /* Couleur de l'icône */
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
                            <div class="mb-3 position-relative">
                                <label for="password" class="form-label"><b>Mot de passe</b></label>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Entrez votre mot de passe" required>
                                <i class="fas fa-eye toggle-password" onclick="togglePassword('password')"></i> <!-- Icône pour afficher/masquer -->
                            </div>

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
    <script>
        function togglePassword(inputId) {
            const inputField = document.getElementById(inputId);
            const icon = event.currentTarget;

            if (inputField.type === "password") {
                inputField.type = "text"; 
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                inputField.type = "password"; 
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
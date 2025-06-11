<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php
$host = 'localhost';
$dbname = 'APPFINALE';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connexion échouée : " . $e->getMessage());
}
?>
<?php
session_start();
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        $stmt = $pdo->prepare("SELECT id, nom, mot_de_passe FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['nom'];
            header('Location: PageAccueil.php');
            exit;
        }

    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title id="page-title">Connexion</title>
    <link rel="stylesheet" href="connexion.css">
    <link rel="stylesheet" href="top.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
</head>
<body>
<div class="navbar-barre"></div>
<div class="top-center">
    <div class="logo-block">
        <a href="PageAccueil.php">
            <img src="images/logo.png" alt="Logo">
        </a>
        <p class="logo-slogan">Chez Gusteau's</p>
        <h1 class="page-title" id="titre-page">Connexion</h1>
    </div>

    <form method="POST" action="connexion.php" class="formulaire-connexion">
        <div class="champ">
            <input type="email" id="email" name="email" placeholder="Entrez votre adresse mail" required>
        </div>
        <div class="champ">
            <div class="input-container">
                <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required>
                <img src="images/eye-closed.png" alt="Afficher mot de passe" class="eye-icon" onclick="togglePasswordVisibility('password')">
            </div>
        </div>
        <button href="PageAccueil" type="submit" class="connexion" id="btn-connexion">Se connecter</button>
    </form>

    <?php if ($error): ?>
        <p style="color:red; text-align:center;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <div class="logo-block">
        <a id="inscription-lien" href="inscription.php" class="inscription" style="text-decoration: none; font-size: 1.2em">Inscription</a>
        <a id="mdp-lien" href="mdp-oublier.php" class="mdp-oublier" style="text-decoration: none; font-size: 1.2em">Mot de passe oublié</a>
    </div>
</div>

<script>
    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        const icon = document.querySelector(`[onclick="togglePasswordVisibility('${inputId}')"]`);

        if (input.type === "password") {
            input.type = "text";
            icon.src = "images/eye-open.png";
            icon.alt = "Masquer mot de passe";
        } else {
            input.type = "password";
            icon.src = "images/eye-closed.png";
            icon.alt = "Afficher mot de passe";
        }
    }
</script>
</body>
</html>
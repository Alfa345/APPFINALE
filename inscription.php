<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php
global $pdo;
session_start();
require_once 'config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


$success = $error = '';
$estConnecte = isset($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mdp = $_POST['mdp'] ?? '';
    $mdp_confirm = $_POST['mdp_confirm'] ?? '';
    $mentions = isset($_POST['mentions']);
    $recaptchaSecret = '6LfJqjErAAAAABxFJ1B916BkIyvUe2S5CyvNjdbl';
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
    $recaptchaVerified = false;
    $langue = 'fr';

    $messages = [
        'fr' => [
            'recaptcha' => "Veuillez confirmer que vous n'êtes pas un robot.",
            'champs' => "Tous les champs sont obligatoires.",
            'mdpInvalide' => "Le mot de passe doit contenir au moins 12 caractères, une majuscule et une minuscule.",
            'mdpDifferent' => "Les mots de passe ne correspondent pas.",
            'emailUtilise' => "Cet e-mail est déjà utilisé.",
            'emailErreur' => "L'inscription est réussie, mais l'email n'a pas pu être envoyé."
        ],
    ];

    if ($recaptchaResponse) {
        $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse");
        $responseData = json_decode($verify);
        $recaptchaVerified = $responseData->success;
    }

    if (!$recaptchaVerified) {
        $error = $messages[$langue]['recaptcha'];
    } elseif (!$nom || !$prenom || !$email || !$mdp || !$mdp_confirm || !$mentions) {
        $error = $messages[$langue]['champs'];
    } elseif ($mdp !== $mdp_confirm) {
        $error = $messages[$langue]['mdpDifferent'];
    } elseif (strlen($mdp) < 12 || !preg_match('/[A-Z]/', $mdp) || !preg_match('/[a-z]/', $mdp)) {
        $error = $messages[$langue]['mdpInvalide'];
    } else {
        $check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $error = $messages[$langue]['emailUtilise'];
        } else {
            $hash = password_hash($mdp, PASSWORD_DEFAULT);
            $insert = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (?, ?, ?)");
            $insert->execute([$nom, $email, $hash]);

            if (!$error) {
                header("Location: Connexion.php?inscription=ok");
                exit;
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title id="page-title">Inscription</title>
    <link rel="stylesheet" href="inscription.css">
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
        <h1 class="page-title" id="titre-page">Inscription</h1>
    </div>
</div>

<main class="contenu-scrollable" >
    <form method="POST" action="inscription.php" class="formulaire-inscription">
        <input type="hidden" name="langue" id="langue">
        <div class="champ">
            <br>
            <div class="champ-obligatoire">
                <span class="etoile">*</span>
                <input type="text" id="nom" name="nom" placeholder="Nom" required>
            </div>

            <div class="champ-obligatoire">
                <span class="etoile">*</span>
                <input type="text" id="prenom" name="prenom" placeholder="Prénom" required>
            </div>

            <div class="champ-obligatoire">
                <span class="etoile">*</span>
                <input type="email" id="email" name="email" placeholder="Entrez votre adresse e-mail" required>
            </div>

            <div class="champ-obligatoire">
                <span class="etoile" style="margin-left: 30px" >*</span>
                <div class="password-container">
                    <input style="margin-left: 30px" type="password" id="mdp" name="mdp" placeholder="Entrez votre mot de passe" required>
                    <img src="images/eye-closed.png" alt="Afficher mot de passe" class="toggle-password" onclick="togglePasswordVisibility('mdp')">
                </div>
            </div>

            <div class="champ-obligatoire">
                <span class="etoile" style="margin-left: 30px" >*</span>
                <div class="password-container">
                    <input style="margin-left: 30px" type="password" id="mdp-confirm" name="mdp_confirm" placeholder="Confirmez votre mot de passe" required>
                    <img src="images/eye-closed.png" alt="Afficher mot de passe" class="toggle-password" onclick="togglePasswordVisibility('mdp-confirm')">
                </div>
            </div>
        </div>

        <div class="conditions-general">
            <div class="champ-obligatoire">
                <span class="etoile">*</span>
                <label class="checkbox-container">
                    <input type="checkbox" id="mentions" name="mentions" required>
                    <span class="checkmark"></span>
                    <span class="conditions" id="conditions-text">Accepter les conditions d'utilisations</span>
                </label>
            </div>
        </div>
        <div class="conditions-general">
            <div class="champ-obligatoire">
                <span class="etoile">*</span>
                <div class="g-recaptcha" data-sitekey="6LfJqjErAAAAAChIifxO3-ht7cVOz2HVz03aWZSF"></div>
            </div>
        </div>

        <div class="conditions-general">
            <br>
            <button type="submit" class="inscription" id="btn-inscription">S'inscrire</button>
        </div>
    </form>

    <strong>
        <?php if ($error): ?>
            <p style="color: red; text-align: center;"><?= htmlspecialchars($error) ?></p>
        <?php elseif ($success): ?>
            <p style="color: green; text-align: center;"><?= $success ?></p>
        <?php endif; ?>
    </strong>

    <div class="logo-block" style="font-size: 1.2em">
        <a href="connexion.php" class="connexion" id="lien-connexion">Se connecter</a>
    </div>
    <div class="conditions-general">
        <div class="champ-obligatoire">
            <span class="etoile">*</span>
            <span class="conditions"  id="champs">Ce champ est obligatoire</span>
        </div>
    </div>
</main>
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
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const userLang = localStorage.getItem("langue") || "fr";
        const script = document.createElement('script');
        script.src = `https://www.google.com/recaptcha/api.js?hl=${userLang}`;
        script.async = true;
        script.defer = true;
        document.body.appendChild(script);
    });
</script>
</body>
</html>
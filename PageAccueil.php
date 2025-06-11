<?php
session_start();
$estConnecte = isset($_SESSION['user_id']);
$accessibilityMode = isset($_SESSION['accessibility_mode']) ? $_SESSION['accessibility_mode'] : false;
?>
<!DOCTYPE html>
<html lang="fr" class="<?= $accessibilityMode ? 'accessibility-mode' : '' ?>">
<head>
    <meta charset="UTF-8">
    <title id="page-title"></title>
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="PageAccueil.css">
    <link rel="stylesheet" href="top.css">
</head>
<body>
<div class="navbar-barre"></div>
<div class="background-fixe"></div>

<div class="top-right">
    <?php if ($estConnecte): ?>
        <a  href="MonCompte.php" style="color: #e0e0d5; font-weight: bold; margin-right: 15px; text-decoration: none;">
            <?= htmlspecialchars($_SESSION['first_name']) ?>
        </a>
    <?php else: ?>
        <a id="lien-compte" href="connexion.php" style="color: #e0e0d5; text-decoration: none;">Mon Compte</a>
    <?php endif; ?>
</div>

<div class="top-center">
    <img src="images/logo.png" alt="Logo">
</div>

<section class="section-accueil">
    <div class="texte-wrapper">
        <div id="texte-principal">Chez Gusteau's</div>
    </div>
</section>
</body>
</html>
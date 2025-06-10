<?php
require_once 'connexion.php';

$utilisateurs = $pdo->query("SELECT * FROM utilisateurs")->fetchAll(PDO::FETCH_ASSOC);

foreach ($utilisateurs as $user) {
    echo $user['nom'] . '<br>';
}
?>

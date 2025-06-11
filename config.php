<?php
/**
 * Fichier de Configuration Principal pour Cap'Tivant
 * Gère la connexion BDD, les chemins de fichiers, et la configuration globale.
 */

// --- PARAMÈTRES DE CONNEXION À LA BASE DE DONNÉES ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'APPFINALE');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// --- CONFIGURATION DES UPLOADS ---
define('BOAT_UPLOAD_DIR', __DIR__ . '/uploads/boat_images/');
define('BOAT_UPLOAD_DIR_RELATIVE', 'uploads/boat_images/');
define('MAX_BOAT_IMAGE_SIZE', 5 * 1024 * 1024); // 5 Mo

define('ALLOWED_BOAT_IMAGE_MIME_TYPES', [
    'image/jpeg',
    'image/png',
    'image/gif',
    'image/webp'
]);

define('ALLOWED_BOAT_IMAGE_EXTENSIONS', [
    'jpg',
    'jpeg',
    'png',
    'gif',
    'webp'
]);

// --- CONNEXION À LA BASE DE DONNÉES (PDO) ---
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    error_log("ERREUR DE CONNEXION BDD : " . $e->getMessage());
    die("Une erreur technique empêche l'accès au service. Veuillez réessayer plus tard.");
}

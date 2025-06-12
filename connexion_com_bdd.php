<?php

// Real time display on the web page
function echoFlush($string)
{
	echo $string . "\n";
	flush();
	ob_flush();
}

echoFlush("<h3>Demo Serial Communication...</h3>");

// Config port série
$portName = 'COM6';
$baudRate = 9600;
$bits = 8;
$stopBit = 1;

// Ouverture du port série
$serialPort = dio_open("\\\\.\\{$portName}", O_RDWR );

$output = array();
exec("mode {$portName} baud={$baudRate} data={$bits} stop={$stopBit} parity=n xon=on", $output);
echoFlush(str_replace("\n", "<br>", print_r($output, true) . "<br>"));

if(!$serialPort) {
    echoFlush("Could not open Serial port {$portName}");
    exit;
}

// Connexion à la base de données MySQL (à adapter avec tes infos)
$host = 'mysql-gusto.alwaysdata.net';
$dbname = 'gusto_g5';
$user = 'gusto';
$pass = 'RestoGustoG5';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echoFlush("Connected to MySQL database.<br>");
} catch (PDOException $e) {
    echoFlush("Database connection failed: " . $e->getMessage());
    exit;
}

// Lecture pendant 20 secondes
$runForSeconds = new DateInterval("PT20S");
$endTime = (new DateTime())->add($runForSeconds);

echoFlush("Waiting for {$runForSeconds->format('%S')} seconds to receive data on serial port...<br>");

while (new DateTime() < $endTime) {

    $data = dio_read($serialPort, 5); // Bloquant

    if ($data) {
        echoFlush("Reçu brut : " . str_replace("\n", "<br>", "[" . $data . "]"));

        // Nettoyage et extraction du float
        if (preg_match('/([-+]?[0-9]*\.?[0-9]+)/', $data, $matches)) {
            $value = floatval($matches[1]);

            // Insertion dans la base
            $stmt = $pdo->prepare("INSERT INTO Capteur_Son (valeur, temps) VALUES (?, NOW())");
            $stmt->execute([$value]);

            echoFlush("Inséré en BDD : {$value}<br>");
        } else {
            echoFlush("Aucune valeur numérique détectée.<br>");
        }
    }
}

dio_close($serialPort);
?>

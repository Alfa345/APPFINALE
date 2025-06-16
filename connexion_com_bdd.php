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
$runForSeconds = new DateInterval("PT3600S");
$endTime = (new DateTime())->add($runForSeconds);

echoFlush("Waiting for {$runForSeconds->format('%S')} seconds to receive data on serial port...<br>");

$buffer = "";

while (new DateTime() < $endTime) {
    // Lecture d'un petit bloc
    $data = dio_read($serialPort, 5); // 16 caractères à la fois

    if ($data) {
        $buffer .= $data;

        // Traitement des messages complets (terminés par ; ou \n ou \r)
        while (preg_match('/^([^;\r\n]{1,10})[;\r\n]/', $buffer, $matches)) {
            $message = $matches[1];
            $buffer = substr($buffer, strlen($matches[0])); // Supprime le message traité

            echoFlush("Message complet : [$message]");

            // Extraction d'une valeur numérique
            if (preg_match('/([-+]?[0-9]*\.?[0-9]+)/', $message, $m)) {
                $value = floatval($m[1]);

                // Insertion dans la BDD
                $stmt = $pdo->prepare("INSERT INTO Capteur_Son (valeur, temps) VALUES (?, NOW())");
                $stmt->execute([$value]);

                echoFlush("Inséré en BDD : $value<br>");
            } else {
                echoFlush("Pas de valeur numérique détectée dans : [$message]<br>");
            }
        }
    }
}


dio_close($serialPort);
?>

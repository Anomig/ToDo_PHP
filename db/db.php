<?php
$host = 'localhost'; // Of '127.0.0.1'
$dbname = 'todo';
$username = 'root'; // Standaard MAMP-gebruikersnaam
$password = 'root'; // Standaard MAMP-wachtwoord

try {
    // Maak de verbinding met de database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Stel PDO foutmodus in op Exception voor betere foutafhandeling
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Als er een fout optreedt, geef de fout weer en stop de script
    echo 'Connection failed: ' . $e->getMessage();
    exit();
}
?>

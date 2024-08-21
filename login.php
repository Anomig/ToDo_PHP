<?php
session_start(); // Start de sessie
require 'db/db.php'; // Verbindt met de database via db.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Zoek de gebruiker in de database
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Sla gebruikersinformatie op in de sessie
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: index.php'); // Verwijs naar het dashboard
        exit();
    } else {
        echo "Ongeldige inloggegevens. Probeer opnieuw.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inloggen</title>
</head>
<body>
    <h2>Inloggen</h2>
    <form method="post" action="">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br>
        
        <label for="password">Wachtwoord:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        
        <input type="submit" value="Inloggen">
    </form>
    <!-- Link naar de registratiepagina -->
    <p>Heb je nog geen account? <a href="register.php">Registreer hier</a></p>
</body>
</html>

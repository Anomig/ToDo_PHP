<?php

//error detectie
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db/db.php'; // Verbindt met de database via db.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Controleer of de gebruiker al bestaat
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        echo "Email is al geregistreerd!";
    } else {
        // Voeg de nieuwe gebruiker toe
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$username, $email, $password])) {
            echo "Registratie succesvol! Je kunt nu <a href='login.php'>inloggen</a>.";
        } else {
            echo "Er is iets misgegaan. Probeer het opnieuw.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
</head>
<body>
    <h2>Registreren</h2>
    <form method="post" action="">
        <label for="username">Gebruikersnaam:</label><br>
        <input type="text" id="username" name="username" required><br>
        
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br>
        
        <label for="password">Wachtwoord:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        
        <input type="submit" value="Registreer">
    </form>
    <!-- Link naar de loginpagina -->
    <p>Heb je al een account? <a href="login.php">Log hier in</a></p>
</body>
</html>

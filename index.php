<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    <h2>Welkom, <?php echo $_SESSION['username']; ?>!</h2>
    <p>Dit is je dashboard.</p>
    <a href="logout.php">Uitloggen</a>
</body>
</html>

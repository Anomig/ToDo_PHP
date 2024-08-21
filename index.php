<?php

//error detectie
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db/db.php';
require_once 'classes/ListController.php';

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$listController = new ListController($pdo);
$user_id = $_SESSION['user_id'];

// Handle form submission for creating a new list
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['list_name'])) {
    $listController->create($user_id, $_POST['list_name']);
    header('Location: index.php'); // Refresh to show the new list
    exit();
}

// Fetch all lists for the logged-in user
$lists = $listController->index($user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    <h2>Welkom, <?php echo $_SESSION['username']; ?>!</h2>

    <h3>Jouw Lijsten</h3>
    <ul>
        <?php foreach ($lists as $list): ?>
            <li><?php echo htmlspecialchars($list['name']); ?> 
                <a href="delete_list.php?id=<?php echo $list['id']; ?>">Verwijder</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <h3>Nieuwe Lijst Toevoegen</h3>
    <form method="POST" action="">
        <label for="list_name">Lijstnaam:</label>
        <input type="text" id="list_name" name="list_name" required>
        <button type="submit">Voeg toe</button>
    </form>

    <a href="logout.php">Uitloggen</a>
</body>
</html>

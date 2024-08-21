<?php
session_start();
require_once 'db/db.php';
require_once 'classes/TaskController.php';

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$taskController = new TaskController($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $task_id = intval($_POST['id']);
    
    // Verwijder de taak
    $result = $taskController->delete($task_id);

    if ($result) {
        echo 'Taak verwijderd.';
    } else {
        echo 'Er is een fout opgetreden bij het verwijderen van de taak.';
    }
    exit();
}

header('Location: index.php');
exit();
?>

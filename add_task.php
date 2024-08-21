<?php
session_start();
require_once 'db/db.php';
require_once 'classes/taskController.php';

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Controleer of alle benodigde gegevens zijn ingediend
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task_title'], $_POST['task_status'])) {
    $list_id = isset($_POST['list_id']) && $_POST['list_id'] != '' ? intval($_POST['list_id']) : null;
    $title = trim($_POST['task_title']);
    $deadline = isset($_POST['task_deadline']) ? $_POST['task_deadline'] : null; // Format: YYYY-MM-DD
    $status = $_POST['task_status'];
    $comment = isset($_POST['task_comment']) ? trim($_POST['task_comment']) : null;

    // Maak een instantie van de taskController
    $taskController = new taskController($pdo);
    
    // Voeg de taak toe aan de database
    if ($taskController->create($list_id, $title, $deadline, $status, $comment)) {
        // Redirect naar index.php na succesvolle toevoeging
        header('Location: index.php');
        exit();
    } else {
        // Verwerk fout bij het toevoegen van de taak
        echo "Er is een fout opgetreden bij het toevoegen van de taak.";
    }
} else {
    // Verwerk geval van ontbrekende gegevens
    echo "Verkeerde aanvraag.";
}
?>

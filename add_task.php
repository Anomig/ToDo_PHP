<?php
session_start();
require_once 'db/db.php';
require_once 'classes/TaskController.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$taskController = new TaskController($pdo);
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Controleer of list_id leeg is en stel het in op NULL indien dit het geval is
    $list_id = !empty($_POST['list_id']) ? $_POST['list_id'] : NULL;
    $title = $_POST['task_title'];
    $deadline = $_POST['task_deadline'];
    $status = $_POST['task_status'];
    $comment = $_POST['task_comment'];

    // Probeer de taak toe te voegen
    $result = $taskController->create($list_id, $title, $deadline, $status, $comment);

    if ($result === true) {
        // Succes, taak toegevoegd
        header('Location: index.php');
        exit();
    } else {
        // Fout, toon de foutboodschap
        $error_message = $result;
    }
}

// Zorg ervoor dat het formulier de foutboodschap toont
include 'index.php';
?>
<?php
session_start();
require_once 'db/db.php';
require_once 'classes/TaskController.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Niet geautoriseerd
    exit('Niet geautoriseerd');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['comment'])) {
    $taskId = intval($_POST['id']);
    $comment = trim($_POST['comment']);

    $taskController = new TaskController($pdo);

    // Werk het commentaar bij
    if ($taskController->updateTaskComment($taskId, $comment)) {
        echo 'Success';
    } else {
        http_response_code(500); // Interne serverfout
        echo 'Fout bij het toevoegen van commentaar';
    }
    exit();
}

http_response_code(400); // Slechte aanvraag
exit('Ongeldige aanvraag');

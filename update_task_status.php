<?php
session_start();
require_once 'db/db.php';
require_once 'classes/TaskController.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Niet geautoriseerd
    exit('Niet geautoriseerd');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    $taskId = intval($_POST['id']);
    $newStatus = $_POST['status'];

    $taskController = new TaskController($pdo);

    // Werk de status bij
    if ($taskController->updateTaskStatus($taskId, $newStatus)) {
        echo 'Success';
    } else {
        http_response_code(500); // Interne serverfout
        echo 'Fout bij het bijwerken van de status';
    }
    exit();
}

http_response_code(400); // Slechte aanvraag
exit('Ongeldige aanvraag');

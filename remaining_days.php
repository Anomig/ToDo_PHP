<?php
header('Content-Type: application/json');

require_once 'db/db.php';
require_once 'classes/TaskController.php';

// Verkrijg de taak-ID vanuit de querystring
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['error' => 'Invalid task ID']);
    exit();
}

$task_id = intval($_GET['id']);
$taskController = new TaskController($pdo);
$task = $taskController->getTaskById($task_id);

if (!$task) {
    echo json_encode(['error' => 'Task not found']);
    exit();
}

$deadline = new DateTime($task['deadline']);
$now = new DateTime();
$interval = $now->diff($deadline);

$remaining_days = $interval->days;

if ($now > $deadline) {
    $remaining_days = -$remaining_days; // Negatieve waarde als de deadline verstreken is
}

echo json_encode(['remaining_days' => $remaining_days]);

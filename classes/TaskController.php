<?php
require_once 'Task.php';

class TaskController {
    private $task;

    public function __construct($pdo) {
        $this->task = new Task($pdo);
    }

    // Maak een taak aan
    public function create($list_id, $title, $deadline, $status, $comment) {
        return $this->task->createTask($list_id, $title, $deadline, $status, $comment);
    }

    // Haal alle taken op voor een specifieke lijst
    public function getTasksByListId($list_id) {
        return $this->task->getAllTasksByListId($list_id);
    }

    // Haal alle taken op zonder groepering
    public function getAllTasks() {
        return $this->task->getAllTasks();
    }

    // Haal een specifieke taak op basis van ID
    public function getTaskById($id) {
        return $this->task->getTaskById($id);
    }

    // Werk een taak bij
    public function updateTask($id, $list_id, $title, $deadline, $status, $comment) {
        return $this->task->updateTask($id, $list_id, $title, $deadline, $status, $comment);
    }

    public function updateTaskStatus($id, $status) {
        return $this->task->updateTaskStatus($id, $status);
    }

    // Verwijder een taak
    public function delete($id) {
        return $this->task->deleteTasks($id);
    }
}

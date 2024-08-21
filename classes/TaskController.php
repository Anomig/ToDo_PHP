<?php
require_once 'Task.php';

class taskController {
    private $task;

    public function __construct($pdo) {
        $this->task = new Task($pdo);
    }

    public function create($list_id, $title, $deadline, $status, $comment) {
        return $this->task->createTask($list_id, $title, $deadline, $status, $comment);
    }

    public function index($list_id) {
        return $this->task->getAllTasksByListId($list_id);
    }

    public function getAllTasks() {
        return $this->task->getAllTasks();
    }

    public function getTaskById($id) {
        return $this->task->getTaskById($id);
    }

    public function updateTask($id, $title, $deadline, $status, $comment) {
        return $this->task->updateTask($id, $title, $deadline, $status, $comment);
    }

    public function delete($id) {
        return $this->task->deleteTasks($id);
    }
}

<?php
class Task {
    private $id;
    private $list_id;
    private $title;
    private $deadline;
    private $status;
    private $comment;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setListId($list_id) {
        $this->list_id = $list_id;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setDeadline($deadline) {
        $this->deadline = $deadline;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function setComment($comment) {
        $this->comment = $comment;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getListId() {
        return $this->list_id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getDeadline() {
        return $this->deadline;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getComment() {
        return $this->comment;
    }

    // Fetch all tasks for a specific user
    public function getAllTasksByListId($list_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM tasks WHERE list_id = ?');
        $stmt->execute([$list_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Controleer of een taak met dezelfde naam al bestaat in de lijst
    public function taskExistsInList($list_id, $task_title) {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM tasks WHERE list_id = ? AND title = ?');
        $stmt->execute([$list_id, $task_title]);
        return $stmt->fetchColumn() > 0;
    }
    // Create a new task
    public function createTask($list_id, $task_title, $deadline, $status, $comment) {
        if ($this->taskExistsInList($list_id, $task_title)) {
            throw new Exception('Er bestaat al een taak met dezelfde naam in deze lijst.');
        }
        
        $stmt = $this->pdo->prepare('INSERT INTO tasks (list_id, title, deadline, status, comment) VALUES (?, ?, ?, ?, ?)');
        return $stmt->execute([$list_id, $task_title, $deadline, $status, $comment]);
    }

    public function getAllTasks() {
        $stmt = $this->pdo->query('SELECT * FROM tasks');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete a task
    public function deleteTasks($id) {
        $stmt = $this->pdo->prepare('DELETE FROM tasks WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function getTaskById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateTask($id, $list_id, $title, $deadline, $status, $comment) {
        $stmt = $this->pdo->prepare('UPDATE tasks SET list_id = ?, title = ?, deadline = ?, status = ?, comment = ? WHERE id = ?');
        return $stmt->execute([$list_id, $title, $deadline, $status, $comment, $id]);
    }
    
    public function updateTaskStatus($id, $status) {
        $stmt = $this->pdo->prepare('UPDATE tasks SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    
    
    
}

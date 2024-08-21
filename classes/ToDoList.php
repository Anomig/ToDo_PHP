<?php
class ToDoList {
    private $id;
    private $user_id;
    private $name;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getName() {
        return $this->name;
    }

    // Fetch all lists for a specific user
    public function getAllListsByUserId($user_id) {
        $stmt = $this->pdo->prepare('SELECT * FROM lists WHERE user_id = ?');
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Create a new list
    public function createList($user_id, $name) {
        $stmt = $this->pdo->prepare('INSERT INTO lists (user_id, name) VALUES (?, ?)');
        return $stmt->execute([$user_id, $name]);
    }

    // Delete a list
    public function deleteList($id) {
        $stmt = $this->pdo->prepare('DELETE FROM lists WHERE id = ?');
        return $stmt->execute([$id]);
    }
}

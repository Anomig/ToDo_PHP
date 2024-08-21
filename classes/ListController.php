<?php
require_once 'ToDoList.php';

class ListController {
    private $list;

    public function __construct($pdo) {
        $this->list = new ToDoList($pdo);
    }

    public function create($user_id, $name) {
        return $this->list->createList($user_id, $name);
    }

    public function index($user_id) {
        return $this->list->getAllListsByUserId($user_id);
    }

    public function delete($id) {
        return $this->list->deleteList($id);
    }
}

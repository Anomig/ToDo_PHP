<?php

//error detectie
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db/db.php';
require_once 'classes/TaskController.php';

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$listController = new TaskController($pdo);

// Controleer of een lijst-ID is opgegeven in de URL
if (isset($_GET['id'])) {
    $list_id = $_GET['id'];
    $listController->delete($task_id);
}

header('Location: index.php');
exit();

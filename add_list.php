<?php
session_start();
require_once 'db/db.php';
require_once 'classes/ListController.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$listController = new ListController($pdo);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['list_name'])) {
    $user_id = $_SESSION['user_id'];
    $list_name = trim($_POST['list_name']);
    
    if (!empty($list_name)) {
        // Voeg de lijst toe via de ListController
        $success = $listController->create($user_id, $list_name);

        if ($success) {
            $_SESSION['message'] = 'Nieuwe lijst succesvol toegevoegd!';
        } else {
            $_SESSION['message'] = 'Er is een fout opgetreden bij het toevoegen van de lijst.';
        }
    } else {
        $_SESSION['message'] = 'Lijstnaam mag niet leeg zijn.';
    }
}

header('Location: index.php');
exit();

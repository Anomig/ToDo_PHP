<?php

// Error detectie
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db/db.php';
require_once 'classes/ListController.php';

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$listController = new ListController($pdo);

// Controleer of een lijst-ID is opgegeven in de POST-gegevens
if (isset($_POST['id'])) {
    $list_id = intval($_POST['id']);
    if ($listController->delete($list_id)) {
        echo 'Lijst verwijderd'; // Dit kan door de JavaScript worden gecontroleerd
    } else {
        echo 'Fout bij verwijderen'; // Foutmelding voor debugging
    }
} else {
    echo 'Geen lijst-ID opgegeven';
}

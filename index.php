<?php

//error detectie
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db/db.php';
require_once 'classes/ListController.php';
require_once 'classes/TaskController.php';

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$listController = new ListController($pdo);
$taskController = new taskController($pdo);
$user_id = $_SESSION['user_id'];

// Handle form submission for creating a new list
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['list_name'])) {
    $listController->create($user_id, $_POST['list_name']);
    header('Location: index.php'); // Refresh to show the new list
    exit();
}

// Fetch all lists for the logged-in user
$lists = $listController->index($user_id);

$list_id = isset($_GET['list_id']) ? intval($_GET['list_id']) : 0;

// Fetch all tasks for the logged-in user
$tasks = $list_id ? $taskController->index($list_id) : $taskController->getAllTasks();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <div class="container">
        <h2>Welkom, <?php echo $_SESSION['username']; ?>!</h2>

        <div class="tasks-container">
            <div class="tasks-column">
                <h2 class="header">Not Done</h2>
                <?php
                // Filter en sorteer taken die 'todo' of 'pending' zijn
                $notDoneTasks = array_filter($tasks, function($task) {
                    return in_array($task['status'], ['todo', 'pending']);
                });
                // Sorteer taken op deadline
                usort($notDoneTasks, function($a, $b) {
                    return strcmp($a['deadline'], $b['deadline']);
                });
                ?>
                <?php foreach ($notDoneTasks as $task): ?>
                    <div class="task-item <?php echo htmlspecialchars($task['status']); ?>">
                        <div class="title"><?php echo htmlspecialchars($task['title']); ?></div>
                        <div class="date"><?php echo htmlspecialchars($task['deadline']); ?></div>
                        
                    </div>
                <?php endforeach; ?>

                <h2 class="header">Done</h2>
                <?php
                // Filter taken die 'done' zijn
                $doneTasks = array_filter($tasks, function($task) {
                    return $task['status'] === 'done';
                });
                ?>
                <?php foreach ($doneTasks as $task): ?>
                    <div class="task-item done">
                        <div class="title"><?php echo htmlspecialchars($task['title']); ?></div>
                        <div class="date"><?php echo htmlspecialchars($task['deadline']); ?></div>
                        
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="lists-column">
                <h2 class="header">Lijsten</h2>
                <?php foreach ($lists as $list): ?>
                    <div class="list-item">
                        <?php echo htmlspecialchars($list['name']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-container">
            <h3>Nieuwe Taak Toevoegen</h3>
            <form method="POST" action="add_task.php">
                <label for="task_title">Taaknaam:</label>
                <input type="text" id="task_title" name="task_title" required>
                
                <label for="task_deadline">Deadline:</label>
                <input type="date" id="task_deadline" name="task_deadline">
                
                <label for="task_status">Status:</label>
                <select id="task_status" name="task_status" required>
                    <option value="todo">Te doen</option>
                    <option value="pending">In behandeling</option>
                    <option value="done">Voltooid</option>
                </select>
                
                <label for="task_comment">Opmerking:</label>
                <textarea id="task_comment" name="task_comment"></textarea>
                
                <label for="list_name">Selecteer Lijst:</label>
                <select id="list_name" name="list_name">
                    <option value="">Geen lijst</option>
                    <?php foreach ($lists as $list): ?>
                        <option value="<?php echo htmlspecialchars($list['id']); ?>">
                            <?php echo htmlspecialchars($list['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <button type="submit">Voeg toe</button>
            </form>
        </div>

        <div class="logout-container">
            <a href="logout.php" class="logout-button">Uitloggen</a>
        </div>
    </div>
</body>
</html>


<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    <h2>Welkom, <?php echo $_SESSION['username']; ?>!</h2>

    <h3>Jouw Lijsten</h3>
    <ul>
        <?php foreach ($lists as $list): ?>
            <li><?php echo htmlspecialchars($list['name']); ?> 
                <a href="delete_list.php?id=<?php echo $list['id']; ?>">Verwijder</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <h3>Nieuwe Lijst Toevoegen</h3>
    <form method="POST" action="">
        <label for="list_name">Lijstnaam:</label>
        <input type="text" id="list_name" name="list_name" required>
        <button type="submit">Voeg toe</button>
    </form>

    <h3>Jouw Taken</h3>
    <ul>
        <?php foreach ($tasks as $task): ?>
            <li><?php echo htmlspecialchars($task['title']); ?> 
                <a href="delete_task.php?id=<?php echo $task['id']; ?>">Verwijder</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <h3>Nieuwe Taak Toevoegen</h3>
    <form method="POST" action="add_task.php">
        <label for="task_title">Taaknaam:</label>
        <input type="text" id="task_title" name="task_title" required>
    
        <label for="task_deadline">Deadline:</label>
        <input type="date" id="task_deadline" name="task_deadline">
    
        <label for="task_status">Status:</label>
        <select id="task_status" name="task_status" required>
            <option value="todo">Te doen</option>
            <option value="pending">In behandeling</option>
            <option value="done">Voltooid</option>
        </select>
    
        <label for="task_comment">Opmerking:</label>
        <textarea id="task_comment" name="task_comment"></textarea>
    
        <label for="list_name">Selecteer Lijst:</label>
            <select id="list_name" name="list_name">
                <option value="">Geen lijst</option>
                <?php foreach ($lists as $list): ?>
                    <option value="<?php echo htmlspecialchars($list['id']); ?>">
                <?php echo htmlspecialchars($list['name']); ?>
                </option>
                <?php endforeach; ?>
            </select>
    
        <button type="submit">Voeg toe</button>
    </form>


    <a href="logout.php">Uitloggen</a>
</body>
</html> -->

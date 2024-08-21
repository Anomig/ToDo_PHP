<?php
session_start();
require_once 'db/db.php';
require_once 'classes/TaskController.php';

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Initialiseer de taskController
$taskController = new TaskController($pdo);

// Verkrijg het taak-ID vanuit de URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$task_id = intval($_GET['id']);

// Verkrijg de taakgegevens
$task = $taskController->getTaskById($task_id);

if (!$task) {
    header('Location: index.php');
    exit();
}

// Verwerk het formulier voor taakupdate
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['task_title'];
    $deadline = $_POST['task_deadline'];
    $status = $_POST['task_status'];
    $comment = $_POST['task_comment'];

    // Update de taak
    $taskController->updateTask($task_id, $title, $deadline, $status, $comment);

    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Taak Bewerken</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <h2>Bewerk Taak</h2>
    <form method="POST" action="">
        <label for="task_title">Taaknaam:</label>
        <input type="text" id="task_title" name="task_title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
        
        <label for="task_deadline">Deadline:</label>
        <input type="date" id="task_deadline" name="task_deadline" value="<?php echo htmlspecialchars($task['deadline']); ?>">
        
        <label for="task_status">Status:</label>
        <select id="task_status" name="task_status" required>
            <option value="todo" <?php echo ($task['status'] === 'todo') ? 'selected' : ''; ?>>Te doen</option>
            <option value="pending" <?php echo ($task['status'] === 'pending') ? 'selected' : ''; ?>>In behandeling</option>
            <option value="done" <?php echo ($task['status'] === 'done') ? 'selected' : ''; ?>>Voltooid</option>
        </select>
        
        <label for="task_comment">Opmerking:</label>
        <textarea id="task_comment" name="task_comment"><?php echo htmlspecialchars($task['comment']); ?></textarea>

        <button type="submit" class="add-button">Bijwerken</button>
    </form>
    
    <a href="index.php" class="add-button">Terug naar overzicht</a>
</body>
</html>

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
$taskController = new TaskController($pdo);
$user_id = $_SESSION['user_id'];

// Handle form submission for creating a new list
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['list_name'])) {
    $listController->create($user_id, $_POST['list_name']);
    header('Location: index.php'); // Refresh to show the new list
    exit();
}

// Fetch all lists for the logged-in user
$lists = $listController->index($user_id);
if (!is_array($lists)) {
    $lists = [];
}

$list_id = isset($_GET['list_id']) ? intval($_GET['list_id']) : 0;

// Fetch tasks for the specific list or all tasks if no list ID is provided
$tasks = $list_id ? $taskController->getTasksByListId($list_id) : $taskController->getAllTasks();
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
        <h2>Welkom, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>

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
                    <div class="task-item" data-task-id="<?php echo htmlspecialchars($task['id']); ?>">
                        <a href="edit_task.php?id=<?php echo htmlspecialchars($task['id']); ?>" class="edit-button">✎</a>
                        <button class="status-toggle done" data-task-id="<?php echo htmlspecialchars($task['id']); ?>" data-new-status="done">Done</button>
                        <button class="status-toggle busy" data-task-id="<?php echo htmlspecialchars($task['id']); ?>" data-new-status="pending">Busy</button>
                        <span class="task-title"><?php echo htmlspecialchars($task['title']); ?></span>
                        <span class="task-deadline" data-original-date="<?php echo htmlspecialchars($task['deadline']); ?>">
                            <?php echo htmlspecialchars($task['deadline']); ?>
                        </span>
                        <form method="POST" action="delete_task.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($task['id']); ?>">
                            <button type="submit" class="delete-button">✖</button>
                        </form>
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
                        <button class="delete-task-button" data-task-id="<?php echo htmlspecialchars($task['id']); ?>">✖</button>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="lists-column">
                <h2 class="header">Lijsten</h2>
                <?php if (!empty($lists)): ?>
                    <?php foreach ($lists as $list): ?>
                        <div class="list-item-container">
                            <div class="list-header">
                            <strong class="list-item-title"><?php echo htmlspecialchars($list['name']); ?></strong>
                            <a href="#" class="delete-button" data-list-id="<?php echo htmlspecialchars($list['id']); ?>" title="verwijder">✖</a>
                            </div>
                            <?php
                            // Fetch tasks associated with the current list
                            $listTasks = $taskController->getTasksByListId($list['id']);
                            if (!empty($listTasks)): ?>
                                <ul class="task-list">
                                    <?php foreach ($listTasks as $task): ?>
                                        <li class="task-list-item">
                                            <a href="edit_task.php?id=<?php echo htmlspecialchars($task['id']); ?>">
                                                <?php echo htmlspecialchars($task['title']); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Geen lijsten gevonden.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-container">
            <div class="form-group">
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

                    <label for="list_id">Selecteer Lijst:</label>
                    <select id="list_id" name="list_id">
                        <option value="">Geen lijst</option>
                        <?php foreach ($lists as $list): ?>
                            <option value="<?php echo htmlspecialchars($list['id']); ?>">
                                <?php echo htmlspecialchars($list['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
            
                    <label for="task_comment">Opmerking:</label>
                    <textarea id="task_comment" name="task_comment"></textarea>

                    <button type="submit" class="add-button">Voeg toe</button>
                </form>
            </div>

            <div class="form-group">
                <h3>Nieuwe Lijst Toevoegen</h3>
                <form method="POST" action="add_list.php">
                    <label for="list_name">Nieuwe Lijstnaam:</label>
                    <input type="text" id="list_name" name="list_name" required>
                    <button type="submit" class="add-button">Voeg Toe</button>
                </form>
            </div>
        </div>

        <div class="logout-container">
            <a href="logout.php" class="logout-button">Uitloggen</a>
        </div>
    </div>

    <script>
        //code voor remaining deadline
    document.addEventListener('DOMContentLoaded', function() {
        const taskItems = document.querySelectorAll('.task-item');

        taskItems.forEach(item => {
            const taskId = item.dataset.taskId; // Zorg ervoor dat je de taak-ID toevoegt aan de data-attributen

            fetch(`remaining_days.php?id=${taskId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error(data.error);
                        return;
                    }

                    const deadlineElement = item.querySelector('.task-deadline');
                    const remainingDays = data.remaining_days;

                    if (remainingDays < 0) {
                        deadlineElement.textContent = `${-remainingDays} dagen verstreken`;
                    } else if (remainingDays < 7) {
                        deadlineElement.textContent = `${remainingDays} dagen resterend`;
                    } else {
                        deadlineElement.textContent = deadlineElement.dataset.originalDate; // Houd de originele datum bij
                    }
                });
        });

        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const form = this.closest('form');
                const taskId = form.querySelector('input[name="id"]').value;

                fetch('delete_task.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        'id': taskId
                    })
                })
                .then(response => response.text())
                .then(message => {
                    if (message.includes('Taak verwijderd')) {
                        // Verwijder het taak-item uit de DOM
                        form.closest('.task-item').remove();
                    } else {
                        console.error(message);
                    }
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
    // Event listener voor delete-knoppen
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const listId = this.getAttribute('data-list-id');
            
            fetch('delete_list.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    'id': listId
                })
            })
            .then(response => response.text())
            .then(message => {
                if (message.includes('Lijst verwijderd')) {
                    // Verwijder de lijst uit de DOM
                    this.closest('.list-item-container').remove();
                } else {
                    console.error(message);
                }
            })
            .catch(error => {
                console.error('Er was een probleem met de fetch-operatie:', error);
            });
        });
    });
});



    document.addEventListener('DOMContentLoaded', function() {
    // Voeg een event listener toe aan alle status-knoppen
    document.querySelectorAll('.status-toggle').forEach(function(button) {
        button.addEventListener('click', function() {
            var taskId = this.dataset.taskId;
            var newStatus = this.dataset.newStatus;

            // AJAX-request om de status te updaten
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_task_status.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    location.reload(); // Pagina opnieuw laden om de wijzigingen te tonen
                }
            };
            xhr.send('id=' + taskId + '&status=' + newStatus);
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-task-button').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const taskId = this.getAttribute('data-task-id');
            
            fetch('delete_task.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    'id': taskId
                })
            })
            .then(response => response.text())
            .then(message => {
                if (message.includes('Taak verwijderd')) {
                    // Verwijder de taak uit de DOM
                    this.closest('.task-item').remove();
                } else {
                    console.error(message); // Toon foutmelding in de console
                }
            })
            .catch(error => {
                console.error('Er was een probleem met de fetch-operatie:', error);
            });
        });
    });
});


    </script>
</body>
</html>

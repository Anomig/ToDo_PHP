<?php
session_start();
session_destroy(); // Verwijder alle sessiegegevens
header('Location: login.php');
exit();
?>

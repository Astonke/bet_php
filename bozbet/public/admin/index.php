<?php
session_start();
// Simple admin check
if (!isset($_SESSION['admin'])) {
    // For simplicity, let's use a simple query param check.
    // In a real app, you'd have a proper admin login.
    if (!isset($_GET['secret']) || $_GET['secret'] !== 'supersecret') {
        header("Location: /");
        exit();
    }
    $_SESSION['admin'] = true;
}

require_once '../../includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Admin Panel</h1>
        <ul>
            <li><a href="create_event.php">Create New Event</a></li>
            <li><a href="settle_events.php">Settle Events</a></li>
        </ul>
    </div>
</body>
</html>

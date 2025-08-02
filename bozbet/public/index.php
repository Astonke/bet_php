<?php
session_start();
require_once '../includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bozbet - Home</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="container">
        <h1>Upcoming Events</h1>
        <div class="events-container">
            <?php
            $events = get_all_events();
            if (empty($events)) {
                echo "<p>No upcoming events. Please check back later.</p>";
            } else {
                foreach ($events as $event) {
                    echo "<div class='event'>";
                    echo "<h2>" . htmlspecialchars($event['title']) . "</h2>";
                    echo "<p>" . htmlspecialchars($event['description']) . "</p>";
                    echo "<div class='odds'>";
                    foreach ($event['outcomes'] as $outcome) {
                        echo "<a href='bet.php?event_id={$event['id']}&outcome={$outcome['name']}' class='outcome-button'>";
                        echo htmlspecialchars($outcome['name']) . " (" . htmlspecialchars($outcome['odds']) . ")";
                        echo "</a>";
                    }
                    echo "</div>";
                    echo "</div>";
                }
            }
            ?>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>

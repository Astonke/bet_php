<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: /");
    exit();
}
require_once '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $outcomes = $_POST['outcomes']; // Array of outcome names
    $odds = $_POST['odds']; // Array of odds

    $event_id = uniqid('evt_');
    $event_outcomes = [];
    for ($i = 0; $i < count($outcomes); $i++) {
        if (!empty($outcomes[$i]) && !empty($odds[$i])) {
            $event_outcomes[] = [
                'name' => $outcomes[$i],
                'odds' => floatval($odds[$i])
            ];
        }
    }

    if (!empty($title) && !empty($event_outcomes)) {
        $event_data = [
            'id' => $event_id,
            'title' => $title,
            'description' => $description,
            'outcomes' => $event_outcomes,
            'status' => 'open' // open, settled
        ];
        save_event($event_data);
        $message = "Event created successfully!";
    } else {
        $error = "Title and at least one outcome with odds are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Event</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Create New Event</h1>
        <a href="index.php">Back to Admin</a>
        
        <?php if(isset($message)): ?><p style="color:green;"><?php echo $message; ?></p><?php endif; ?>
        <?php if(isset($error)): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>

        <form action="create_event.php" method="post">
            <div class="form-group">
                <label for="title">Event Title</label>
                <input type="text" name="title" id="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description"></textarea>
            </div>
            
            <h3>Outcomes</h3>
            <div id="outcomes-wrapper">
                <div class="form-group outcome">
                    <input type="text" name="outcomes[]" placeholder="Outcome Name (e.g., Team A Wins)" required>
                    <input type="number" step="0.01" name="odds[]" placeholder="Odds (e.g., 1.5)" required>
                </div>
            </div>
            <button type="button" id="add-outcome">Add Another Outcome</button>
            <hr>
            <button type="submit" class="btn">Create Event</button>
        </form>

        <script>
            document.getElementById('add-outcome').addEventListener('click', function() {
                const wrapper = document.getElementById('outcomes-wrapper');
                const newOutcome = document.createElement('div');
                newOutcome.classList.add('form-group', 'outcome');
                newOutcome.innerHTML = `
                    <input type="text" name="outcomes[]" placeholder="Outcome Name">
                    <input type="number" step="0.01" name="odds[]" placeholder="Odds">
                `;
                wrapper.appendChild(newOutcome);
            });
        </script>
    </div>
</body>
</html>

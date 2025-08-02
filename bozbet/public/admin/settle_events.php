<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: /");
    exit();
}
require_once '../../includes/functions.php';

// Handle form submission for settling an event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['settle_event'])) {
    $event_id = $_POST['event_id'];
    $winning_outcome = $_POST['winning_outcome'];

    $event = get_event($event_id);
    if ($event && $event['status'] === 'open') {
        // Update event status
        $event['status'] = 'settled';
        $event['winning_outcome'] = $winning_outcome;
        save_event($event);

        // Find all bets on this event
        $all_bets = glob(BETS_PATH . '/*.json');
        foreach ($all_bets as $bet_file) {
            $bet = json_decode(file_get_contents($bet_file), true);
            if ($bet['event_id'] === $event_id) {
                if ($bet['outcome'] === $winning_outcome) {
                    // This is a winning bet
                    $bet['status'] = 'won';
                    $payout = $bet['stake'] * $bet['odds'];
                    
                    // Update user balance
                    $user = get_user($bet['username']);
                    if ($user) {
                        $user['balance'] += $payout;
                        save_user($user);
                    }
                } else {
                    // This is a losing bet
                    $bet['status'] = 'lost';
                }
                file_put_contents($bet_file, json_encode($bet, JSON_PRETTY_PRINT));
            }
        }
        $message = "Event settled. Winning bets have been paid out.";
    } else {
        $error = "Could not settle event. It might already be settled or does not exist.";
    }
}

$open_events = array_filter(get_all_events(), function($event) {
    return $event['status'] === 'open';
});

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settle Events</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Settle Open Events</h1>
        <a href="index.php">Back to Admin</a>

        <?php if(isset($message)): ?><p style="color:green;"><?php echo $message; ?></p><?php endif; ?>
        <?php if(isset($error)): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>

        <?php if (empty($open_events)): ?>
            <p>There are no open events to settle.</p>
        <?php else: ?>
            <?php foreach ($open_events as $event): ?>
                <div class="event" style="margin-bottom: 20px;">
                    <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                    <form action="settle_events.php" method="post">
                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                        <div class="form-group">
                            <label for="winning_outcome_<?php echo $event['id']; ?>">Winning Outcome:</label>
                            <select name="winning_outcome" id="winning_outcome_<?php echo $event['id']; ?>" required>
                                <?php foreach ($event['outcomes'] as $outcome): ?>
                                    <option value="<?php echo htmlspecialchars($outcome['name']); ?>">
                                        <?php echo htmlspecialchars($outcome['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" name="settle_event" class="btn">Settle Event</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
session_start();
require_once '../includes/functions.php';

if (!isset($_SESSION['user'])) {
    header('Location: /auth/login.php');
    exit;
}

$event_id = $_GET['event_id'] ?? null;
$selected_outcome = $_GET['outcome'] ?? null;

if (!$event_id || !$selected_outcome) {
    header('Location: /public/index.php');
    exit;
}

$event = get_event($event_id);
if (!$event) {
    die("Event not found.");
}

// Find the odds for the selected outcome
$odds = null;
foreach ($event['outcomes'] as $outcome) {
    if ($outcome['name'] === $selected_outcome) {
        $odds = $outcome['odds'];
        break;
    }
}

if ($odds === null) {
    die("Outcome not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stake = floatval($_POST['stake']);
    $user = get_user($_SESSION['user']['username']);

    if ($stake <= 0) {
        $error = "Stake must be a positive number.";
    } elseif ($stake > $user['balance']) {
        $error = "You do not have enough balance to place this bet.";
    } else {
        // Place the bet
        $bet_id = uniqid('bet_');
        $bet_data = [
            'id' => $bet_id,
            'username' => $user['username'],
            'event_id' => $event_id,
            'event_title' => $event['title'],
            'outcome' => $selected_outcome,
            'odds' => $odds,
            'stake' => $stake,
            'status' => 'active', // active, won, lost
            'timestamp' => time()
        ];

        // Deduct balance and save user
        $user['balance'] -= $stake;
        save_user($user);
        $_SESSION['user'] = $user; // Update session

        // Save bet
        file_put_contents(BETS_PATH . "/$bet_id.json", json_encode($bet_data, JSON_PRETTY_PRINT));

        $success = "Bet placed successfully! Your new balance is $" . number_format($user['balance'], 2);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Place Bet</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="container">
        <h1>Place a Bet</h1>
        
        <div class="event">
            <h2><?php echo htmlspecialchars($event['title']); ?></h2>
            <p><strong>Your Selection:</strong> <?php echo htmlspecialchars($selected_outcome); ?></p>
            <p><strong>Odds:</strong> <?php echo htmlspecialchars($odds); ?></p>
        </div>

        <?php if (isset($success)): ?>
            <p style="color:green;"><?php echo $success; ?></p>
            <a href="index.php">Back to events</a> | <a href="dashboard.php">View My Bets</a>
        <?php elseif (isset($error)): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <?php if (!isset($success)): ?>
        <form action="bet.php?event_id=<?php echo $event_id; ?>&outcome=<?php echo urlencode($selected_outcome); ?>" method="post" class="form-container">
            <div class="form-group">
                <label for="stake">Stake Amount</label>
                <input type="number" step="0.01" name="stake" id="stake" required>
            </div>
            <p>Potential Winnings: $<span id="winnings">0.00</span></p>
            <button type="submit" class="btn">Place Bet</button>
        </form>
        <?php endif; ?>

        <script>
            const stakeInput = document.getElementById('stake');
            const winningsSpan = document.getElementById('winnings');
            const odds = <?php echo $odds; ?>;

            stakeInput.addEventListener('input', function() {
                const stake = parseFloat(this.value) || 0;
                const potentialWinnings = stake * odds;
                winningsSpan.textContent = potentialWinnings.toFixed(2);
            });
        </script>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>

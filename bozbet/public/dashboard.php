<?php
session_start();
require_once '../includes/functions.php';

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header('Location: /auth/login.php');
    exit;
}

$user = get_user($_SESSION['user']['username']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bozbet</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="container">
        <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
        
        <div class="dashboard-info">
            <h2>Account Summary</h2>
            <p><strong>Balance:</strong> $<?php echo number_format($user['balance'], 2); ?></p>
        </div>

        <div class="bet-history">
            <h2>Your Bet History</h2>
            <?php
            $user_bets = get_user_bets($user['username']);
            if (empty($user_bets)) {
                echo "<p>You haven't placed any bets yet.</p>";
            } else {
                // Display bets (implementation for get_user_bets needed)
                echo "<ul>";
                foreach ($user_bets as $bet) {
                    echo "<li>";
                    echo "Bet ID: " . htmlspecialchars($bet['id']);
                    echo " | Event: " . htmlspecialchars($bet['event_title']);
                    echo " | Stake: $" . number_format($bet['stake'], 2);
                    echo " | Status: " . htmlspecialchars($bet['status']);
                    echo "</li>";
                }
                echo "</ul>";
            }
            ?>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>

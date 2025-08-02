<?php
session_start();
require_once '../../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $result = register_user($username, $password);
        if ($result === true) {
            $_SESSION['user'] = get_user($username);
            header("Location: /dashboard.php");
            exit();
        } else {
            $error = $result;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Bozbet</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>

    <main class="container">
        <div class="form-container">
            <h1>Register</h1>
            <?php if (isset($error)): ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="register.php" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <button type="submit" class="btn">Register</button>
            </form>
        </div>
    </main>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>

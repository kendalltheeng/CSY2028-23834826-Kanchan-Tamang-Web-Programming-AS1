<?php
session_start();
require 'KanTdbConnect.php';

$KanTLoginMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $KanTEmail = $_POST['KanTEmail'];
    $KanTPassword = $_POST['KanTPassword'];

    // Hardcoded admin login
    if ($KanTEmail === 'reejan@gmail.com' && $KanTPassword === 'reejanthapa') {
        $_SESSION['KanTUserId'] = 0;
        $_SESSION['KanTUserName'] = 'Reejan Thapa';
        $_SESSION['KanTRole'] = 'admin';
        header('Location: KanTadminDashboard.php');
        exit;
    }

    // Database check
    $KanTStmt = $KanTPdo->prepare('SELECT * FROM user WHERE email = ?');
    $KanTStmt->execute([$KanTEmail]);
    $KanTUser = $KanTStmt->fetch();

    if ($KanTUser && password_verify($KanTPassword, $KanTUser['password'])) {
        $_SESSION['KanTUserId'] = $KanTUser['id'];
        $_SESSION['KanTUserName'] = $KanTUser['name'];
        $_SESSION['KanTRole'] = $KanTUser['role'];

        if ($KanTUser['role'] === 'admin') {
            header('Location: KanTadminDashboard.php');
        } else {
            header('Location: KanTindex.php');
        }
        exit;
    } else {
        $KanTLoginMessage = 'Invalid email or password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KanT Login</title>
    <link rel="stylesheet" href="KanTstyle.css">
    <style>
        .kant-login-container {
            width: 70%;
            margin: 5vw auto;
            padding: 2em;
            background-color: white;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.15);
            border-radius: 1em;
        }

        .kant-login-container h1 {
            text-align: center;
            margin-bottom: 2em;
            color: #2b60f0;
        }

        .kant-form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 1.5em;
        }

        .kant-form-group label {
            font-weight: bold;
            margin-bottom: 0.5em;
        }

        .kant-form-group input {
            padding: 0.8em;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 0.5em;
        }

        .kant-form-submit {
            text-align: center;
        }

        .kant-form-submit input[type="submit"] {
            background-color: #2b60f0;
            color: white;
            padding: 0.8em 2em;
            font-size: 1em;
            border: none;
            border-radius: 0.5em;
            cursor: pointer;
        }

        .kant-form-submit input[type="submit"]:hover {
            background-color: #1f45aa;
        }

        .kant-message {
            text-align: center;
            margin-top: 1em;
            font-weight: bold;
            color: red;
        }
    </style>
</head>
<body>

<main>
    <div class="kant-login-container">
        <h1>KanT User Login</h1>

        <?php if ($KanTLoginMessage): ?>
            <p class="kant-message"><?= htmlspecialchars($KanTLoginMessage) ?></p>
        <?php endif; ?>

        <form method="POST" action="KanTlogin.php">
            <div class="kant-form-group">
                <label for="KanTEmail">Email Address</label>
                <input type="email" id="KanTEmail" name="KanTEmail" required>
            </div>

            <div class="kant-form-group">
                <label for="KanTPassword">Password</label>
                <input type="password" id="KanTPassword" name="KanTPassword" required>
            </div>

            <div class="kant-form-submit">
                <input type="submit" value="Login">
            </div>
        </form>
    </div>
</main>
</body>
</html>

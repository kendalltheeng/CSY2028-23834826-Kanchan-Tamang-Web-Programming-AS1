<?php
session_start();
require 'KanTdbConnect.php';

$KanTMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $KanTEmail = $_POST['KanTEmail'];
    $KanTName = $_POST['KanTName'];
    $KanTPassword = password_hash($_POST['KanTPassword'], PASSWORD_DEFAULT);

    function KanTRegisterUser($KanTPdo, $KanTEmail, $KanTPassword, $KanTName) {
        $KanTStmt = $KanTPdo->prepare('INSERT INTO user (email, password, name) VALUES (?, ?, ?)');
        $KanTStmt->execute([$KanTEmail, $KanTPassword, $KanTName]);
    }

    try {
        KanTRegisterUser($KanTPdo, $KanTEmail, $KanTPassword, $KanTName);
        $KanTMessage = 'Registration successful!';
    } catch (PDOException $e) {
        $KanTMessage = 'Registration failed: ' . (str_contains($e->getMessage(), 'Integrity constraint violation')
            ? 'Email already exists.'
            : $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KanT Register</title>
    <link rel="stylesheet" href="KanTstyle.css">
    <style>
        .kant-register-container {
            width: 70%;
            margin: 5vw auto;
            padding: 2em;
            background-color: white;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.15);
            border-radius: 1em;
        }

        .kant-register-container h1 {
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
            color: green;
        }

        .kant-error {
            color: red;
        }
    </style>
</head>
<body>
<main>
    <div class="kant-register-container">
        <h1>Register on KanT Auctions</h1>

        <?php if ($KanTMessage): ?>
            <p class="kant-message <?= str_starts_with($KanTMessage, 'NO') ? 'kant-error' : '' ?>">
                <?= htmlspecialchars($KanTMessage) ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="KanTregister.php">
            <div class="kant-form-group">
                <label for="KanTEmail">Email Address</label>
                <input type="email" id="KanTEmail" name="KanTEmail" required>
            </div>

            <div class="kant-form-group">
                <label for="KanTName">Full Name</label>
                <input type="text" id="KanTName" name="KanTName" required>
            </div>

            <div class="kant-form-group">
                <label for="Kan

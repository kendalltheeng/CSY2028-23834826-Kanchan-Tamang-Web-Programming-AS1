<?php
session_start();
require 'KanTdbConnect.php';

// Restrict access to admin only
if (!isset($_SESSION['KanTRole']) || $_SESSION['KanTRole'] !== 'admin') {
    header('Location: KanTindex.php');
    exit;
}

$KanTSuccessMessage = '';
$KanTErrorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $KanTCategoryName = trim($_POST['KanTCategoryName']);

    if (!empty($KanTCategoryName)) {
        $KanTStmt = $KanTPdo->prepare('INSERT INTO category (name) VALUES (?)');
        $KanTStmt->execute([$KanTCategoryName]);
        $KanTSuccessMessage = 'Category added successfully!';
    } else {
        $KanTErrorMessage = ' Please enter a category name.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Category - Admin</title>
    <link rel="stylesheet" href="KanTstyle.css">
    <style>
        .kant-cat-container {
            max-width: 600px;
            margin: 5vw auto;
            background: #fff;
            padding: 2em;
            border-radius: 1em;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #2b60f0;
            margin-bottom: 2em;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1.4em;
        }

        label {
            font-weight: 600;
        }

        input[type="text"] {
            padding: 0.8em;
            border-radius: 0.5em;
            border: 1px solid #ccc;
            font-size: 1em;
        }

        input[type="submit"] {
            background: #2b60f0;
            color: white;
            padding: 0.8em;
            font-size: 1em;
            border: none;
            border-radius: 0.5em;
            cursor: pointer;
            width: 50%;
            margin: 0 auto;
        }

        input[type="submit"]:hover {
            background-color: #1f45aa;
        }

        .kant-message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 1em;
        }

        .kant-success {
            color: green;
        }

        .kant-error {
            color: red;
        }

        .kant-back-link {
            text-align: center;
            margin-top: 2em;
        }

        .kant-back-link a {
            color: #2b60f0;
            font-weight: bold;
            text-decoration: none;
        }

        .kant-back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<main>
    <div class="kant-cat-container">
        <h1>Add New Categories</h1>

        <?php if ($KanTSuccessMessage): ?>
            <p class="kant-message kant-success"><?= $KanTSuccessMessage ?></p>
        <?php endif; ?>

        <?php if ($KanTErrorMessage): ?>
            <p class="kant-message kant-error"><?= $KanTErrorMessage ?></p>
        <?php endif; ?>

        <form method="POST" action="KanTaddCategory.php">
            <label for="KanTCategoryName">Category Name:</label>
            <input type="text" name="KanTCategoryName" id="KanTCategoryName" required>
            <input type="submit" value="Add Category">
        </form>

        <div class="kant-back-link">
            <a href="KanTadminDashboard.php">← Back to Dashboard</a>
        </div>
    </div>
</main>
</body>
</html>

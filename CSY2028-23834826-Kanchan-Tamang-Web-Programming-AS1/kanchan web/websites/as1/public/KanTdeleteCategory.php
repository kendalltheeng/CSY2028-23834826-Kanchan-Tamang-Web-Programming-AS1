<?php
session_start();
require 'KanTdbConnect.php';

// Only admin access
if (!isset($_SESSION['KanTRole']) || $_SESSION['KanTRole'] !== 'admin') {
    header('Location: KanTindex.php');
    exit;
}

$KanTCategoryId = $_GET['id'] ?? null;
$KanTCategoryName = '';
$KanTError = '';
$KanTSuccess = '';

// Get category for confirmation
if ($KanTCategoryId) {
    $KanTStmt = $KanTPdo->prepare('SELECT * FROM category WHERE id = ?');
    $KanTStmt->execute([$KanTCategoryId]);
    $KanTCategory = $KanTStmt->fetch();

    if ($KanTCategory) {
        $KanTCategoryName = $KanTCategory['name'];
    } else {
        $KanTError = 'Category not found.';
    }
} else {
    $KanTError = 'No category selected.';
}

// If confirmed, delete category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $KanTDelete = $KanTPdo->prepare('DELETE FROM category WHERE id = ?');
    $KanTDelete->execute([$KanTCategoryId]);

    header('Location: KanTadminCategories.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Category</title>
    <link rel="stylesheet" href="KanTstyle.css">
    <style>
        .kant-delete-box {
            width: 60%;
            margin: 5vw auto;
            background: white;
            padding: 2em;
            border-radius: 1em;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        h1 {
            color: #e43137;
            margin-bottom: 1.5em;
        }

        .kant-warning {
            color: #e43137;
            font-weight: bold;
            font-size: 1.1em;
            margin-bottom: 2em;
        }

        form {
            display: flex;
            justify-content: center;
            gap: 2em;
        }

        .kant-btn {
            padding: 0.7em 1.5em;
            font-size: 1em;
            font-weight: bold;
            border: none;
            border-radius: 0.5em;
            cursor: pointer;
            text-decoration: none;
        }

        .kant-btn-delete {
            background-color: #e43137;
            color: white;
        }

        .kant-btn-delete:hover {
            background-color: #c11e26;
        }

        .kant-btn-cancel {
            background-color: #aaa;
            color: white;
        }

        .kant-btn-cancel:hover {
            background-color: #888;
        }

        .kant-error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
<main>
    <div class="kant-delete-box">
        <?php if ($KanTError): ?>
            <p class="kant-error"><?= $KanTError ?></p>
            <a href="KanTadminCategories.php" class="kant-btn kant-btn-cancel">← Back</a>
        <?php else: ?>
            <h1>Delete Category</h1>
            <p class="kant-warning">Are you sure you want to delete "<strong><?= htmlspecialchars($KanTCategoryName) ?></strong>"?</p>

            <form method="POST" action="KanTdeleteCategory.php?id=<?= htmlspecialchars($KanTCategoryId) ?>">
                <input type="submit" value="Yes, Delete" class="kant-btn kant-btn-delete">
                <a href="KanTadminCategories.php" class="kant-btn kant-btn-cancel">Cancel</a>
            </form>
        <?php endif; ?>
    </div>
</main>
</body>
</html>

<?php
session_start();
require 'KanTdbConnect.php';

// Admin-only access
if (!isset($_SESSION['KanTRole']) || $_SESSION['KanTRole'] !== 'admin') {
    header('Location: KanTindex.php');
    exit;
}

$KanTCategoryId = $_GET['id'] ?? null;
$KanTCategoryName = '';
$KanTSuccessMessage = '';
$KanTErrorMessage = '';

// Fetch category info
if ($KanTCategoryId) {
    $KanTStmt = $KanTPdo->prepare('SELECT * FROM category WHERE id = ?');
    $KanTStmt->execute([$KanTCategoryId]);
    $KanTCategory = $KanTStmt->fetch();

    if (!$KanTCategory) {
        $KanTErrorMessage = 'Category not found.';
    } else {
        $KanTCategoryName = $KanTCategory['name'];
    }
} else {
    $KanTErrorMessage = 'No category ID provided.';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $KanTUpdatedName = trim($_POST['KanTCategoryName']);

    if (!empty($KanTUpdatedName)) {
        $KanTUpdate = $KanTPdo->prepare('UPDATE category SET name = ? WHERE id = ?');
        $KanTUpdate->execute([$KanTUpdatedName, $KanTCategoryId]);
        $KanTSuccessMessage = 'Category updated successfully!';
        $KanTCategoryName = $KanTUpdatedName;
    } else {
        $KanTErrorMessage = 'Please enter a category name.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Category</title>
    <link rel="stylesheet" href="KanTstyle.css">
    <style>
        .kant-editcat-container {
            width: 60%;
            margin: 5vw auto;
            background-color: white;
            padding: 2em;
            border-radius: 1em;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #2b60f0;
            margin-bottom: 2em;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1.5em;
        }

        label {
            font-weight: bold;
        }

        input[type="text"] {
            padding: 0.8em;
            border-radius: 0.5em;
            border: 1px solid #ccc;
            font-size: 1em;
        }

        input[type="submit"] {
            background-color: #2b60f0;
            color: white;
            padding: 0.8em;
            font-size: 1em;
            border: none;
            border-radius: 0.5em;
            cursor: pointer;
            width: 50%;
            align-self: center;
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

        .kant-back {
            text-align: center;
            margin-top: 2em;
        }

        .kant-back a {
            color: #2b60f0;
            font-weight: bold;
            text-decoration: none;
        }

        .kant-back a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<main>
    <div class="kant-editcat-container">
        <h1>Edit Category</h1>

        <?php if ($KanTSuccessMessage): ?>
            <p class="kant-message kant-success"><?= $KanTSuccessMessage ?></p>
        <?php endif; ?>

        <?php if ($KanTErrorMessage): ?>
            <p class="kant-message kant-error"><?= $KanTErrorMessage ?></p>
        <?php endif; ?>

        <?php if (!$KanTErrorMessage): ?>
            <form method="POST" action="KanTeditCategory.php?id=<?= htmlspecialchars($KanTCategoryId) ?>">
                <label for="KanTCategoryName">Category Name:</label>
                <input type="text" name="KanTCategoryName" id="KanTCategoryName" value="<?= htmlspecialchars($KanTCategoryName) ?>" required>
                <input type="submit" value="Update Category">
            </form>
        <?php endif; ?>

        <div class="kant-back">
            <a href="KanTadminCategories.php">← Back to Category List</a>
        </div>
    </div>
</main>
</body>
</html>

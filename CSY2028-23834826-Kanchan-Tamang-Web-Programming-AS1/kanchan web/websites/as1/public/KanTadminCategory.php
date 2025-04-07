<?php
session_start();
require 'KanTdbConnect.php';

// Admin access check
if (!isset($_SESSION['KanTRole']) || $_SESSION['KanTRole'] !== 'admin') {
    header('Location: KanTindex.php');
    exit;
}

// Fetch all categories
$KanTStmt = $KanTPdo->query('SELECT * FROM category ORDER BY name');
$KanTCategories = $KanTStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KanT Admin - Manage Categories</title>
    <link rel="stylesheet" href="KanTstyle.css">
    <style>
        .kant-admin-wrapper {
            width: 85%;
            margin: 4vw auto;
            background: #fff;
            padding: 2.5em;
            border-radius: 1em;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .kant-admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .kant-admin-header h1 {
            color: #2b60f0;
            margin: 0;
        }

        .kant-btn {
            padding: 0.6em 1.4em;
            background-color: #2b60f0;
            color: white;
            border-radius: 0.5em;
            text-decoration: none;
            font-weight: bold;
        }

        .kant-btn:hover {
            background-color: #1f45aa;
        }

        table {
            width: 100%;
            margin-top: 2em;
            border-collapse: collapse;
        }

        th, td {
            padding: 1em;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }

        th {
            background: #f3f3f3;
        }

        .kant-actions a {
            color: #2b60f0;
            font-weight: bold;
            margin-right: 1em;
        }

        .kant-actions a:hover {
            text-decoration: underline;
        }

        .kant-back-link {
            margin-top: 2em;
            text-align: center;
        }

        .kant-back-link a {
            text-decoration: none;
            color: #2b60f0;
            font-weight: bold;
        }

        .kant-back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<main>
    <div class="kant-admin-wrapper">
        <div class="kant-admin-header">
            <h1>Manage Categories</h1>
            <a href="KanTaddCategory.php" class="kant-btn">Add Categories</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Category Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($KanTCategories as $KanTCategory): ?>
                    <tr>
                        <td><?= htmlspecialchars($KanTCategory['name']) ?></td>
                        <td class="kant-actions">
                            <a href="KanTeditCategory.php?id=<?= $KanTCategory['id'] ?>">Edit</a>
                            <a href="KanTdeleteCategory.php?id=<?= $KanTCategory['id'] ?>" onclick="return confirm('Are you sure you want to delete this category?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="kant-back-link">
            <a href="KanTadminDashboard.php">← Back to Dashboard</a>
        </div>
    </div>
</main>
</body>
</html>

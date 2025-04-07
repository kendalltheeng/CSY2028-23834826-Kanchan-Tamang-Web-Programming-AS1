<?php
session_start();
require 'KanTdbConnect.php';

// Admin access only
if (!isset($_SESSION['KanTRole']) || $_SESSION['KanTRole'] !== 'admin') {
    header('Location: KanTindex.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KanT Admin Dashboard</title>
    <link rel="stylesheet" href="KanTstyle.css">
    <style>
        .kant-dashboard {
            max-width: 900px;
            margin: 5vw auto;
            background: #fff;
            padding: 3em;
            border-radius: 1em;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .kant-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .kant-header h1 {
            color: #2b60f0;
            margin: 0;
        }

        .kant-logout-btn {
            background-color: #e43137;
            color: white;
            padding: 0.6em 1.2em;
            border-radius: 0.5em;
            font-weight: bold;
            text-decoration: none;
        }

        .kant-logout-btn:hover {
            background-color: #c11e26;
        }

        .kant-dashboard ul {
            list-style: none;
            padding: 0;
            margin-top: 2em;
        }

        .kant-dashboard li {
            margin: 1.5em 0;
        }

        .kant-dashboard a.kant-link {
            display: inline-block;
            padding: 1em 2em;
            background-color: #2b60f0;
            color: white;
            font-weight: bold;
            border-radius: 0.6em;
            text-decoration: none;
        }

        .kant-dashboard a.kant-link:hover {
            background-color: #1f45aa;
        }
    </style>
</head>
<body>
<main>
    <div class="kant-dashboard">
        <div class="kant-header">
            <h1>Welcome, <?= htmlspecialchars($_SESSION['KanTUserName']) ?> </h1>
            <a href="KanTlogout.php" class="kant-logout-btn">Logout</a>
        </div>

        <ul>
            <li><a href="KanTadminCategories.php" class="kant-link">Manage Categories</a></li>
            <!-- Future admin features can go here -->
            <!-- <li><a href="#" class="kant-link">View All Auctions</a></li> -->
            <!-- <li><a href="#" class="kant-link">Manage Users</a></li> -->
        </ul>
    </div>
</main>
</body>
</html>

<?php
session_start();
require 'KanTdbConnect.php';

if (!isset($_SESSION['KanTUserId'])) {
    header('Location: KanTlogin.php');
    exit;
}

$KanTAuctionId = $_GET['id'] ?? null;
$KanTUserId = $_SESSION['KanTUserId'];

$KanTSuccessMessage = '';
$KanTErrorMessage = '';

if (!$KanTAuctionId || !is_numeric($KanTAuctionId)) {
    header('Location: KanTindex.php');
    exit;
}

$KanTCatStmt = $KanTPdo->query("SELECT * FROM category ORDER BY name");
$KanTCategories = $KanTCatStmt->fetchAll();

$KanTStmt = $KanTPdo->prepare("SELECT * FROM auction WHERE id = ? AND user_id = ?");
$KanTStmt->execute([$KanTAuctionId, $KanTUserId]);
$KanTAuction = $KanTStmt->fetch();

if (!$KanTAuction) {
    die("You are not authorized to edit this auction.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $KanTTitle = trim($_POST['KanTTitle']);
    $KanTCategoryId = $_POST['KanTCategoryId'];
    $KanTDescription = trim($_POST['KanTDescription']);
    $KanTCurrentBid = floatval($_POST['KanTCurrentBid']);
    $KanTEndTime = $_POST['KanTEndTime'];

    if (!empty($KanTTitle) && $KanTCurrentBid > 0 && !empty($KanTDescription) && !empty($KanTEndTime)) {
        $KanTUpdate = $KanTPdo->prepare("UPDATE auction SET title = ?, category_id = ?, description = ?, current_bid = ?, end_time = ? WHERE id = ? AND user_id = ?");
        $KanTUpdate->execute([$KanTTitle, $KanTCategoryId, $KanTDescription, $KanTCurrentBid, $KanTEndTime, $KanTAuctionId, $KanTUserId]);

        $KanTSuccessMessage = "Auction updated successfully!";
        $KanTAuction['title'] = $KanTTitle;
        $KanTAuction['category_id'] = $KanTCategoryId;
        $KanTAuction['description'] = $KanTDescription;
        $KanTAuction['current_bid'] = $KanTCurrentBid;
        $KanTAuction['end_time'] = $KanTEndTime;
    } else {
        $KanTErrorMessage = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Auction</title>
    <link rel="stylesheet" href="KanTstyle.css">
    <style>
        .kant-edit-container {
            width: 700px;
            margin: 5vw auto;
            background: #fff;
            padding: 2em;
            border-radius: 1em;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .kant-message {
            text-align: center;
            font-weight: bold;
        }

        .kant-success {
            color: green;
        }

        .kant-error {
            color: red;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1.5em;
        }

        label {
            font-weight: bold;
        }

        input, textarea, select {
            padding: 0.8em;
            border: 1px solid #ccc;
            border-radius: 0.5em;
        }

        .btn-row {
            display: flex;
            justify-content: center;
            gap: 1em;
        }

        .btn {
            padding: 0.8em 2em;
            border: none;
            border-radius: 0.5em;
            cursor: pointer;
        }

        .btn-update {
            background: #2b60f0;
            color: white;
        }

        .btn-update:hover {
            background: #1f45aa;
        }

        .btn-cancel {
            background: #aaa;
            color: white;
            text-decoration: none;
            display: inline-block;
            line-height: 2.2em;
        }

        .btn-cancel:hover {
            background: #888;
        }
    </style>
</head>
<body>
<main>
    <div class="kant-edit-container">
        <h1>Edit Auction</h1>

        <?php if ($KanTSuccessMessage): ?>
            <p class="kant-message kant-success"><?= $KanTSuccessMessage ?></p>
        <?php elseif ($KanTErrorMessage): ?>
            <p class="kant-message kant-error"><?= $KanTErrorMessage ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="KanTTitle">Title:</label>
            <input type="text" name="KanTTitle" value="<?= htmlspecialchars($KanTAuction['title']) ?>" required>

            <label for="KanTCategoryId">Category:</label>
            <select name="KanTCategoryId" required>
                <?php foreach ($KanTCategories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $KanTAuction['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="KanTDescription">Description:</label>
            <textarea name="KanTDescription" required><?= htmlspecialchars($KanTAuction['description']) ?></textarea>

            <label for="KanTCurrentBid">Current Bid (£):</label>
            <input type="number" name="KanTCurrentBid" step="0.01" value="<?= htmlspecialchars($KanTAuction['current_bid']) ?>" required>

            <label for="KanTEndTime">End Time:</label>
            <input type="datetime-local" name="KanTEndTime" value="<?= date('Y-m-d\TH:i', strtotime($KanTAuction['end_time'])) ?>" required>

            <div class="btn-row">
                <input type="submit" class="btn btn-update" value="Update">
                <a href="KanTindex.php" class="btn btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</main>
</body>
</html>

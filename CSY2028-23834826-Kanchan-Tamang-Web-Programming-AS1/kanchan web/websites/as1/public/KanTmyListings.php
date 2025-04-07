<?php
session_start();
require 'KanTdbConnect.php';

// Only for logged-in regular users
if (!isset($_SESSION['KanTUserId']) || $_SESSION['KanTRole'] !== 'user') {
    header('Location: KanTlogin.php');
    exit;
}

$KanTUserId = $_SESSION['KanTUserId'];

// Fetch auctions listed by this user
$KanTStmt = $KanTPdo->prepare("
    SELECT auction.*, category.name AS category_name
    FROM auction
    JOIN category ON auction.category_id = category.id
    WHERE auction.user_id = ?
    ORDER BY auction.id DESC
");
$KanTStmt->execute([$KanTUserId]);
$KanTMyAuctions = $KanTStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Listings - KanT Auctions</title>
    <link rel="stylesheet" href="KanTstyle.css">
    <style>
        main {
            max-width: 1000px;
            margin: auto;
            padding: 2em;
        }

        h1 {
            text-align: center;
            color: #2b60f0;
            margin-bottom: 2em;
        }

        .kant-listing {
            border: 1px solid #ccc;
            border-radius: 0.5em;
            padding: 1.5em;
            margin-bottom: 1.5em;
            background-color: #fff;
        }

        .kant-listing h2 {
            margin-top: 0;
        }

        .kant-buttons {
            margin-top: 1em;
        }

        .kant-buttons a {
            display: inline-block;
            margin-right: 1em;
            padding: 0.5em 1.2em;
            border-radius: 0.5em;
            color: white;
            background-color: #2b60f0;
            text-decoration: none;
        }

        .kant-buttons a.delete {
            background-color: #e43137;
        }

        .kant-buttons a:hover {
            opacity: 0.85;
        }

        .kant-empty {
            text-align: center;
            color: #888;
            font-style: italic;
        }

        .authLink {
            background-color: #2b60f0;
            color: white !important;
            padding: 0.5em 1.2em;
            border-radius: 0.5em;
            text-decoration: none;
            font-weight: bold;
        }

        .authLink:hover {
            background-color: #1f45aa;
        }
    </style>
</head>
<body>
<main>
    <h1>Auction Listings</h1>

    <?php if (count($KanTMyAuctions) > 0): ?>
        <?php foreach ($KanTMyAuctions as $auction): ?>
            <div class="kant-listing">
                <h2><?= htmlspecialchars($auction['title']) ?></h2>
                <p><strong>Category:</strong> <?= htmlspecialchars($auction['category_name']) ?></p>
                <p><strong>Current Bid:</strong> £<?= number_format($auction['current_bid'], 2) ?></p>
                <p><strong>Ends:</strong> <?= date('d M Y, H:i', strtotime($auction['end_time'])) ?></p>

                <div class="kant-buttons">
                    <a href="KanTeditAuction.php?id=<?= $auction['id'] ?>"> Edit</a>
                    <a href="KanTdeleteAuction.php?id=<?= $auction['id'] ?>" class="delete" onclick="return confirm('Are you sure you want to delete this auction?');"> Delete</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="kant-empty">You haven’t listed any auctions yet.</p>
    <?php endif; ?>

    <div style="text-align: center; margin-top: 2em;">
        <a href="KanTindex.php" class="authLink">← Back to Home</a>
    </div>
</main>
</body>
</html>

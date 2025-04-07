<?php
session_start();
require 'KanTdbConnect.php';

$KanTCategoryId = $_GET['id'] ?? null;

if (!$KanTCategoryId || !is_numeric($KanTCategoryId)) {
    header('Location: KanTindex.php');
    exit;
}

// Get category name
$KanTCatStmt = $KanTPdo->prepare("SELECT * FROM category WHERE id = ?");
$KanTCatStmt->execute([$KanTCategoryId]);
$KanTCategory = $KanTCatStmt->fetch();

if (!$KanTCategory) {
    echo "<p style='text-align:center;'>Category not found.</p>";
    exit;
}

// Get auctions by category
$KanTStmt = $KanTPdo->prepare("
    SELECT auction.*, user.name AS seller_name 
    FROM auction 
    JOIN user ON auction.user_id = user.id 
    WHERE category_id = ?
    ORDER BY auction.id DESC
");
$KanTStmt->execute([$KanTCategoryId]);
$KanTAuctions = $KanTStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($KanTCategory['name']) ?> Auctions</title>
    <link rel="stylesheet" href="KanTstyle.css">
    <style>
        .kant-link {
            background-color: #2b60f0;
            color: white !important;
            padding: 0.6em 1.2em;
            border-radius: 0.5em;
            font-weight: bold;
            display: inline-block;
            text-align: center;
            text-decoration: none;
        }

        .kant-link:hover {
            background-color: #1f45aa;
        }

        .carList {
            list-style: none;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5em;
            margin-top: 2em;
        }

        .carList li {
            background: #fff;
            padding: 1em;
            border-radius: 1em;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }

        .carList img {
            width: 100%;
            border-radius: 0.5em;
        }

        .price {
            color: #0b8;
            font-weight: bold;
            margin: 0.5em 0;
        }

        .auctionLink {
            display: inline-block;
            margin-top: 0.8em;
            color: #0056b3;
            text-decoration: underline;
            font-weight: bold;
        }

        .auctionLink:hover {
            color: #004099;
        }
    </style>
</head>
<body>

<?php include 'KanTheader.php'; ?>

<main style="max-width: 1000px; margin: auto; padding: 2em;">
    <h1>Auctions in "<?= htmlspecialchars($KanTCategory['name']) ?>"</h1>

    <ul class="carList">
        <?php if (count($KanTAuctions) > 0): ?>
            <?php foreach ($KanTAuctions as $auction): ?>
                <li>
                    <img src="car.png" alt="Car">
                    <article>
                        <h2><?= htmlspecialchars($auction['title']) ?></h2>
                        <p><?= nl2br(htmlspecialchars($auction['description'])) ?></p>
                        <p class="price">Current bid: £<?= number_format($auction['current_bid'], 2) ?></p>
                        <p>Listed by <strong><?= htmlspecialchars($auction['seller_name']) ?></strong></p>
                        <p><em>Ends: <?= date('d M Y, H:i', strtotime($auction['end_time'])) ?></em></p>
                        <a href="KanTviewAuction.php?id=<?= $auction['id'] ?>" class="auctionLink">More &gt;&gt;</a>
                    </article>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No auctions found in this category.</p>
        <?php endif; ?>
    </ul>

    <div style="text-align: center; margin-top: 2em;">
        <a href="KanTindex.php" class="kant-link">← Back to Home</a>
    </div>
</main>

<?php include 'KanTfooter.php'; ?>
</body>
</html>

<?php
session_start();
require 'KanTdbConnect.php';

// ✅ Fetch all categories
$KanTCatStmt = $KanTPdo->query('SELECT * FROM category ORDER BY name');
$KanTCategories = $KanTCatStmt->fetchAll();

$KanTMainCategories = array_slice($KanTCategories, 0, 6);
$KanTExtraCategories = array_slice($KanTCategories, 6);

// ✅ Fetch latest 10 auctions
$KanTStmt = $KanTPdo->query("
    SELECT auction.*, category.name AS category_name, user.name AS seller_name
    FROM auction
    JOIN category ON auction.category_id = category.id
    JOIN user ON auction.user_id = user.id
    ORDER BY auction.id DESC
    LIMIT 10
");
$KanTAuctions = $KanTStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KanT Auto Auctions</title>
    <link rel="stylesheet" href="KanTstyle.css" />
    <style>
        .kant-nav-dropdown {
            position: relative;
        }

        .kant-nav-content {
            display: none;
            position: absolute;
            top: 3em;
            right: 0;
            background-color: #fff;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            min-width: 12em;
            z-index: 2000;
            border-radius: 0.5em;
            padding: 0.75em 0;
        }

        .kant-nav-content li {
            list-style: none;
        }

        .kant-nav-content li a {
            display: block;
            padding: 0.6em 1.2em;
            color: #333;
            text-decoration: none;
        }

        .kant-nav-content li a:hover {
            background-color: #f5f5f5;
        }

        .kant-nav-dropdown:hover .kant-nav-content {
            display: block;
        }

        .category-item {
            padding: 0.4em 0.6em;
            display: inline-block;
            text-decoration: none;
            font-weight: bold;
            color: #222;
        }

        .carList {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5em;
            list-style-type: none;
            padding: 0;
        }

        .carList li {
            background: #fafafa;
            padding: 1em;
            border-radius: 0.8em;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
        }

        .carList li:hover {
            transform: translateY(-5px);
        }

        .carList img {
            width: 100%;
            height: auto;
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
    </style>
</head>

<body>
<?php include 'KanTheader.php'; ?>

<main>
    <?php if (isset($_SESSION['KanTUserName'])): ?>
        <p style="text-align: center; font-size: 1.1em; margin-bottom: 1.2em;">
            👋 Hello, <?= htmlspecialchars($_SESSION['KanTUserName']); ?>!
        </p>
    <?php endif; ?>

    <?php if (isset($_SESSION['KanTUserId']) && $_SESSION['KanTRole'] === 'user'): ?>
        <div style="text-align: center; margin-bottom: 2em;">
            <a href="KanTmyListings.php" class="authLink" style="padding: 0.6em 1.6em; font-size: 1em;">
                📄 View My Listings
            </a>
        </div>
    <?php endif; ?>

    <h1>Fresh Car Listings</h1>
    <ul class="carList">
        <?php foreach ($KanTAuctions as $auction): ?>
            <li>
                <?php if (!empty($auction['image']) && file_exists($auction['image'])): ?>
                    <img src="<?= htmlspecialchars($auction['image']) ?>" alt="Car">
                <?php else: ?>
                    <img src="car.png" alt="Default car">
                <?php endif; ?>
                <article>
                    <h2><?= htmlspecialchars($auction['title']) ?></h2>
                    <h3><?= htmlspecialchars($auction['category_name']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($auction['description'])) ?></p>
                    <p class="price">Current bid: £<?= number_format($auction['current_bid'], 2) ?></p>
                    <p>Posted by <strong><?= htmlspecialchars($auction['seller_name']) ?></strong></p>
                    <p><em>Ends on: <?= date('d M Y, H:i', strtotime($auction['end_time'])) ?></em></p>
                    <a href="KanTviewAuction.php?id=<?= $auction['id'] ?>" class="auctionLink">More &gt;&gt;</a>
                </article>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php include 'KanTfooter.php'; ?>
</main>
</body>
</html>

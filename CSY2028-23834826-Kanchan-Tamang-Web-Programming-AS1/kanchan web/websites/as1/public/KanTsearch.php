<?php
session_start();
require 'KanTdbConnect.php';

$KanTQuery = $_GET['query'] ?? '';
$KanTAuctions = [];

if ($KanTQuery) {
    $KanTStmt = $KanTPdo->prepare("
        SELECT auction.*, category.name AS category_name, user.name AS seller_name
        FROM auction
        JOIN category ON auction.category_id = category.id
        JOIN user ON auction.user_id = user.id
        WHERE auction.title LIKE ? OR auction.description LIKE ?
        ORDER BY auction.id DESC
    ");
    $KanTStmt->execute(["%$KanTQuery%", "%$KanTQuery%"]);
    $KanTAuctions = $KanTStmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results for '<?= htmlspecialchars($KanTQuery) ?>'</title>
    <link rel="stylesheet" href="KanTstyle.css">
</head>
<body>
<main style="max-width: 900px; margin: auto; padding: 2em;">
    <h1>Search Results for '<?= htmlspecialchars($KanTQuery) ?>'</h1>

    <?php if (count($KanTAuctions) > 0): ?>
        <ul class="carList">
            <?php foreach ($KanTAuctions as $auction): ?>
                <li>
                    <img src="car.png" alt="Car image">
                    <article>
                        <h2><?= htmlspecialchars($auction['title']) ?></h2>
                        <h3><?= htmlspecialchars($auction['category_name']) ?></h3>
                        <p><?= nl2br(htmlspecialchars($auction['description'])) ?></p>
                        <p class="price">Current bid: £<?= number_format($auction['current_bid'], 2) ?></p>
                        <p>Listed by <strong><?= htmlspecialchars($auction['seller_name']) ?></strong></p>
                        <p><em>Ends: <?= date('d M Y, H:i', strtotime($auction['end_time'])) ?></em></p>
                        <a href="KanTviewAuction.php?id=<?= $auction['id'] ?>" class="more auctionLink">More &gt;&gt;</a>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No auctions found matching your search.</p>
    <?php endif; ?>

    <div style="text-align:center; margin-top:2em;">
        <a href="KanTindex.php" class="authLink">← Back to Home</a>
    </div>
</main>
</body>
</html>

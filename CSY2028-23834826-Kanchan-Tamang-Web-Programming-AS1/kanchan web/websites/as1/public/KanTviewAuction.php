<?php
session_start();
require 'KanTdbConnect.php';

$KanTAuctionId = $_GET['id'] ?? null;
$KanTUserId = $_SESSION['KanTUserId'] ?? null;

if (!$KanTAuctionId || !is_numeric($KanTAuctionId)) {
    header('Location: KanTindex.php');
    exit;
}

// Get Auction Details
$KanTStmt = $KanTPdo->prepare("
    SELECT auction.*, category.name AS category_name, user.name AS seller_name
    FROM auction
    JOIN category ON auction.category_id = category.id
    JOIN user ON auction.user_id = user.id
    WHERE auction.id = ?
");
$KanTStmt->execute([$KanTAuctionId]);
$KanTAuction = $KanTStmt->fetch();

if (!$KanTAuction) {
    echo "<p style='text-align:center;'>Auction not found.</p>";
    exit;
}

// Handle Review
if (isset($_POST['KanTReviewText']) && $KanTUserId) {
    $KanTReviewText = trim($_POST['KanTReviewText']);
    if ($KanTReviewText) {
        $KanTReviewStmt = $KanTPdo->prepare("INSERT INTO review (user_id, auction_id, content, review_date) VALUES (?, ?, ?, NOW())");
        $KanTReviewStmt->execute([$KanTUserId, $KanTAuctionId, $KanTReviewText]);
    }
}

// Handle Bid
if (isset($_POST['KanTBidAmount']) && $KanTUserId && $KanTUserId != $KanTAuction['user_id']) {
    $KanTBid = floatval($_POST['KanTBidAmount']);
    if ($KanTBid > $KanTAuction['current_bid']) {
        $KanTInsertBid = $KanTPdo->prepare("INSERT INTO bid (user_id, auction_id, amount) VALUES (?, ?, ?)");
        $KanTInsertBid->execute([$KanTUserId, $KanTAuctionId, $KanTBid]);

        $KanTUpdateAuction = $KanTPdo->prepare("UPDATE auction SET current_bid = ? WHERE id = ?");
        $KanTUpdateAuction->execute([$KanTBid, $KanTAuctionId]);

        $KanTAuction['current_bid'] = $KanTBid;
    }
}

// Fetch Reviews
$KanTReviewFetch = $KanTPdo->prepare("
    SELECT review.content, review.review_date, user.name 
    FROM review 
    JOIN user ON review.user_id = user.id 
    WHERE auction_id = ? 
    ORDER BY review.review_date DESC
");
$KanTReviewFetch->execute([$KanTAuctionId]);
$KanTReviews = $KanTReviewFetch->fetchAll();

// Fetch Bid History
$KanTBidsStmt = $KanTPdo->prepare("
    SELECT bid.amount, bid.bid_time, user.name 
    FROM bid 
    JOIN user ON bid.user_id = user.id 
    WHERE bid.auction_id = ?
    ORDER BY bid.bid_time DESC
");
$KanTBidsStmt->execute([$KanTAuctionId]);
$KanTBids = $KanTBidsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($KanTAuction['title']) ?> - Auction Details</title>
    <link rel="stylesheet" href="KanTstyle.css">
    <style>
        main { max-width: 900px; margin: auto; padding: 2em; background: white; border-radius: 1em; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #2b60f0; text-align: center; }
        .kant-section { margin-bottom: 2em; }
        .kant-price { font-size: 2em; font-weight: bold; color: red; }
        .kant-label { font-weight: bold; margin-top: 1em; display: block; }
        .kant-review-box, .kant-bid-box { margin-top: 1em; }
        textarea, input[type="number"], input[type="submit"] {
            width: 100%; padding: 0.8em; border-radius: 0.5em; border: 1px solid #ccc; margin-top: 0.5em;
        }
        input[type="submit"] {
            background-color: #2b60f0;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #1f45aa;
        }
        ul { list-style: none; padding-left: 0; }
        ul li { margin-bottom: 0.5em; }
        .authLink {
            background-color: #2b60f0;
            color: white !important;
            padding: 0.5em 1em;
            margin-top: 2em;
            display: inline-block;
            border-radius: 0.5em;
            font-weight: bold;
            text-decoration: none;
        }
        .authLink:hover {
            background-color: #1f45aa;
        }
    </style>
</head>
<body>
<main>
    <h1><?= htmlspecialchars($KanTAuction['title']) ?></h1>

    <div class="kant-section">
        <p><strong>Category:</strong> <?= htmlspecialchars($KanTAuction['category_name']) ?></p>
        <p><strong>Seller:</strong> <?= htmlspecialchars($KanTAuction['seller_name']) ?></p>
        <p><strong>Description:</strong><br> <?= nl2br(htmlspecialchars($KanTAuction['description'])) ?></p>
        <p class="kant-price">Current Bid: £<?= number_format($KanTAuction['current_bid'], 2) ?></p>
        <p><strong>Ends:</strong> <?= date('d M Y, H:i', strtotime($KanTAuction['end_time'])) ?></p>
    </div>

    <?php if (isset($_SESSION['KanTUserId']) && $_SESSION['KanTUserId'] != $KanTAuction['user_id']): ?>
        <div class="kant-bid-box">
            <form method="POST">
                <label for="KanTBidAmount" class="kant-label">Place a Bid:</label>
                <input type="number" name="KanTBidAmount" step="0.01" min="<?= $KanTAuction['current_bid'] + 0.01 ?>" required>
                <input type="submit" value="Place Bid">
            </form>
        </div>
    <?php endif; ?>

    <div class="kant-section">
        <h2>Bid History</h2>
        <ul>
            <?php if (count($KanTBids) > 0): ?>
                <?php foreach ($KanTBids as $bid): ?>
                    <li>
                         £<?= number_format($bid['amount'], 2) ?>
                        by <strong><?= htmlspecialchars($bid['name']) ?></strong>
                        on <?= date('d M Y, H:i', strtotime($bid['bid_time'])) ?>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No bids yet.</li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="kant-section">
        <h2>Reviews</h2>
        <ul>
            <?php foreach ($KanTReviews as $review): ?>
                <li>
                    <strong><?= htmlspecialchars($review['name']) ?>:</strong>
                    <?= htmlspecialchars($review['content']) ?>
                    <em>(<?= date('d M Y', strtotime($review['review_date'])) ?>)</em>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if ($KanTUserId): ?>
            <div class="kant-review-box">
                <form method="POST">
                    <label for="KanTReviewText" class="kant-label">Add Your Review:</label>
                    <textarea name="KanTReviewText" required></textarea>
                    <input type="submit" value="Add Review">
                </form>
            </div>
        <?php endif; ?>
    </div>

    <div style="text-align:center;">
        <a href="KanTindex.php" class="authLink">← Back to Listings</a>
    </div>
</main>
</body>
</html>

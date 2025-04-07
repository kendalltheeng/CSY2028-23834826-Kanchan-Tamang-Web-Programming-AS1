<?php
session_start();
require 'KanTdbConnect.php';

// Allow only logged-in users
if (!isset($_SESSION['KanTUserId'])) {
    header('Location: KanTlogin.php');
    exit;
}

$KanTSuccessMsg = '';
$KanTErrorMsg = '';

// Fetch categories
$KanTCatStmt = $KanTPdo->query('SELECT * FROM category ORDER BY name');
$KanTCategories = $KanTCatStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $KanTTitle = trim($_POST['KanTTitle']);
    $KanTCategoryId = $_POST['KanTCategoryId'];
    $KanTDesc = trim($_POST['KanTDescription']);
    $KanTStartBid = floatval($_POST['KanTCurrentBid']);
    $KanTEndTime = $_POST['KanTEndTime'];

    $KanTImagePath = '';

    // 📷 Handle image upload
    if (isset($_FILES['KanTImage']) && $_FILES['KanTImage']['error'] === UPLOAD_ERR_OK) {
        $KanTTemp = $_FILES['KanTImage']['tmp_name'];
        $KanTFileName = basename($_FILES['KanTImage']['name']);
        $KanTNewPath = 'uploads/' . time() . '_' . $KanTFileName;

        if (move_uploaded_file($KanTTemp, $KanTNewPath)) {
            $KanTImagePath = $KanTNewPath;
        } else {
            $KanTErrorMsg = 'Image upload failed.';
        }
    }

    if (!empty($KanTTitle) && !empty($KanTCategoryId) && !empty($KanTDesc) && $KanTStartBid > 0 && !empty($KanTEndTime)) {
        $KanTStmt = $KanTPdo->prepare('
            INSERT INTO auction (title, description, category_id, user_id, current_bid, end_time, image)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ');
        $KanTStmt->execute([$KanTTitle, $KanTDesc, $KanTCategoryId, $_SESSION['KanTUserId'], $KanTStartBid, $KanTEndTime, $KanTImagePath]);

        $KanTSuccessMsg = 'Car auction added successfully!';
    } else {
        $KanTErrorMsg = 'Please fill in all required fields correctly.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Car Auction</title>
    <link rel="stylesheet" href="KanTstyle.css">
    <style>
        .kant-add-wrapper {
            max-width: 700px;
            margin: 4vw auto;
            background: #fff;
            padding: 2em;
            border-radius: 1em;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #2b60f0;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1.4em;
        }

        label {
            font-weight: 600;
        }

        input, select, textarea {
            padding: 0.8em;
            border-radius: 0.5em;
            border: 1px solid #bbb;
            font-size: 1em;
        }

        input[type="submit"] {
            background: #2b60f0;
            color: white;
            border: none;
            width: 50%;
            margin: 1em auto 0;
            cursor: pointer;
            font-weight: bold;
        }

        input[type="submit"]:hover {
            background: #2147bb;
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

        .kant-cancel {
            text-align: center;
            margin-top: 1em;
        }

        .kant-cancel a {
            padding: 0.7em 2em;
            background: #999;
            border-radius: 0.5em;
            color: #fff;
            text-decoration: none;
        }

        .kant-cancel a:hover {
            background: #777;
        }
    </style>
</head>
<body>

<main>
    <div class="kant-add-wrapper">
        <h1>Add Car Auction</h1>

        <?php if ($KanTSuccessMsg): ?>
            <p class="kant-message kant-success"><?= $KanTSuccessMsg ?></p>
        <?php endif; ?>

        <?php if ($KanTErrorMsg): ?>
            <p class="kant-message kant-error"><?= $KanTErrorMsg ?></p>
        <?php endif; ?>

        <form method="POST" action="KanTaddAuction.php" enctype="multipart/form-data">
            <label for="KanTTitle">Car Title:</label>
            <input type="text" name="KanTTitle" id="KanTTitle" required>

            <label for="KanTCategoryId">Category:</label>
            <select name="KanTCategoryId" id="KanTCategoryId" required>
                <option value="">-- Select --</option>
                <?php foreach ($KanTCategories as $cat): ?>
                    <option value="<?= $cat['id']; ?>"><?= htmlspecialchars($cat['name']); ?></option>
                <?php endforeach; ?>
            </select>

            <label for="KanTDescription">Description:</label>
            <textarea name="KanTDescription" id="KanTDescription" required></textarea>

            <label for="KanTCurrentBid">Starting Bid (£):</label>
            <input type="number" name="KanTCurrentBid" step="0.01" required>

            <label for="KanTEndTime">Auction End Date:</label>
            <input type="datetime-local" name="KanTEndTime" required>

            <label for="KanTImage">Upload Car Image:</label>
            <input type="file" name="KanTImage" id="KanTImage" accept="image/*" required>

            <input type="submit" value="Add Listing">
        </form>

        <div class="kant-cancel">
            <a href="KanTindex.php">Cancel</a>
        </div>
    </div>

    <?php include 'KanTfooter.php'; ?>
</main>
</body>
</html>

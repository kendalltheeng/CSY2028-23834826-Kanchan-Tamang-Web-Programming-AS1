<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require 'KanTdbConnect.php';

// Fetch categories
$KanTCatStmt = $KanTPdo->query('SELECT * FROM category ORDER BY name');
$KanTCategories = $KanTCatStmt->fetchAll();
$KanTMainCategories = array_slice($KanTCategories, 0, 6);
$KanTExtraCategories = array_slice($KanTCategories, 6);
?>

<header>
    <h1>
        <span class="C">C</span><span class="a">a</span><span class="r">r</span>
        <span class="b">b</span><span class="u">u</span><span class="y">y</span>
    </h1>

    <form action="KanTsearch.php" method="GET">
        <input type="text" name="search" placeholder="Search for a car" />
        <input type="submit" value="Search" />
    </form>
</header>

<nav>
    <ul>
        <?php foreach ($KanTMainCategories as $cat): ?>
            <li><a class="categoryLink" href="KanTcategory.php?id=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
        <?php endforeach; ?>

        <?php if (count($KanTExtraCategories) > 0): ?>
            <li class="kant-dropdown">
                <a class="categoryLink" href="#">More ▼</a>
                <ul class="kant-dropdown-content">
                    <?php foreach ($KanTExtraCategories as $cat): ?>
                        <li><a class="categoryLink" href="KanTcategory.php?id=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endif; ?>

        <?php if (isset($_SESSION['KanTUserName'])): ?>
            <?php if ($_SESSION['KanTRole'] === 'user'): ?>
                <li><a class="authLink" href="KanTaddAuction.php">Add Auction</a></li>
            <?php endif; ?>
            <li style="margin-left:auto;"><a class="authLink" href="KanTlogout.php">Logout</a></li>
        <?php else: ?>
            <li style="margin-left:auto;"><a class="authLink" href="KanTlogin.php">Login</a></li>
            <li><a class="authLink" href="KanTregister.php">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>

<img src="banners/1.jpg" alt="Banner" />

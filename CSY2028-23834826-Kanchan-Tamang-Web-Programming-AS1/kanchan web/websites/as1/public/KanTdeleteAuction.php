<?php
session_start();
require 'KanTdbConnect.php';

if (!isset($_SESSION['KanTUserId']) || !isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: KanTindex.php');
    exit;
}

$KanTAuctionId = $_GET['id'];
$KanTUserId = $_SESSION['KanTUserId'];

$KanTStmt = $KanTPdo->prepare("DELETE FROM auction WHERE id = ? AND user_id = ?");
$KanTStmt->execute([$KanTAuctionId, $KanTUserId]);

header('Location: KanTindex.php');
exit;
?>

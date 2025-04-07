<?php
$KanTHost = 'mysql'; 
$KanTDb = 'ijdb'; 
$KanTUser = 'student';
$KanTPass = 'student';
$KanTCharset = 'utf8mb4';

$KanTDsn = "mysql:host=$KanTHost;dbname=$KanTDb;charset=$KanTCharset";

$KanTOptions = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $KanTPdo = new PDO($KanTDsn, $KanTUser, $KanTPass, $KanTOptions);
} catch (PDOException $e) {
    die('KanT Database Connection Error: ' . $e->getMessage());
}
?>

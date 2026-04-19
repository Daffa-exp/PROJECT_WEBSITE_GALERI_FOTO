<?php
require_once 'config/database.php';
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Login dulu']);
    exit;
}

$photo_id = $_POST['photo_id'];
$user_id = $_SESSION['user_id'];

$pdo->prepare("INSERT INTO likes (photo_id, user_id) VALUES (?,?)")->execute([$photo_id, $user_id]);

$count = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE photo_id = ?");
$count->execute([$photo_id]);
$total = $count->fetchColumn();

echo json_encode(['likes' => $total, 'liked' => true]);
?>
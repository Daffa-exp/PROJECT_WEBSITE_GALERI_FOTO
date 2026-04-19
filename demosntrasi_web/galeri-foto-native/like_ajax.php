<?php
require_once 'config/database.php';

$photo_id = $_POST['photo_id'];

$pdo->prepare("INSERT INTO likes (photo_id, user_id) VALUES (?,?)")->execute([$photo_id, 0]);

$count = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE photo_id = ?");
$count->execute([$photo_id]);
echo $count->fetchColumn();
?>
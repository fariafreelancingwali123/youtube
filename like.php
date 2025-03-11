<?php
include 'includes/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Login required");
}

$user_id = $_SESSION['user_id'];
$video_id = $_POST['video_id'];

$conn->query("INSERT INTO likes (user_id, video_id) VALUES ($user_id, $video_id)");
echo "Liked!";
?>

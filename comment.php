<?php
include 'includes/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Login required");
}

$user_id = $_SESSION['user_id'];
$video_id = $_POST['video_id'];
$comment = $_POST['comment'];

$conn->query("INSERT INTO comments (user_id, video_id, comment) VALUES ($user_id, $video_id, '$comment')");
echo "Comment added!";
?>

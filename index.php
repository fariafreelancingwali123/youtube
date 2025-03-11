<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch videos
$query = "SELECT id, title, description, file_path FROM videos ORDER BY created_at DESC";
$result = $conn->query($query);

// Handle Comment Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["comment_submit"])) {
    $video_id = $_POST["video_id"];
    $user_id = $_SESSION["user_id"];
    $comment = trim($_POST["comment_text"]);

    if (!empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO comments (video_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $video_id, $user_id, $comment);
        $stmt->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Home - YouTube Clone</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background: #ff0000;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar h1 {
            margin: 0;
            font-size: 22px;
        }
        .nav-buttons a {
            text-decoration: none;
            color: white;
            background: #333;
            padding: 8px 15px;
            border-radius: 5px;
            margin-left: 10px;
        }
        .video-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
        }
        .video-card {
            width: 320px;
            background: white;
            margin: 10px;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: left;
        }
        .video-card video {
            width: 100%;
            border-radius: 5px;
        }
        .video-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }
        .video-description {
            font-size: 14px;
            color: #555;
        }
        .action-buttons {
            margin-top: 10px;
            display: flex;
            gap: 10px;
        }
        .action-button {
            padding: 8px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
            color: white;
        }
        .like { background: #007bff; }
        .comment { background: #28a745; }
        .subscribe { background: #ff0000; }
        .comment-section {
            margin-top: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .comment-box {
            background: #f1f1f1;
            padding: 8px;
            border-radius: 5px;
            margin-top: 5px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <h1>YouTube Clone</h1>
        <div class="nav-buttons">
            <a href="upload_video.php">Upload Video</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Video Grid -->
    <div class="video-grid">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="video-card">
                <video controls>
                    <source src="<?php echo htmlspecialchars($row['file_path']); ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <div class="video-title"><?php echo htmlspecialchars($row['title']); ?></div>
                <div class="video-description"><?php echo htmlspecialchars($row['description']); ?></div>

                <!-- Like, Comment, Subscribe Buttons -->
                <div class="action-buttons">
                    <button class="action-button like">Like</button>
                    <button class="action-button subscribe">Subscribe</button>
                </div>

                <!-- Comment Form -->
                <form method="POST">
                    <input type="hidden" name="video_id" value="<?php echo $row['id']; ?>">
                    <input type="text" name="comment_text" placeholder="Write a comment..." required>
                    <button type="submit" name="comment_submit" class="action-button comment">Comment</button>
                </form>

                <!-- Show Comments -->
                <div class="comment-section">
                    <?php
                    $video_id = $row['id'];
                    $comment_query = "SELECT users.username, comments.comment FROM comments JOIN users ON comments.user_id = users.id WHERE comments.video_id = ? ORDER BY comments.created_at DESC";
                    $stmt = $conn->prepare($comment_query);
                    $stmt->bind_param("i", $video_id);
                    $stmt->execute();
                    $comments_result = $stmt->get_result();

                    while ($comment_row = $comments_result->fetch_assoc()) {
                        echo "<div class='comment-box'><strong>" . htmlspecialchars($comment_row['username']) . ":</strong> " . htmlspecialchars($comment_row['comment']) . "</div>";
                    }
                    ?>
                </div>

            </div>
        <?php } ?>
    </div>

</body>
</html>

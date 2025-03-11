<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    die("Error: You must be logged in to upload videos.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["video"]) && $_FILES["video"]["error"] == 0) {
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $user_id = $_SESSION["user_id"];

    // Create upload directory if not exists
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $video = $_FILES["video"];
    $video_name = time() . "_" . basename($video["name"]);
    $video_path = $upload_dir . $video_name;
    
    // Allowed video formats
    $allowed_types = ["video/mp4", "video/avi", "video/mov", "video/mkv"];
    if (!in_array($video["type"], $allowed_types)) {
        die("Error: Only MP4, AVI, MOV, and MKV files are allowed.");
    }

    // Check file size (100MB limit)
    if ($video["size"] > 100 * 1024 * 1024) {
        die("Error: Video file is too large. Max size is 100MB.");
    }

    // Move uploaded file
    if (move_uploaded_file($video["tmp_name"], $video_path)) {
        // Insert into database
        $query = "INSERT INTO videos (user_id, title, description, file_path) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isss", $user_id, $title, $description, $video_path);

        if ($stmt->execute()) {
            echo "Video uploaded successfully!";
        } else {
            die("Database error: " . $stmt->error);
        }
    } else {
        die("Error: Upload failed. Check folder permissions.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Upload Video - YouTube Clone</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        form { max-width: 500px; margin: auto; padding: 20px; border: 1px solid #ccc; }
        input, textarea, button { width: 100%; padding: 10px; margin: 10px 0; }
        button { background: red; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>

    <h2>Upload a New Video</h2>

    <form action="upload_video.php" method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Enter video title" required>
        <textarea name="description" placeholder="Enter video description" required></textarea>
        <input type="file" name="video" accept="video/*" required>
        <button type="submit" name="upload">Upload Video</button>
    </form>

</body>
</html>

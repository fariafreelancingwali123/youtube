<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]); // Get email
    $password = trim($_POST["password"]);

    if (!empty($username) && !empty($email) && !empty($password)) {
        // Check if username or email already exists
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $checkStmt->bind_param("ss", $username, $email);
        $checkStmt->execute();
        $checkStmt->store_result();
        
        if ($checkStmt->num_rows > 0) {
            echo "Error: Username or Email already taken. Please choose another.";
        } else {
            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $_SESSION["user_id"] = $stmt->insert_id;
                $_SESSION["username"] = $username;
                header("Location: index.php");
                exit();
            } else {
                echo "Signup failed. Try again!";
            }
        }
    } else {
        echo "Error: All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Signup</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        form { max-width: 300px; margin: auto; background: #f4f4f4; padding: 20px; border-radius: 10px; }
        input { display: block; width: 100%; padding: 8px; margin: 10px 0; }
        button { background: red; color: white; padding: 10px; border: none; cursor: pointer; }
    </style>
</head>
<body>

<h2>Signup</h2>
<form method="POST">
    <input type="text" name="username" placeholder="Enter Username" required>
    <input type="email" name="email" placeholder="Enter Email" required>
    <input type="password" name="password" placeholder="Enter Password" required>
    <button type="submit">Signup</button>
</form>

</body>
</html>

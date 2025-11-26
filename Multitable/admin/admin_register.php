<?php
require_once __DIR__ . '/../includes/db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = trim($_POST['email']);
    $role = 'manager';
    $restaurant_name = trim($_POST['restaurant_name']);

    // Create restaurant entry
    $stmt = $conn->prepare("INSERT INTO restaurants (name) VALUES (?)");
    $stmt->bind_param("s", $restaurant_name);
    $stmt->execute();
    $restaurant_id = $stmt->insert_id;
    $stmt->close();

    // Register hotel manager
    $q = $conn->prepare("INSERT INTO admins (username, password, email, role, restaurant_id) VALUES (?, ?, ?, ?, ?)");
    $q->bind_param("ssssi", $username, $password, $email, $role, $restaurant_id);
    $q->execute();
    $q->close();

    echo "<script>alert('Hotel Manager registered successfully! You can now log in.'); window.location='admin_login.php';</script>";
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Register Hotel Manager</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="login-container">
  <h2>Register Hotel Manager</h2>
  <form method="POST">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="text" name="restaurant_name" placeholder="Restaurant Name" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Register</button>
  </form>
  <p><a href="admin_login.php">Back to Login</a></p>
</div>
</body>
</html>

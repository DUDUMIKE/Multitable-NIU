<?php
require_once __DIR__ . '/../includes/db_connect.php';
session_start();

if ($_SESSION['admin']['role'] !== 'manager') {
  die("Access denied");
}

$restaurant_id = $_SESSION['admin']['restaurant_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = 'staff';

    $stmt = $conn->prepare("INSERT INTO admins (username, email, password, role, restaurant_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $username, $email, $password, $role, $restaurant_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Staff added successfully!'); window.location='manager_dashboard.php';</script>";
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Add Staff</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="login-container">
  <h2>Add Staff</h2>
  <form method="POST">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Add Staff</button>
  </form>
</div>
</body>
</html>

<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $restaurant_name = trim($_POST['restaurant_name']);

    if (empty($username) || empty($email) || empty($_POST['password']) || empty($restaurant_name)) {
        $message = "⚠️ Please fill in all fields.";
    } else {
        // Check if email exists
        $check = $conn->prepare("SELECT id FROM admins WHERE email=?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "⚠️ Email already registered.";
        } else {
            // Create restaurant (hotel)
            $stmt = $conn->prepare("INSERT INTO restaurants (name, location, cuisine) VALUES (?, '', '')");
            $stmt->bind_param("s", $restaurant_name);
            $stmt->execute();
            $restaurant_id = $stmt->insert_id;
            $stmt->close();

            // Register manager (pending approval)
            $role = 'manager';
            $status = 'pending';
            $q = $conn->prepare("INSERT INTO admins (username,email,password,role,restaurant_id,status) VALUES (?,?,?,?,?,?)");
            $q->bind_param("ssssss", $username, $email, $password, $role, $restaurant_id, $status);
            $q->execute();
            $q->close();

            $message = "✅ Registration successful! Wait for admin approval before you can log in.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Register Hotel Manager</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body {font-family:"Poppins",sans-serif;background:#f7f9fc;display:flex;justify-content:center;align-items:center;height:100vh;}
.box {background:#fff;padding:2rem;border-radius:10px;box-shadow:0 4px 15px rgba(0,0,0,0.1);width:400px;}
input,button {width:100%;padding:10px;margin:6px 0;border-radius:8px;border:1px solid #ccc;}
button {background:#111;color:#fff;cursor:pointer;font-weight:600;}
button:hover {background:#333;}
.msg {margin-bottom:10px;text-align:center;}
</style>
</head>
<body>
<div class="box">
  <h2>Register Hotel Manager</h2>
  <?php if ($message): ?>
    <p class="msg"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>
  <form method="POST">
    <input type="text" name="username" placeholder="Manager Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="text" name="restaurant_name" placeholder="Hotel/Restaurant Name" required>
    <button type="submit">Register</button>
  </form>
  <p style="text-align:center;margin-top:10px;">
    <a href="index.php">Back to Login</a>
  </p>
</div>
</body>
</html>

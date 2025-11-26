<?php
require_once __DIR__ . '/includes/db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM admins WHERE email=?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $res = $stmt->get_result();

  if ($res->num_rows > 0) {
    $admin = $res->fetch_assoc();
    if (password_verify($password, $admin['password'])) {
      $_SESSION['admin'] = [
        'id' => $admin['id'],
        'username' => $admin['username'],
        'role' => $admin['role'],
        'restaurant_id' => $admin['restaurant_id']
      ];

      // Redirect by role
      if ($admin['role'] === 'admin') header("Location: dashboard_admin.php");
      elseif ($admin['role'] === 'manager') header("Location: dashboard_manager.php");
      else header("Location: dashboard_staff.php");
      exit;
    } else $error = "Invalid password.";
  } else $error = "No user found.";
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Admin Login</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
.login-container { width:350px; margin:100px auto; background:white; padding:25px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
input { width:100%; padding:10px; margin:8px 0; border-radius:6px; border:1px solid #ccc; }
button { width:100%; padding:10px; background:#111; color:white; border:none; border-radius:6px; cursor:pointer; }
button:hover { background:#333; }
a { color:#007bff; text-decoration:none; }
.error { color:red; text-align:center; margin-bottom:10px; }
</style>
</head>
<body>
<div class="login-container">
  <h2>Admin Login</h2>
  <?php if(!empty($error)) echo "<div class='error'>$error</div>"; ?>
  <form method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
  </form>
  <p style="text-align:center;margin-top:10px;">
    <a href="register_manager.php">Register as Hotel Manager</a>
  </p>
</div>
</body>
</html>

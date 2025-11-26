<?php
// register.php
session_start();
require_once __DIR__ . '/includes/db_connect.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    if ($name === '' || $email === '' || $pass === '') $err = "All fields required.";
    else {
        // check existing
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param('s',$email);
        $stmt->execute(); $stmt->store_result();
        if ($stmt->num_rows > 0) $err = "Email already registered.";
        else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $ins = $conn->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?, 'customer')");
            $ins->bind_param('sss',$name,$email,$hash);
            if ($ins->execute()) {
                $_SESSION['user'] = ['id'=>$ins->insert_id,'name'=>$name,'email'=>$email,'role'=>'customer'];
                header("Location: index.php"); exit;
            } else $err = "Registration failed.";
            $ins->close();
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Register — MultiTable</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body>
<div class="auth-box">
  <h2>Create account</h2>
  <?php if ($err): ?><div class="form-message error"><?=htmlspecialchars($err)?></div><?php endif; ?>
  <form method="post" class="auth-form">
    <div class="form-group"><label>Name</label><input name="name" required></div>
    <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
    <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
    <button class="primary-btn" type="submit">Register</button>
    <p class="auth-links">Have account? <a href="login.php">Login</a></p>
  </form>
</div>
</body>
</html>

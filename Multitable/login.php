<?php
// login.php
session_start();
require_once __DIR__ . '/includes/db_connect.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    if ($email === '' || $pass === '') $err = "All fields required.";
    else {
        $stmt = $conn->prepare("SELECT id,name,password,role FROM users WHERE email = ?");
        $stmt->bind_param('s',$email);
        $stmt->execute(); $stmt->store_result();
        if ($stmt->num_rows === 0) $err = "Invalid credentials.";
        else {
            $stmt->bind_result($id,$name,$hash,$role);
            $stmt->fetch();
            if (password_verify($pass, $hash)) {
                $_SESSION['user'] = ['id'=>$id,'name'=>$name,'email'=>$email,'role'=>$role];
                header("Location: index.php"); exit;
            } else $err = "Invalid credentials.";
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Login — MultiTable</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body>
<div class="auth-box">
  <h2>Login</h2>
  <?php if ($err): ?><div class="form-message error"><?=htmlspecialchars($err)?></div><?php endif; ?>
  <form method="post" class="auth-form">
    <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
    <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
    <button class="primary-btn" type="submit">Login</button>
    <p class="auth-links">No account? <a href="register.php">Register</a></p>
  </form>
</div>
</body>
</html>

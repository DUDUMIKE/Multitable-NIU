<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

$error = '';

if (isset($_SESSION['admin'])) {
    $role = $_SESSION['admin']['role'];
    if ($role === 'admin') header("Location: dashboard_admin.php");
    elseif ($role === 'manager') header("Location: dashboard_manager.php");
    else header("Location: dashboard_staff.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = "Please fill in both fields.";
    } else {
        $stmt = $conn->prepare("SELECT id, username AS name, password, role, restaurant_id, status FROM admins WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                if ($user['status'] === 'pending') {
                    $error = "Your account is awaiting admin approval.";
                } elseif ($user['status'] === 'suspended') {
                    $error = "Your account has been suspended. Please contact support.";
                } elseif (password_verify($password, $user['password'])) {
                    $_SESSION['admin'] = [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'role' => $user['role'],
                        'restaurant_id' => $user['restaurant_id']
                    ];

                    if ($user['role'] === 'admin') header("Location: dashboard_admin.php");
                    elseif ($user['role'] === 'manager') header("Location: dashboard_manager.php");
                    else header("Location: dashboard_staff.php");
                    exit();
                } else {
                    $error = "Incorrect password.";
                }
            } else {
                $error = "Account not found.";
            }
            $stmt->close();
        } else {
            $error = "Database error: failed to prepare statement.";
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Login | MultiTable</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background: #f6f8fa;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
    .auth-box {
      background: #fff;
      padding: 2rem 2.5rem;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      width: 380px;
    }
    h2 {
      text-align: center;
      margin-bottom: 1.5rem;
      color: #333;
    }
    .form-group { margin-bottom: 1rem; }
    label { display: block; font-weight: 500; margin-bottom: 0.3rem; }
    input {
      width: 100%;
      padding: .7rem;
      border: 1px solid #ccc;
      border-radius: 8px;
    }
    button.primary-btn {
      width: 100%;
      background: #111;
      color: white;
      border: none;
      padding: .75rem;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
    }
    button.primary-btn:hover { background: #333; }
    .form-message.error {
      background: #ffe3e3;
      color: #c0392b;
      padding: .8rem;
      border-radius: 8px;
      margin-bottom: 1rem;
      text-align: center;
    }
  </style>
</head>
<body>
<div class="auth-box">
  <h2>Admin / Manager / Staff Login</h2>

  <?php if (!empty($error)): ?>
    <div class="form-message error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" autocomplete="off">
    <div class="form-group">
      <label>Email</label>
      <input name="email" type="email" required>
    </div>

    <div class="form-group">
      <label>Password</label>
      <input name="password" type="password" required>
    </div>

    <button class="primary-btn" type="submit">Login</button>
  </form>

  <p style="text-align:center; margin-top:15px; font-size:0.9rem;">
    <a href="register_manager.php" style="color:#007bff; text-decoration:none;">
      ➕ Register as Hotel Manager
    </a>
  </p>
</div>
</body>
</html>

<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';
if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'manager') {
  header("Location: index.php");
  exit;
}

$restaurant_id = intval($_SESSION['admin']['restaurant_id']);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_staff'])) {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);
  if ($name && $email && $password) {
    $check = $conn->prepare("SELECT id FROM admins WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) $message = "⚠️ Email already exists.";
    else {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $conn->prepare("INSERT INTO admins (username, email, password, role, restaurant_id, status) VALUES (?, ?, ?, 'staff', ?, 'approved')");
      $stmt->bind_param("sssi", $name, $email, $hash, $restaurant_id);
      $stmt->execute();
      $message = "✅ Staff added successfully.";
      $stmt->close();
    }
  }
}

// Delete staff
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $conn->query("DELETE FROM admins WHERE id=$id AND role='staff' AND restaurant_id=$restaurant_id");
  $message = "🗑️ Staff removed.";
}

$staff = $conn->query("SELECT id, username, email FROM admins WHERE restaurant_id=$restaurant_id AND role='staff'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Manage Staff</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body { font-family:"Poppins",sans-serif; background:#f6f8fa; margin:0; color:#333; }
.container { max-width:900px; margin:40px auto; background:#fff; padding:30px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.1); }
table { width:100%; border-collapse:collapse; margin-top:15px; }
th, td { padding:10px; border-bottom:1px solid #eee; text-align:left; }
th { background:#111; color:#fff; }
button, a.btn { background:#111; color:white; border:none; padding:8px 14px; border-radius:6px; text-decoration:none; cursor:pointer; }
button:hover, a.btn:hover { background:#333; }
</style>
</head>
<body>
<div class="container">
  <a href="dashboard_manager.php" class="btn">← Back</a>
  <h2>Manage Staff</h2>
  <?php if ($message): ?><p><?= htmlspecialchars($message) ?></p><?php endif; ?>

  <form method="POST">
    <input type="text" name="name" placeholder="Staff Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button name="add_staff" type="submit">Add Staff</button>
  </form>

  <table>
    <tr><th>ID</th><th>Name</th><th>Email</th><th>Action</th></tr>
    <?php while ($s = $staff->fetch_assoc()): ?>
      <tr>
        <td><?= $s['id'] ?></td>
        <td><?= htmlspecialchars($s['username']) ?></td>
        <td><?= htmlspecialchars($s['email']) ?></td>
        <td><a href="?delete=<?= $s['id'] ?>" class="btn" onclick="return confirm('Delete this staff?')">Delete</a></td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>
</body>
</html>

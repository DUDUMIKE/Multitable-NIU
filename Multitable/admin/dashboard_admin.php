<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'admin') {
  header("Location: index.php");
  exit;
}

$admin_name = $_SESSION['admin']['name'];

// Fetch stats
$restCount = $conn->query("SELECT COUNT(*) AS total FROM restaurants")->fetch_assoc()['total'];
$manCount = $conn->query("SELECT COUNT(*) AS total FROM admins WHERE role='manager'")->fetch_assoc()['total'];
$bookCount = $conn->query("SELECT COUNT(*) AS total FROM bookings")->fetch_assoc()['total'];
$payCount = $conn->query("SELECT COUNT(*) AS total FROM payments")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin Dashboard | MultiTable</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body { font-family:"Poppins",sans-serif; background:#f7f8fa; margin:0; color:#333; }
.dashboard { max-width:1100px; margin:40px auto; background:#fff; padding:30px; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.1); }
h1 { color:#111; }
nav a { text-decoration:none; color:#fff; background:#111; padding:8px 14px; border-radius:6px; margin-right:8px; }
nav a:hover { background:#333; }
.grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:20px; margin-top:30px; }
.card { background:#fafafa; padding:20px; border-radius:8px; text-align:center; box-shadow:0 3px 8px rgba(0,0,0,0.05); }
.card h3 { margin:10px 0; }
</style>
</head>
<body>

<div class="dashboard">
  <nav>
    <h1>👑 Welcome, <?= htmlspecialchars($admin_name) ?> (Admin)</h1>
    <div>
      <a href="manage_restaurants.php">Manage Restaurants</a>
      <a href="view_bookings.php">Bookings</a>
      <a href="view_payments.php">Payments</a>
      <a href="../logout.php" style="background:#c0392b;">Logout</a>
    </div>
  </nav>

  <div class="grid">
    <div class="card"><h3>Restaurants</h3><p><?= $restCount ?> total</p></div>
    <div class="card"><h3>Managers</h3><p><?= $manCount ?> active</p></div>
    <div class="card"><h3>Bookings</h3><p><?= $bookCount ?> total</p></div>
    <div class="card"><h3>Payments</h3><p><?= $payCount ?> processed</p></div>
  </div>
</div>

</body>
</html>

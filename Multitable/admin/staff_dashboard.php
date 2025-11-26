<?php
session_start();
include 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'staff') {
  header("Location: admin_login.php");
  exit();
}
$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Staff Dashboard | MultiTable</title>
  <style>
    body { font-family: Poppins, sans-serif; background: #f4f6f9; padding: 20px; }
    .nav {
      background: #111; color: white;
      padding: 1rem; border-radius: 8px;
      display: flex; justify-content: space-between; align-items: center;
    }
    table {
      width: 100%; border-collapse: collapse; margin-top: 2rem;
      background: white; border-radius: 8px; overflow: hidden;
    }
    th, td { padding: 12px; border-bottom: 1px solid #eee; }
    th { background: #f7f7f7; }
  </style>
</head>
<body>
<div class="nav">
  <h2>👨‍🍳 Staff Dashboard</h2>
  <a href="logout.php" style="color:white;">Logout</a>
</div>

<h3>Today's Bookings</h3>
<table>
  <tr><th>Customer</th><th>Restaurant</th><th>Date</th><th>Time</th><th>Table</th><th>Status</th></tr>
  <?php
  $today = date('Y-m-d');
  $res = $conn->query("SELECT b.*, u.name AS user_name, r.name AS restaurant_name, t.table_name
                       FROM bookings b
                       JOIN users u ON b.user_id = u.id
                       JOIN restaurants r ON b.restaurant_id = r.id
                       JOIN tables t ON b.table_id = t.id
                       WHERE b.booking_date = '$today'
                       ORDER BY b.booking_time ASC");
  while ($row = $res->fetch_assoc()) {
    echo "<tr>
      <td>{$row['user_name']}</td>
      <td>{$row['restaurant_name']}</td>
      <td>{$row['booking_date']}</td>
      <td>{$row['booking_time']}</td>
      <td>{$row['table_name']}</td>
      <td>{$row['status']}</td>
    </tr>";
  }
  ?>
</table>
</body>
</html>

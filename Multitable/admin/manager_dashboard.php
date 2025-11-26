<?php
session_start();
include 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
  header("Location: admin_login.php");
  exit();
}
$user_id = $_SESSION['user_id'];

// get manager’s restaurant
$restaurant = $conn->query("SELECT * FROM restaurants WHERE manager_id = $user_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manager Dashboard | MultiTable</title>
  <style>
    body { font-family: Poppins, sans-serif; background: #f4f6f9; padding: 20px; }
    h2 { color: #111; }
    .nav {
      display: flex; justify-content: space-between;
      align-items: center; background: #111; color: white;
      padding: 1rem; border-radius: 8px;
    }
    table {
      width: 100%; background: #fff; border-collapse: collapse;
      margin-top: 2rem; border-radius: 8px; overflow: hidden;
    }
    th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
    th { background: #f7f7f7; }
  </style>
</head>
<body>
<div class="nav">
  <h2>🏨 Manager Dashboard</h2>
  <a href="logout.php" style="color:white;">Logout</a>
</div>

<h3>Welcome! Managing: <?= htmlspecialchars($restaurant['name']) ?></h3>

<table>
  <tr><th>Booking ID</th><th>Customer</th><th>Date</th><th>Time</th><th>Table</th><th>Status</th></tr>
  <?php
  $res = $conn->query("SELECT b.*, u.name AS user_name, t.table_name
                       FROM bookings b
                       JOIN users u ON b.user_id = u.id
                       JOIN tables t ON b.table_id = t.id
                       WHERE b.restaurant_id = {$restaurant['id']}
                       ORDER BY b.booking_date DESC");
  while ($row = $res->fetch_assoc()) {
    echo "<tr>
      <td>{$row['id']}</td>
      <td>{$row['user_name']}</td>
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

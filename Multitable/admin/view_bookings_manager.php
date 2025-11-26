<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'manager') {
  header("Location: index.php");
  exit;
}

$restaurant_id = intval($_SESSION['admin']['restaurant_id']);

$query = $conn->prepare("
  SELECT b.id, b.booking_date, b.booking_time, b.guests, b.status,
         u.name AS customer_name, t.table_name
  FROM bookings b
  JOIN users u ON b.user_id = u.id
  JOIN tables t ON b.table_id = t.id
  WHERE b.restaurant_id = ?
  ORDER BY b.booking_date DESC, b.booking_time DESC
");
$query->bind_param("i", $restaurant_id);
$query->execute();
$result = $query->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Bookings | Manager</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body { font-family:"Poppins",sans-serif; background:#f7f8fa; margin:0; color:#333; }
.container { max-width:1000px; margin:40px auto; background:#fff; padding:25px; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.1); }
h2 { margin-bottom:20px; }
table { width:100%; border-collapse:collapse; margin-top:10px; }
th, td { padding:10px; border-bottom:1px solid #eee; text-align:left; }
th { background:#111; color:#fff; }
.status { font-weight:bold; }
.status.pending { color:orange; }
.status.confirmed { color:green; }
.status.cancelled { color:red; }
.back { text-decoration:none; background:#111; color:white; padding:8px 12px; border-radius:6px; }
</style>
</head>
<body>

<div class="container">
  <a href="dashboard_manager.php" class="back">← Back</a>
  <h2>Bookings for Your Restaurant</h2>
  <table>
    <tr>
      <th>ID</th>
      <th>Customer</th>
      <th>Table</th>
      <th>Date</th>
      <th>Time</th>
      <th>Guests</th>
      <th>Status</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['customer_name']) ?></td>
        <td><?= htmlspecialchars($row['table_name']) ?></td>
        <td><?= htmlspecialchars($row['booking_date']) ?></td>
        <td><?= htmlspecialchars($row['booking_time']) ?></td>
        <td><?= htmlspecialchars($row['guests']) ?></td>
        <td class="status <?= htmlspecialchars($row['status']) ?>"><?= ucfirst($row['status']) ?></td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>
</body>
</html>

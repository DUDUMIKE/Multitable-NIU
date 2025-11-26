<?php
session_start();
require_once __DIR__ . '/includes/db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT b.*, t.table_name, r.name AS restaurant_name 
                        FROM bookings b 
                        JOIN tables t ON b.table_id = t.id
                        JOIN restaurants r ON b.restaurant_id = r.id
                        WHERE b.user_id = $user_id
                        ORDER BY b.booking_date DESC");
?>
<!DOCTYPE html>
<html>
<head>
<title>My Bookings</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<h2>My Bookings</h2>
<table border="1" cellpadding="10" cellspacing="0">
<tr>
  <th>Restaurant</th><th>Table</th><th>Date</th><th>Time</th><th>Status</th>
</tr>
<?php while($b = $result->fetch_assoc()): ?>
<tr>
  <td><?=htmlspecialchars($b['restaurant_name'])?></td>
  <td><?=htmlspecialchars($b['table_name'])?></td>
  <td><?=htmlspecialchars($b['booking_date'])?></td>
  <td><?=htmlspecialchars($b['booking_time'])?></td>
  <td><?=htmlspecialchars($b['status'])?></td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>

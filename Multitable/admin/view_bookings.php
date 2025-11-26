<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'admin') {
  header("Location: index.php");
  exit;
}

// Fetch all bookings
$role = $_SESSION['admin']['role'];
$where = "";

if ($role === 'manager' || $role === 'staff') {
  $restaurant_id = $_SESSION['admin']['restaurant_id'];
  $where = "WHERE b.restaurant_id = $restaurant_id";
}

$query = "
  SELECT b.id, b.booking_date, b.booking_time, b.guests, b.status,
         u.name AS customer_name, r.name AS restaurant_name, t.table_name
  FROM bookings b
  JOIN users u ON b.user_id = u.id
  JOIN restaurants r ON b.restaurant_id = r.id
  JOIN tables t ON b.table_id = t.id
  $where
  ORDER BY b.id DESC
";



$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>All Bookings | MultiTable</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body { font-family:"Poppins",sans-serif; background:#f6f8fa; padding:30px; }
h2 { margin-bottom:10px; color:#111; }
a.back { text-decoration:none; color:#fff; background:#111; padding:8px 12px; border-radius:6px; }
table { width:100%; border-collapse:collapse; margin-top:20px; background:white; border-radius:8px; overflow:hidden; }
th, td { padding:10px; border-bottom:1px solid #ddd; text-align:left; }
th { background:#111; color:white; }
.status { font-weight:600; }
.pending { color:orange; }
.confirmed { color:green; }
.cancelled { color:red; }
</style>
</head>
<body>

<h2>All Bookings</h2>
<a href="dashboard_admin.php" class="back">← Back to Dashboard</a>

<table>
<tr>
  <th>ID</th>
  <th>Customer</th>
  <th>Restaurant</th>
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
  <td><?= htmlspecialchars($row['restaurant_name']) ?></td>
  <td><?= htmlspecialchars($row['table_name']) ?></td>
  <td><?= htmlspecialchars($row['booking_date']) ?></td>
  <td><?= htmlspecialchars($row['booking_time']) ?></td>
  <td><?= htmlspecialchars($row['guests']) ?></td>
  <td class="status <?= strtolower($row['status']) ?>"><?= ucfirst($row['status']) ?></td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>

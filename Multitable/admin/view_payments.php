<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'admin') {
  header("Location: index.php");
  exit;
}

// Fetch all payments //Just addded/modified
$role = $_SESSION['admin']['role'];
$where = "";

if ($role === 'manager' || $role === 'staff') {
  $restaurant_id = $_SESSION['admin']['restaurant_id'];
  $where = "WHERE b.restaurant_id = $restaurant_id";
}

$query = "
  SELECT p.id, p.amount, p.created_at AS payment_date, p.status,
         b.id AS booking_id, r.name AS restaurant_name, u.name AS customer_name
  FROM payments p
  JOIN bookings b ON p.booking_id = b.id
  JOIN restaurants r ON b.restaurant_id = r.id
  JOIN users u ON b.user_id = u.id
  $where
  ORDER BY p.id DESC
";



$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Payments | MultiTable</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body { font-family:"Poppins",sans-serif; background:#f6f8fa; padding:30px; }
h2 { margin-bottom:10px; color:#111; }
a.back { text-decoration:none; color:#fff; background:#111; padding:8px 12px; border-radius:6px; }
table { width:100%; border-collapse:collapse; margin-top:20px; background:white; border-radius:8px; overflow:hidden; }
th, td { padding:10px; border-bottom:1px solid #ddd; text-align:left; }
th { background:#111; color:white; }
.status { font-weight:600; }
.paid { color:green; }
.pending { color:orange; }
.failed { color:red; }
</style>
</head>
<body>

<h2>All Payments</h2>
<a href="dashboard_admin.php" class="back">← Back to Dashboard</a>

<table>
<tr>
  <th>Payment ID</th>
  <th>Booking ID</th>
  <th>Customer</th>
  <th>Restaurant</th>
  <th>Amount (₹)</th>
  <th>Date</th>
  <th>Status</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
  <td><?= $row['id'] ?></td>
  <td><?= $row['booking_id'] ?></td>
  <td><?= htmlspecialchars($row['customer_name']) ?></td>
  <td><?= htmlspecialchars($row['restaurant_name']) ?></td>
  <td><?= number_format($row['amount'],2) ?></td>
  <td><?= htmlspecialchars($row['payment_date']) ?></td>
  <td class="status <?= strtolower($row['status']) ?>"><?= ucfirst($row['status']) ?></td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>

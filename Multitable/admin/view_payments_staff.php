<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';
if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'staff') {
  header("Location: index.php");
  exit;
}

$restaurant_id = intval($_SESSION['admin']['restaurant_id']);
$query = $conn->prepare("
  SELECT p.id, p.amount, p.payment_date, p.status,
         b.id AS booking_id, u.name AS customer_name
  FROM payments p
  JOIN bookings b ON p.booking_id = b.id
  JOIN users u ON b.user_id = u.id
  WHERE b.restaurant_id = ?
  ORDER BY p.payment_date DESC
");
$query->bind_param("i", $restaurant_id);
$query->execute();
$result = $query->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Payments | Staff</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body { font-family:"Poppins",sans-serif; background:#f7f8fa; color:#333; }
.container { max-width:1000px; margin:40px auto; background:#fff; padding:25px; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.1); }
table { width:100%; border-collapse:collapse; }
th, td { padding:10px; border-bottom:1px solid #eee; }
th { background:#111; color:#fff; }
.status.paid { color:green; font-weight:bold; }
.status.pending { color:orange; font-weight:bold; }
.status.failed { color:red; font-weight:bold; }
.back { text-decoration:none; background:#111; color:white; padding:8px 12px; border-radius:6px; }
</style>
</head>
<body>
<div class="container">
  <a href="dashboard_staff.php" class="back">← Back</a>
  <h2>Payments for Your Restaurant</h2>
  <table>
    <tr><th>ID</th><th>Booking ID</th><th>Customer</th><th>Amount</th><th>Date</th><th>Status</th></tr>
    <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['booking_id'] ?></td>
        <td><?= htmlspecialchars($row['customer_name']) ?></td>
        <td>₹<?= number_format($row['amount'],2) ?></td>
        <td><?= htmlspecialchars($row['payment_date']) ?></td>
        <td class="status <?= htmlspecialchars($row['status']) ?>"><?= ucfirst($row['status']) ?></td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>
</body>
</html>

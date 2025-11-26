<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

// Security check
if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'staff') {
  header("Location: index.php");
  exit;
}

$staff = $_SESSION['admin'];
$restaurant_id = intval($staff['restaurant_id']);

// Fetch restaurant details
$stmt = $conn->prepare("SELECT name FROM restaurants WHERE id=?");
$stmt->bind_param("i", $restaurant_id);
$stmt->execute();
$res = $stmt->get_result();
$restaurant_name = $res->num_rows ? $res->fetch_assoc()['name'] : "Unknown Restaurant";
$stmt->close();

// Fetch stats
$bookings = $conn->query("SELECT COUNT(*) AS total FROM bookings WHERE restaurant_id=$restaurant_id")->fetch_assoc()['total'];
$payments = $conn->query("
  SELECT COUNT(*) AS total, SUM(amount) AS total_amount
  FROM payments 
  WHERE booking_id IN (SELECT id FROM bookings WHERE restaurant_id=$restaurant_id)
")->fetch_assoc();
$total_payments = $payments['total'] ?? 0;
$total_amount = $payments['total_amount'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Staff Dashboard | <?= htmlspecialchars($restaurant_name) ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body { font-family:"Poppins",sans-serif; background:#f4f6f8; margin:0; color:#333; }
.container { max-width:1000px; margin:40px auto; background:#fff; padding:30px; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.1); }
h1 { margin-bottom:10px; }
h2 { margin:25px 0 10px; }
nav a { text-decoration:none; background:#111; color:white; padding:8px 14px; border-radius:6px; margin-right:8px; font-weight:500; }
nav a:hover { background:#333; }
.grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(240px,1fr)); gap:20px; margin-top:30px; }
.card { background:#fafafa; padding:20px; border-radius:8px; text-align:center; box-shadow:0 3px 8px rgba(0,0,0,0.05); transition:0.2s; }
.card:hover { transform:translateY(-5px); }
.logout { background:#c0392b !important; }
</style>
</head>
<body>

<div class="container">
  <nav>
    <div>
      <h1>👩‍🍳 Welcome, <?= htmlspecialchars($staff['name']) ?> (Staff)</h1>
      <p>Restaurant: <strong><?= htmlspecialchars($restaurant_name) ?></strong></p>
    </div>
    <div>
      <a href="view_bookings_staff.php">View Bookings</a>
      <a href="view_payments_staff.php">View Payments</a>
      <a href="../logout.php" class="logout">Logout</a>
    </div>
  </nav>

  <div class="grid">
    <div class="card">
      <h3>Bookings</h3>
      <p><?= $bookings ?> total</p>
    </div>

    <div class="card">
      <h3>Payments</h3>
      <p><?= $total_payments ?> payments</p>
      <p>₹<?= number_format($total_amount, 2) ?></p>
    </div>
  </div>
</div>

</body>
</html>

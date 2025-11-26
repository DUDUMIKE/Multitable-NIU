<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

// Security check
if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'manager') {
  header("Location: index.php");
  exit;
}

$manager = $_SESSION['admin'];
$restaurant_id = intval($manager['restaurant_id']);

// Fetch restaurant name
$stmt = $conn->prepare("SELECT name FROM restaurants WHERE id = ?");
$stmt->bind_param("i", $restaurant_id);
$stmt->execute();
$res = $stmt->get_result();
$restaurant_name = $res->num_rows ? $res->fetch_assoc()['name'] : "Unknown Restaurant";
$stmt->close();

// Quick Stats
$bookings = $conn->query("SELECT COUNT(*) AS total FROM bookings WHERE restaurant_id = $restaurant_id")->fetch_assoc()['total'];
$staff = $conn->query("SELECT COUNT(*) AS total FROM admins WHERE restaurant_id = $restaurant_id AND role = 'staff'")->fetch_assoc()['total'];
$extras = $conn->query("SELECT COUNT(*) AS total FROM extra_services WHERE restaurant_id = $restaurant_id")->fetch_assoc()['total'];
$payments = $conn->query("
  SELECT COUNT(*) AS total, SUM(amount) AS total_amount
  FROM payments 
  WHERE booking_id IN (SELECT id FROM bookings WHERE restaurant_id = $restaurant_id)
")->fetch_assoc();
$total_payments = $payments['total'] ?? 0;
$total_amount = $payments['total_amount'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Manager Dashboard | <?= htmlspecialchars($restaurant_name) ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body {
  font-family: "Poppins", sans-serif;
  background: #f4f6f8;
  margin: 0;
  color: #333;
}
.container {
  max-width: 1200px;
  margin: 40px auto;
  background: white;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  padding: 30px;
}
h1 { margin-bottom: 10px; }
h2 { margin: 30px 0 10px; color: #111; }
nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}
nav a {
  text-decoration: none;
  background: #111;
  color: white;
  padding: 8px 14px;
  border-radius: 6px;
  font-weight: 500;
  transition: 0.2s;
}
nav a:hover { background: #333; }
.grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
}
.card {
  background: #fafafa;
  border-radius: 10px;
  padding: 20px;
  text-align: center;
  box-shadow: 0 3px 10px rgba(0,0,0,0.05);
  transition: transform 0.2s ease;
}
.card:hover { transform: translateY(-5px); }
.card h3 { margin-bottom: 5px; color: #111; }
.card p { margin: 5px 0; color: #666; }
.manage-links {
  margin-top: 25px;
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}
.manage-links a {
  flex: 1;
  text-align: center;
  padding: 12px;
  background: #111;
  color: white;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 500;
  transition: 0.2s;
}
.manage-links a:hover { background: #333; }
.logout {
  background: #c0392b !important;
}
</style>
</head>
<body>
<div class="container">
  <nav>
    <div>
      <h1>👨‍🍳 Welcome, <?= htmlspecialchars($manager['name']) ?></h1>
      <p>Managing: <strong><?= htmlspecialchars($restaurant_name) ?></strong></p>
    </div>
    <a href="../logout.php" class="logout">Logout</a>
  </nav>

  <div class="grid">
    <div class="card">
      <h3>Bookings</h3>
      <p><?= $bookings ?> total</p>
      <a href="view_bookings_manager.php">View Bookings</a>
    </div>

    <div class="card">
      <h3>Staff</h3>
      <p><?= $staff ?> members</p>
      <a href="manage_staff.php">Manage Staff</a>
    </div>

    <div class="card">
      <h3>Extra Services</h3>
      <p><?= $extras ?> active</p>
      <a href="manage_extras.php">Manage Extras</a>
    </div>

    <div class="card">
      <h3>Payments</h3>
      <p><?= $total_payments ?> payments</p>
      <p>₹<?= number_format($total_amount, 2) ?></p>
      <a href="view_payments_manager.php">View Payments</a>
    </div>
  </div>
</div>
</body>
</html>

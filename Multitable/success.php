<?php
// success.php
session_start();
require_once __DIR__ . '/includes/db_connect.php';
$booking_id = intval($_GET['booking_id'] ?? 0);
if (!$booking_id) { echo "Invalid."; exit; }

$stmt = $conn->prepare("SELECT b.*, t.table_name, r.name AS restaurant_name FROM bookings b JOIN tables t ON t.id=b.table_id JOIN restaurants r ON r.id=t.restaurant_id WHERE b.id=?");
$stmt->bind_param('i',$booking_id);
$stmt->execute(); $res = $stmt->get_result();
if ($res->num_rows === 0) { echo "Not found."; exit; }
$b = $res->fetch_assoc();
$stmt->close();
?>
<!doctype html><html>
<head><meta charset="utf-8"><title>Booking Confirmed</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body>
<div class="success-box">
  <h2>Booking Confirmed</h2>
  <p>Thanks <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Guest') ?> — your booking is confirmed.</p>
  <p><strong>Restaurant:</strong> <?=htmlspecialchars($b['restaurant_name'])?><br>
     <strong>Table:</strong> <?=htmlspecialchars($b['table_name'])?><br>
     <strong>When:</strong> <?=htmlspecialchars($b['booking_date'].' '.$b['booking_time'])?><br>
     <strong>Guests:</strong> <?=intval($b['guests'])?></p>
  <p><a href="index.php">Back to home</a></p>
</div>
</body></html>

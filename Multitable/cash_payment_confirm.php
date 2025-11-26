<?php
require_once __DIR__ . '/includes/db_connect.php';
session_start();

if (!isset($_GET['booking_id'])) die("Invalid access");

$booking_id = intval($_GET['booking_id']);

$q = $conn->prepare("
  SELECT b.*, r.name AS restaurant_name, t.table_name
  FROM bookings b
  JOIN restaurants r ON b.restaurant_id = r.id
  JOIN tables t ON b.table_id = t.id
  WHERE b.id = ?
");
$q->bind_param("i", $booking_id);
$q->execute();
$booking = $q->get_result()->fetch_assoc();
$q->close();

if (!$booking) die("Booking not found.");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Cash Payment Confirmation</title>
<style>
  body { background:#f8f9fa; font-family:'Poppins',sans-serif; }
  .box {
    max-width: 700px; margin: 80px auto; background:#fff;
    padding:30px; border-radius:12px;
    box-shadow:0 4px 15px rgba(0,0,0,0.1);
    text-align:center;
  }
  .success {
    color:#28a745; font-size:1.8rem; font-weight:700;
  }
  .btn {
    margin-top:25px; padding:12px 25px; background:#007bff;
    color:white; text-decoration:none; border-radius:8px;
    font-weight:600;
  }
  .btn:hover { background:#0056b3; }
</style>
</head>
<body>

<div class="box">
  <div class="success">✔ Booking Confirmed!</div>
 <br> </br>
   <a class="btn" href="generate_receipt.php?booking_id=<?= $booking_id ?>" target="_blank">
  Download Receipt (PDF)
  </a>

  <p>Your booking has been confirmed.</p>
  <p><strong>Restaurant:</strong> <?= htmlspecialchars($booking['restaurant_name']) ?></p>
  <p><strong>Table:</strong> <?= htmlspecialchars($booking['table_name']) ?></p>
  <p><strong>Date:</strong> <?= htmlspecialchars($booking['booking_date']) ?></p>
  <p><strong>Time:</strong> <?= htmlspecialchars($booking['booking_time']) ?></p>

  <h3 style="margin-top:20px;color:#333;">Payment Method: <span style="color:#28a745;">Cash</span></h3>
  <p>Please pay at the restaurant counter on arrival.</p>

  <a class="btn" href="index.php">Return to Home</a>

</div>

</body>
</html>

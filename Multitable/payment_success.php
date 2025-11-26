<?php
require_once __DIR__ . '/includes/db_connect.php';
session_start();

if (!isset($_GET['booking_id'])) die("Invalid payment reference");

$id = intval($_GET['booking_id']);
$q = $conn->prepare("
  SELECT b.*, r.name AS restaurant_name
  FROM bookings b
  JOIN restaurants r ON b.restaurant_id = r.id
  WHERE b.id = ?
");
$q->bind_param("i", $id);
$q->execute();
$booking = $q->get_result()->fetch_assoc();
$q->close();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Payment Successful</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    body { background: #f8f9fa; text-align: center; font-family: 'Poppins', sans-serif; }
    .success-box { margin: 100px auto; padding: 30px; max-width: 600px; background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .success-box h2 { color: #28a745; }
    .success-box p { margin-top: 10px; }
    .back-btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #007bff; color: white; border-radius: 6px; text-decoration: none; }
    .back-btn:hover { background: #0056b3; }
  </style>
</head>
<body>
  <div class="success-box">
    <h2>✅ Payment Successful!</h2>
    <p>Your booking at <strong><?= htmlspecialchars($booking['restaurant_name']) ?></strong> has been confirmed.</p>
    <p>Booking ID: <strong>#<?= htmlspecialchars($booking['id']) ?></strong></p>
    <a href="index.php" class="back-btn">Back to Home</a>
  </div>
</body>
</html>

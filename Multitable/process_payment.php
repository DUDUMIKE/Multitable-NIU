<?php
require_once __DIR__ . '/includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: index.php');
  exit;
}

$booking_id = intval($_POST['booking_id']);
$upi = trim($_POST['upi']);
$amount = floatval($_POST['amount']);

if ($upi === '') {
  die("Invalid UPI ID");
}

// Update booking status to confirmed
$conn->query("UPDATE bookings SET status='confirmed' WHERE id=$booking_id");

echo "<h2>✅ Payment Successful!</h2>
      <p>Your booking has been confirmed and extra services have been reserved.</p>
      <a href='index.php'>Return to Home</a>";

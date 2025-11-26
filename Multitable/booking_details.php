<?php
// booking_details.php
require_once __DIR__ . '/includes/db_connect.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$booking_id = intval($_GET['id']);

$query = $conn->prepare("
    SELECT b.*, r.name AS restaurant_name, t.table_name, t.table_type, t.capacity, t.premium_fee
    FROM bookings b
    JOIN restaurants r ON b.restaurant_id = r.id
    JOIN tables t ON b.table_id = t.id
    WHERE b.id = ?
");
$query->bind_param('i', $booking_id);
$query->execute();
$result = $query->get_result();
if ($result->num_rows === 0) {
    echo "<h3>Booking not found.</h3>";
    exit;
}
$booking = $result->fetch_assoc();
$query->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booking Details | TableBooker</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <header class="site-header">
    <div class="logo">
      <img src="https://img.icons8.com/ios-filled/50/000000/restaurant.png" alt="logo" />
      <div class="logo-text"><h1>TableBooker</h1></div>
    </div>
  </header>

  <main class="booking-details">
    <h2>Booking Confirmation</h2>

    <div class="booking-summary">
      <p><strong>Restaurant:</strong> <?= htmlspecialchars($booking['restaurant_name']) ?></p>
      <p><strong>Table:</strong> <?= htmlspecialchars($booking['table_name']) ?> (<?= htmlspecialchars($booking['table_type']) ?>)</p>
      <p><strong>Date:</strong> <?= htmlspecialchars($booking['booking_date']) ?></p>
      <p><strong>Time:</strong> <?= htmlspecialchars($booking['booking_time']) ?></p>
      <p><strong>Guests:</strong> <?= htmlspecialchars($booking['guests']) ?></p>
      <p><strong>Base Price:</strong> ₹<?= number_format($booking['premium_fee'],2) ?></p>
    </div>

    <h3>🎁 Add Extra Services</h3>
    <form action="payment.php" method="POST" class="extras-form">
      <input type="hidden" name="booking_id" value="<?= $booking_id ?>">
      <div class="form-group">
        <label><input type="checkbox" name="extras[]" value="Candlelight Setup"> Candlelight Setup (₹500)</label><br>
        <label><input type="checkbox" name="extras[]" value="Birthday Decor"> Birthday Decor (₹700)</label><br>
        <label><input type="checkbox" name="extras[]" value="Champagne Bottle"> Champagne Bottle (₹1200)</label><br>
        <label><input type="checkbox" name="extras[]" value="Live Music"> Live Music (₹800)</label><br>
        <label><input type="checkbox" name="extras[]" value="Special Menu"> Special Menu (₹1000)</label>
      </div>

      <button type="submit" class="primary-btn">Proceed to Payment</button>
    </form>
  </main>
</body>
</html>

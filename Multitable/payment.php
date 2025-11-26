<?php
session_start();
require_once __DIR__ . '/includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['booking_id'])) {
  header('Location: index.php');
  exit;
}

$booking_id = intval($_POST['booking_id']);
$services = $_POST['services'] ?? [];

// Save selected services
if (!empty($services)) {
  $stmt = $conn->prepare("INSERT IGNORE INTO booking_services (booking_id, service_id) VALUES (?, ?)");
  foreach ($services as $s_id) {
    $s_id = intval($s_id);
    $stmt->bind_param('ii', $booking_id, $s_id);
    $stmt->execute();
  }
  $stmt->close();
}

// Calculate costs
$q = $conn->prepare("
  SELECT es.name, es.price, sc.category_name
  FROM booking_services bs
  JOIN extra_services es ON bs.service_id = es.id
  JOIN service_categories sc ON es.category_id = sc.id
  WHERE bs.booking_id=?
");
$q->bind_param('i', $booking_id);
$q->execute();
$services_data = $q->get_result()->fetch_all(MYSQLI_ASSOC);
$q->close();

$extra_cost = array_sum(array_column($services_data, 'price'));

// Get base table fee
$info = $conn->query("
  SELECT t.premium_fee, r.name AS restaurant
  FROM bookings b
  JOIN tables t ON b.table_id=t.id
  JOIN restaurants r ON b.restaurant_id=r.id
  WHERE b.id=$booking_id
")->fetch_assoc();

$total = $info['premium_fee'] + $extra_cost;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Payment - TableBooker</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="payment-container">
  <h2>Payment Details</h2>
  <p><strong>Restaurant:</strong> <?= htmlspecialchars($info['restaurant']) ?></p>
  <p><strong>Base Table Price:</strong> ₹<?= number_format($info['premium_fee'], 2) ?></p>

  <?php if ($services_data): ?>
    <h3>Selected Extra Services</h3>
    <ul>
      <?php foreach ($services_data as $srv): ?>
        <li><?= htmlspecialchars($srv['category_name']) ?> — <?= htmlspecialchars($srv['name']) ?> (₹<?= number_format($srv['price'], 2) ?>)</li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p>No extra services selected.</p>
  <?php endif; ?>

  <h3>Total Payable: ₹<?= number_format($total, 2) ?></h3>

  <form method="POST" action="process_payment.php">
    <input type="hidden" name="booking_id" value="<?= $booking_id ?>">
    <input type="hidden" name="amount" value="<?= $total ?>">

    <label>UPI ID:</label>
    <input type="text" name="upi" placeholder="example@upi" required>

    <button type="submit" class="primary-btn">Pay Now</button>
  </form>
</div>
</body>
</html>

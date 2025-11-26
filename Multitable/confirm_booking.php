<?php
require_once __DIR__ . '/includes/db_connect.php';
session_start();

if (!isset($_POST['booking_id']) && !isset($_GET['booking_id'])) {
  die("Invalid booking access");
}

$booking_id = intval($_POST['booking_id'] ?? $_GET['booking_id']);

// Fetch booking info
$q = $conn->prepare("
  SELECT b.*, r.name AS restaurant_name, t.table_name, t.premium_fee
  FROM bookings b
  JOIN restaurants r ON b.restaurant_id = r.id
  JOIN tables t ON b.table_id = t.id
  WHERE b.id = ?
");
$q->bind_param("i", $booking_id);
$q->execute();
$booking = $q->get_result()->fetch_assoc();
$q->close();

if (!$booking) die("Booking not found");

// Fetch extras
$extras = $conn->prepare("
  SELECT es.name, es.price
  FROM booking_services bs
  JOIN extra_services es ON bs.extra_service_id = es.id
  WHERE bs.booking_id = ?
");
$extras->bind_param("i", $booking_id);
$extras->execute();
$extra_res = $extras->get_result();

$total_extras = 0;
$extra_list = [];
while ($row = $extra_res->fetch_assoc()) {
  $total_extras += $row['price'];
  $extra_list[] = $row;
}
$extras->close();

$grand_total = $booking['premium_fee'] + $total_extras;

// ---------------------------------------------------------
// PROCESS PAYMENT
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_method'])) {
  $method = $_POST['payment_method'];

  // --------------------------------
  // CASH PAYMENT (Instant Confirm)
  // --------------------------------

// CASH PAYMENT → redirect to cash confirmation page

if ($method === "cash") {

    // Insert cash payment (pending until paid physically)
    $ins = $conn->prepare("
      INSERT INTO payments (booking_id, amount, payment_method, status)
      VALUES (?, ?, 'cash', 'success')
    ");
    $ins->bind_param("id", $booking_id, $grand_total);
    $ins->execute();
    $ins->close();

    // Confirm booking
    $up = $conn->prepare("UPDATE bookings SET status='confirmed' WHERE id=?");
    $up->bind_param("i", $booking_id);
    $up->execute();
    $up->close();

    header("Location: cash_payment_confirm.php?booking_id=$booking_id");
    exit;
}


  // --------------------------------
  // UPI PAYMENT (Redirect first)
  // --------------------------------
if ($method === "upi") {
    $upi_id = trim($_POST['upi_id'] ?? "");

    if (empty($upi_id)) {
        $error = "Please enter a valid UPI ID.";
    } else {

      $ins = $conn->prepare("
        INSERT INTO payments (booking_id, amount, payment_method, status, upi_id)
        VALUES (?, ?, 'upi', 'pending', ?)
      ");
      $ins->bind_param("ids", $booking_id, $grand_total, $upi_id);
      $ins->execute();
      $ins->close();

      header("Location: upi_redirect.php?booking_id=$booking_id&amount=$grand_total&upi=$upi_id");
      exit;
    }
}

}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Confirm & Pay</title>
  <style>
    body { background:#f0f2f5; font-family:'Poppins',sans-serif; }
    .pay-container {
      max-width: 750px; margin: 40px auto; background: #fff; padding: 30px;
      border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    .summary { line-height: 1.7; }
    .payment-method-box { margin-top: 25px; display: flex; flex-direction: column; gap: 25px; }
    .payment-option {
      background: #fff; border: 2px solid #e0e0e0; border-radius: 12px;
      padding: 20px; display: flex; justify-content: space-between;
      align-items: center; cursor: pointer; transition: .3s;
    }
    .payment-option:hover { background:#fafafa; border-color:#444; }
    .payment-btn {
      padding: 10px 22px; background:#111; color:white;
      border:none; border-radius:10px; cursor:pointer;
      font-weight:600; transition:.25s;
    }
    .upi-input-box { margin-top: 20px; }
    .upi-input-box input {
      width: 100%; padding: 12px; border-radius: 8px;
      border: 1.5px solid #ccc; font-size: .95rem;
    }
    .cash-tag {
      padding: 4px 7px; background:#28a745; color:white;
      border-radius:5px; font-size:.75rem; font-weight:600;
    }
    .error { color:red;margin:15px 0;text-align:center; }
  </style>
</head>
<body>
<div class="pay-container">
  <h2 style="text-align:center;">Confirm & Proceed to Payment</h2>

  <div class="summary">
    <p><strong>Restaurant:</strong> <?= htmlspecialchars($booking['restaurant_name']) ?></p>
    <p><strong>Table:</strong> <?= htmlspecialchars($booking['table_name']) ?></p>
    <p><strong>Date:</strong> <?= htmlspecialchars($booking['booking_date']) ?></p>
    <p><strong>Time:</strong> <?= htmlspecialchars($booking['booking_time']) ?></p>
    <p><strong>Status:</strong> <span style="color:orange;font-weight:bold;">Pending</span></p>
  </div>

  <h3>Extra Services</h3>
  <?php if ($extra_list): ?>
    <ul>
      <?php foreach ($extra_list as $ex): ?>
        <li><?= htmlspecialchars($ex['name']) ?> — ₹<?= number_format($ex['price'],2) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p>No extra services selected.</p>
  <?php endif; ?>

  <p style="font-size:1.2rem;font-weight:bold;text-align:right;">Grand Total: ₹<?= number_format($grand_total,2) ?></p>

  <?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <h3>Select Payment Method</h3>

  <div class="payment-method-box">

    <!-- UPI OPTION -->
    <div class="payment-option" onclick="toggleUpi()">
      <div style="display:flex;align-items:center;gap:15px;">
        <img src="https://img.icons8.com/color/96/google-pay.png" width="50">
        <div>
          <strong>UPI Payment</strong><br>
          <small>Google Pay / PhonePe / Paytm</small>
        </div>
      </div>
      <button type="button" class="payment-btn">Pay ₹<?= number_format($grand_total,2) ?></button>
    </div>

    <!-- HIDDEN UPI FORM -->
    <form method="POST" id="upi-form" style="display:none;">
      <input type="hidden" name="booking_id" value="<?= $booking_id ?>">
      <input type="hidden" name="payment_method" value="upi">
      <div class="upi-input-box">
        <label><strong>Your UPI ID</strong></label>
        <input type="text" name="upi_id" placeholder="yourname@upi" required>
      </div>
      <br>
      <button class="payment-btn" style="background:#0a7cff;">Proceed with UPI</button>
    </form>

    <!-- CASH OPTION -->
    <form method="POST" class="payment-option" style="cursor:auto;">
      <div style="display:flex;align-items:center;gap:15px;">
        <img src="https://img.icons8.com/emoji/96/money-bag-emoji.png" width="50">
        <div>
          <strong>Cash Payment <span class="cash-tag">Offline</span></strong><br>
          <small>Pay directly at restaurant</small>
        </div>
      </div>
      <input type="hidden" name="booking_id" value="<?= $booking_id ?>">
      <input type="hidden" name="payment_method" value="cash">
      <button class="payment-btn" style="background:#28a745;">Pay ₹<?= number_format($grand_total,2) ?></button>
    </form>

  </div>
</div>

<script>
function toggleUpi() {
  let form = document.getElementById("upi-form");
  form.style.display = (form.style.display === "none") ? "block" : "none";
}
</script>
</body>
</html>

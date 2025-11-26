<?php
require_once __DIR__ . '/includes/db_connect.php';
session_start();

if (!isset($_GET['booking_id'])) {
  die("Invalid booking ID");
}
$booking_id = intval($_GET['booking_id']);

// Get booking info
$q = $conn->prepare("
  SELECT b.*, r.name AS restaurant_name 
  FROM bookings b
  JOIN restaurants r ON b.restaurant_id = r.id
  WHERE b.id = ?
");
$q->bind_param("i", $booking_id);
$q->execute();
$booking = $q->get_result()->fetch_assoc();
if (!$booking) die("Booking not found");
$q->close();

// Get available extras for that restaurant
$extras = $conn->prepare("SELECT * FROM extra_services WHERE restaurant_id = ?");
$extras->bind_param("i", $booking['restaurant_id']);
$extras->execute();
$extra_res = $extras->get_result();

// Get already added extras for this booking
$added = $conn->prepare("
  SELECT es.id, es.name, es.price 
  FROM booking_services bs
  JOIN extra_services es ON bs.extra_service_id = es.id
  WHERE bs.booking_id = ?
");
$added->bind_param("i", $booking_id);
$added->execute();
$added_res = $added->get_result();

// Calculate total price
$total_price = 0;
$added_services = [];
while ($row = $added_res->fetch_assoc()) {
  $added_services[] = $row;
  $total_price += $row['price'];
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Booking Summary</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .summary-container { max-width: 900px; margin: 40px auto; background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08);}
    h2 { text-align: center; margin-bottom: 20px; }
    .info-box { margin-bottom: 25px; }
    .extras-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px,1fr)); gap: 15px; }
    .extra-card { border: 1px solid #ddd; border-radius: 10px; padding: 15px; background: #fafafa; transition: 0.2s; }
    .extra-card:hover { background: #f0f8ff; }
    .extra-card h4 { margin: 0 0 8px; }
    .selected-services { margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px; }
    .total { font-weight: bold; color: #111; text-align: right; margin-top: 10px; font-size: 1.1rem; }
    .add-btn { display: inline-block; margin-top: 10px; padding: 8px 12px; background: #111; color: white; border: none; border-radius: 6px; cursor: pointer; transition: 0.2s; }
    .add-btn:hover { background: #333; }
  </style>
</head>
<body>

<div class="summary-container">
  <h2>Booking Summary</h2>

  <div class="info-box">
    <p><strong>Restaurant:</strong> <?= htmlspecialchars($booking['restaurant_name']) ?></p>
    <p><strong>Date:</strong> <?= htmlspecialchars($booking['booking_date']) ?></p>
    <p><strong>Time:</strong> <?= htmlspecialchars($booking['booking_time']) ?></p>
    <p><strong>Guests:</strong> <?= htmlspecialchars($booking['guests']) ?></p>
    <p><strong>Status:</strong> <span style="color:<?= $booking['status']=='pending'?'orange':($booking['status']=='confirmed'?'green':'red') ?>;font-weight:bold;"><?= ucfirst($booking['status']) ?></span></p>
  </div>

  <h3>Available Extra Services</h3>
  <form action="add_extras.php" method="POST">
    <input type="hidden" name="booking_id" value="<?= $booking_id ?>">
    <div class="extras-list">
      <?php if ($extra_res->num_rows > 0): ?>
        <?php while ($row = $extra_res->fetch_assoc()): ?>
          <label class="extra-card">
            <input type="checkbox" name="extras[]" value="<?= $row['id'] ?>">
            <h4><?= htmlspecialchars($row['name']) ?></h4>
            <p><?= htmlspecialchars($row['description']) ?></p>
            <p><strong>₹<?= number_format($row['price'],2) ?></strong></p>
          </label>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No extra services available for this restaurant.</p>
      <?php endif; ?>
    </div>
    <button type="submit" class="add-btn">Add Selected Services</button>
  </form>


<?php if (isset($_GET['msg'])): ?>
  <div class="alert <?= $_GET['msg'] === 'added' ? 'success' : 'error' ?>">
    <?php
    if ($_GET['msg'] === 'no_selection') echo "Please select at least one extra service.";
    elseif ($_GET['msg'] === 'added') echo "Extra services added successfully!";
    elseif ($_GET['msg'] === 'removed') echo "Service removed.";
    ?>
  </div>
<?php endif; ?>



<?php if ($added_services): ?>
  <div class="selected-services">
    <h3>Selected Extra Services</h3>
    <ul class="extras-list" id="extrasList">
      <?php
      $added_res->data_seek(0);
      while ($s = $added_res->fetch_assoc()):
      ?>
        <li class="extra-item" data-service-id="<?= $s['id'] ?>">
          <span class="extra-name"><?= htmlspecialchars($s['name']) ?></span>
          <span class="extra-price">₹<?= number_format($s['price'], 2) ?></span>
          <button class="remove-btn" onclick="removeExtraService(<?= $booking_id ?>, <?= $s['id'] ?>)">Remove</button>
        </li>
      <?php endwhile; ?>
    </ul>
    <p class="total" id="totalDisplay">Total (Extras): ₹<?= number_format($total_price, 2) ?></p>
  </div>
<?php endif; ?>


<!-- Confirm Booking or Skip Extras -->
<div class="confirm-container" style="margin-top: 30px; text-align: center;">
  <form action="confirm_booking.php" method="POST">
    <input type="hidden" name="booking_id" value="<?= $booking_id ?>">
    <button type="submit" class="confirm-btn"
      style="padding: 10px 20px; background-color: #007bff; border: none; color: white; border-radius: 6px; cursor: pointer;">
      Confirm & Proceed to Payment
    </button>
  </form>

  <p style="margin-top: 12px; font-size: 0.9rem; color: #666;">
    You can proceed without selecting any extra services.
  </p>
</div>

<script>
function removeExtraService(bookingId, serviceId) {
  if (!confirm("Remove this service?")) return;

  fetch('remove_extra_ajax.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'booking_id=' + bookingId + '&extra_service_id=' + serviceId
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      const item = document.querySelector(`[data-service-id="${serviceId}"]`);
      if (item) item.remove();
      document.getElementById('totalDisplay').textContent = "Total (Extras): ₹" + data.new_total;
    } else {
      alert("Failed to remove service: " + data.message);
    }
  })
  .catch(err => console.error(err));
}
</script>


<script>
function removeExtraService(bookingId, serviceId) {
  if (!confirm("Remove this service?")) return;

  fetch('remove_extra_ajax.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'booking_id=' + bookingId + '&extra_service_id=' + serviceId
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      const item = document.querySelector(`[data-service-id="${serviceId}"]`);
      if (item) item.remove();
      document.getElementById('totalDisplay').textContent = "Total (Extras): ₹" + data.new_total;
    } else {
      alert("Failed to remove service: " + data.message);
    }
  })
  .catch(err => console.error(err));
}
</script>


</body>
</html>

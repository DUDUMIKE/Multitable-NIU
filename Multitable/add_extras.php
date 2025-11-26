<?php
// add_extras.php
require_once __DIR__ . '/includes/db_connect.php';
session_start();

if (!isset($_POST['booking_id'])) {
  header("Location: index.php");
  exit;
}

$booking_id = intval($_POST['booking_id']);
$extras = $_POST['extras'] ?? [];

if (empty($extras)) {
  header("Location: booking_summary.php?booking_id=$booking_id&msg=no_selection");
  exit;
}

$stmt = $conn->prepare("INSERT IGNORE INTO booking_services (booking_id, extra_service_id) VALUES (?, ?)");
foreach ($extras as $extra_id) {
  $id = intval($extra_id);
  $stmt->bind_param("ii", $booking_id, $id);
  $stmt->execute();
}
$stmt->close();

header("Location: booking_summary.php?booking_id=$booking_id&msg=added");
exit;
?>

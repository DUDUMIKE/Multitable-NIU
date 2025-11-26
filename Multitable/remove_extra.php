<?php
// remove_extra.php
require_once __DIR__ . '/includes/db_connect.php';
session_start();

if (!isset($_POST['booking_id'], $_POST['extra_service_id'])) {
  header("Location: index.php");
  exit;
}

$booking_id = intval($_POST['booking_id']);
$extra_service_id = intval($_POST['extra_service_id']);

// Secure deletion
$del = $conn->prepare("DELETE FROM booking_services WHERE booking_id = ? AND extra_service_id = ?");
$del->bind_param("ii", $booking_id, $extra_service_id);
$del->execute();
$del->close();

// Redirect back to booking summary with a flag
header("Location: booking_summary.php?booking_id=" . $booking_id . "&removed=1");
exit;

<?php
require_once __DIR__ . '/includes/db_connect.php';
header('Content-Type: application/json');

$booking_id = intval($_POST['booking_id'] ?? 0);
$extra_id = intval($_POST['extra_service_id'] ?? 0);

if (!$booking_id || !$extra_id) {
  echo json_encode(['success' => false, 'message' => 'Missing parameters']);
  exit;
}

$del = $conn->prepare("DELETE FROM booking_services WHERE booking_id = ? AND extra_service_id = ?");
$del->bind_param("ii", $booking_id, $extra_id);
$del->execute();
$del->close();

// Get updated total
$totalQ = $conn->prepare("
  SELECT SUM(es.price) AS total
  FROM booking_services bs
  JOIN extra_services es ON bs.extra_service_id = es.id
  WHERE bs.booking_id = ?
");
$totalQ->bind_param("i", $booking_id);
$totalQ->execute();
$totalRes = $totalQ->get_result();
$total = $totalRes->fetch_assoc()['total'] ?? 0;

echo json_encode(['success' => true, 'new_total' => number_format($total, 2)]);

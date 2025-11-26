<?php
require_once __DIR__ . '/includes/db_connect.php';

header('Content-Type: application/json');

if (!isset($_GET['table_id'])) {
  echo json_encode([]);
  exit;
}

$table_id = intval($_GET['table_id']);

// Get restaurant_id of this table
$stmt = $conn->prepare("SELECT restaurant_id FROM tables WHERE id=?");
$stmt->bind_param("i", $table_id);
$stmt->execute();
$res = $stmt->get_result();
if (!$res->num_rows) {
  echo json_encode([]);
  exit;
}
$restaurant_id = $res->fetch_assoc()['restaurant_id'];
$stmt->close();

// Get all tables for this restaurant (different types)
$q = $conn->prepare("SELECT id, table_name, table_type FROM tables WHERE restaurant_id=? ORDER BY table_type");
$q->bind_param("i", $restaurant_id);
$q->execute();
$result = $q->get_result();

$tables = [];
while ($row = $result->fetch_assoc()) {
  $tables[] = $row;
}

echo json_encode($tables);
?>

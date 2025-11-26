<?php
// filter.php
require_once __DIR__ . '/includes/db_connect.php';

$search = $_GET['search'] ?? '';
$cuisine = $_GET['cuisine'] ?? '';
$location = $_GET['location'] ?? '';
$table_type = $_GET['table_type'] ?? ''; // ✅ NEW

$sql = "SELECT r.*, t.id AS table_id, t.table_name, t.table_type, t.capacity, t.premium_fee
        FROM restaurants r
        JOIN tables t ON t.restaurant_id = r.id
        WHERE 1=1";
$params = [];
$types = '';

// dynamic clauses
if ($search !== '') {
    $sql .= " AND (r.name LIKE CONCAT('%',?,'%') OR r.cuisine LIKE CONCAT('%',?,'%') OR t.table_name LIKE CONCAT('%',?,'%'))";
    $params[] = $search; 
    $params[] = $search; 
    $params[] = $search;
    $types .= 'sss';
}
if ($cuisine !== '') {
    $sql .= " AND r.cuisine = ?";
    $params[] = $cuisine;
    $types .= 's';
}
if ($location !== '') {
    $sql .= " AND r.location = ?";
    $params[] = $location;
    $types .= 's';
}
if ($table_type !== '') { // ✅ NEW condition
    $sql .= " AND LOWER(TRIM(t.table_type)) LIKE LOWER(TRIM(CONCAT('%',?,'%')))";
    $params[] = $table_type;
    $types .= 's';
}

$sql .= " ORDER BY r.name, t.table_name";

// New Line  
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    // helpful debug if prepare fails
    http_response_code(500);
    echo "DB prepare error: " . htmlspecialchars($conn->error);
    exit;
}


$stmt = $conn->prepare($sql);
if ($types) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo '<p class="no-results">No tables match your filters.</p>';
    exit;
}

while ($row = $res->fetch_assoc()) {
    echo '<div class="restaurant-card">';
    echo '<img src="'.htmlspecialchars($row['image']).'" alt="'.htmlspecialchars($row['name']).'">';
    echo '<div class="info">';
    echo '<h3>'.htmlspecialchars($row['name']).' <span class="rating">★ '.htmlspecialchars($row['rating']).'</span></h3>';
    echo '<p class="details">'.htmlspecialchars($row['cuisine']).' · '.htmlspecialchars($row['location']).' · '.htmlspecialchars($row['price_range']).'</p>';
    echo '<p class="desc">'.htmlspecialchars($row['description']).'</p>';
    echo '<div class="table-line">';
    echo '<div class="table-meta"><strong>'.htmlspecialchars($row['table_name']).'</strong> <small>'.htmlspecialchars($row['table_type']).' · '.intval($row['capacity']).' seats</small></div>';
    echo '<div class="table-actions">';
    echo '<a class="view" href="javascript:void(0);" onclick="openBookingModal('.$row['table_id'].',\''.addslashes(htmlspecialchars($row['name'])).'\',\''.addslashes(htmlspecialchars($row['table_name'])).'\')">Book</a>';
    echo '<a class="price">₹'.number_format($row['premium_fee'],2).'</a>';
    echo '</div></div></div></div>';
}
$stmt->close();

<?php
require_once __DIR__ . '/../includes/db_connect.php';

$accounts = [
  ['email' => 'admin@tablebooker.com', 'password' => 'admin123'],
  ['email' => 'manager@sunsetbistro.com', 'password' => 'manager123'],
  ['email' => 'staff@sunsetbistro.com', 'password' => 'staff123']
];

foreach ($accounts as $acc) {
  $hash = password_hash($acc['password'], PASSWORD_BCRYPT);
  $stmt = $conn->prepare("UPDATE admins SET password=? WHERE email=?");
  $stmt->bind_param("ss", $hash, $acc['email']);
  $stmt->execute();
  echo "Updated password for {$acc['email']}<br>";
}

echo "<p style='color:green;'>✅ Passwords successfully rehashed!</p>";
?>

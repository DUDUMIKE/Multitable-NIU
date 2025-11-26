<?php
require_once __DIR__ . '/../includes/db_connect.php';

$name = 'Super Admin';
$email = 'superadmin@multitable.com';
$pass = password_hash('Admin@123', PASSWORD_DEFAULT);
$role = 'admin';

// Clear old admins
$conn->query("DELETE FROM users WHERE role='admin'");

// Insert new one
$stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $pass, $role);

if ($stmt->execute()) {
    echo "✅ Admin created successfully.<br>Email: $email<br>Password: Admin@123";
} else {
    echo "❌ Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>

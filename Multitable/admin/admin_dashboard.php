<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: admin_login.php");
  exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard | MultiTable</title>
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background: #f4f6f9;
      padding: 20px;
    }
    h1 { color: #111; }
    .nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #111;
      color: white;
      padding: 1rem;
      border-radius: 8px;
    }
    .content { margin-top: 2rem; }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 2rem;
      background: white;
      border-radius: 8px;
      overflow: hidden;
    }
    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }
    th { background: #f7f7f7; }
    tr:hover { background: #f1f1f1; }
    a.button {
      background: #111;
      color: #fff;
      padding: 8px 12px;
      text-decoration: none;
      border-radius: 5px;
    }
  </style>
</head>
<body>

<div class="nav">
  <h2>👑 Admin Dashboard</h2>
  <a href="logout.php" class="button">Logout</a>
</div>

<div class="content">
  <h3>Restaurants</h3>
  <table>
    <tr><th>ID</th><th>Name</th><th>Location</th><th>Cuisine</th><th>Manager</th></tr>
    <?php
    $res = $conn->query("SELECT r.*, u.name AS manager_name 
                         FROM restaurants r 
                         LEFT JOIN users u ON r.manager_id = u.id");
    while ($row = $res->fetch_assoc()) {
      echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['name']}</td>
        <td>{$row['location']}</td>
        <td>{$row['cuisine']}</td>
        <td>{$row['manager_name']}</td>
      </tr>";
    }
    ?>
  </table>

  <h3>Bookings</h3>
  <table>
    <tr><th>ID</th><th>Customer</th><th>Restaurant</th><th>Date</th><th>Time</th><th>Table</th><th>Status</th></tr>
    <?php
    $res = $conn->query("SELECT b.*, r.name AS restaurant_name, u.name AS user_name, t.table_name
                         FROM bookings b
                         JOIN restaurants r ON b.restaurant_id = r.id
                         JOIN users u ON b.user_id = u.id
                         JOIN tables t ON b.table_id = t.id
                         ORDER BY b.booking_date DESC");
    while ($row = $res->fetch_assoc()) {
      echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['user_name']}</td>
        <td>{$row['restaurant_name']}</td>
        <td>{$row['booking_date']}</td>
        <td>{$row['booking_time']}</td>
        <td>{$row['table_name']}</td>
        <td>{$row['status']}</td>
      </tr>";
    }
    ?>
  </table>

  <h3>Users</h3>
  <table>
    <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>
    <?php
    $res = $conn->query("SELECT id, name, email, role FROM users");
    while ($row = $res->fetch_assoc()) {
      echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['name']}</td>
        <td>{$row['email']}</td>
        <td>{$row['role']}</td>
      </tr>";
    }
    ?>
  </table>
</div>

</body>
</html>

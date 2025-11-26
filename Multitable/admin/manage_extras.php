<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';
if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'manager') {
  header("Location: index.php");
  exit;
}

$restaurant_id = intval($_SESSION['admin']['restaurant_id']);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_extra'])) {
  $name = trim($_POST['name']);
  $description = trim($_POST['description']);
  $price = floatval($_POST['price']);
  if ($name && $price > 0) {
    $stmt = $conn->prepare("INSERT INTO extra_services (restaurant_id, name, description, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issd", $restaurant_id, $name, $description, $price);
    $stmt->execute();
    $stmt->close();
    $message = "✅ Extra service added.";
  } else $message = "⚠️ Please fill in all required fields.";
}

// Delete extra
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $conn->query("DELETE FROM extra_services WHERE id=$id AND restaurant_id=$restaurant_id");
  $message = "🗑️ Service removed.";
}

$extras = $conn->query("SELECT * FROM extra_services WHERE restaurant_id=$restaurant_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Manage Extra Services</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body { font-family:"Poppins",sans-serif; background:#f6f8fa; margin:0; color:#333; }
.container { max-width:900px; margin:40px auto; background:#fff; padding:30px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.1); }
table { width:100%; border-collapse:collapse; margin-top:15px; }
th, td { padding:10px; border-bottom:1px solid #eee; text-align:left; }
th { background:#111; color:#fff; }
button, a.btn { background:#111; color:white; border:none; padding:8px 14px; border-radius:6px; text-decoration:none; cursor:pointer; }
button:hover, a.btn:hover { background:#333; }
</style>
</head>
<body>
<div class="container">
  <a href="dashboard_manager.php" class="btn">← Back</a>
  <h2>Manage Extra Services</h2>
  <?php if ($message): ?><p><?= htmlspecialchars($message) ?></p><?php endif; ?>

  <form method="POST">
    <input type="text" name="name" placeholder="Service Name" required>
    <input type="text" name="description" placeholder="Description">
    <input type="number" name="price" step="0.01" placeholder="Price (₹)" required>
    <button name="add_extra" type="submit">Add Service</button>
  </form>

  <table>
    <tr><th>ID</th><th>Name</th><th>Description</th><th>Price</th><th>Action</th></tr>
    <?php while ($e = $extras->fetch_assoc()): ?>
      <tr>
        <td><?= $e['id'] ?></td>
        <td><?= htmlspecialchars($e['name']) ?></td>
        <td><?= htmlspecialchars($e['description']) ?></td>
        <td>₹<?= number_format($e['price'], 2) ?></td>
        <td><a href="?delete=<?= $e['id'] ?>" class="btn" onclick="return confirm('Delete this service?')">Delete</a></td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>
</body>
</html>

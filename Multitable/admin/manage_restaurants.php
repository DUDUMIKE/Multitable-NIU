<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';
if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'admin') {
  header("Location: index.php");
  exit;
}

$message = "";

// Add restaurant
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_restaurant'])) {
  $name = trim($_POST['name']);
  $location = trim($_POST['location']);
  $cuisine = trim($_POST['cuisine']);
  $image = trim($_POST['image']);

  if ($name && $location && $cuisine) {
    $stmt = $conn->prepare("INSERT INTO restaurants (name, location, cuisine, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $location, $cuisine, $image);
    if ($stmt->execute()) $message = "✅ Restaurant added successfully!";
    else $message = "❌ Error adding restaurant.";
    $stmt->close();
  } else $message = "⚠️ Fill in all fields.";
}

// Delete restaurant
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $del = $conn->prepare("DELETE FROM restaurants WHERE id=?");
  $del->bind_param("i", $id);
  $del->execute();
  $del->close();
  $message = "🗑️ Restaurant removed.";
}

// Fetch all restaurants
$restaurants = $conn->query("SELECT * FROM restaurants ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Manage Restaurants | MultiTable</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body { font-family:"Poppins",sans-serif; background:#f6f8fa; }
.container { max-width:900px; margin:40px auto; background:white; padding:30px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.08); }
h2 { margin-bottom:20px; }
form { display:flex; flex-wrap:wrap; gap:10px; margin-bottom:25px; }
input { padding:8px; border:1px solid #ccc; border-radius:6px; flex:1; }
button { background:#111; color:white; border:none; padding:8px 14px; border-radius:6px; cursor:pointer; }
button:hover { background:#333; }
.message { background:#f0f8ff; padding:10px; border-radius:6px; margin-bottom:15px; color:#111; }
a.manage { color:#111; font-weight:bold; text-decoration:none; }
a.delete { color:#c0392b; text-decoration:none; font-weight:bold; }
a.manage:hover, a.delete:hover { text-decoration:underline; }
table { width:100%; border-collapse:collapse; margin-top:15px; }
th, td { padding:10px; border-bottom:1px solid #eee; text-align:left; }
th { background:#111; color:white; }
</style>
</head>
<body>

<div class="container">
  <a href="dashboard_admin.php" class="back" style="text-decoration:none;display:inline-block;background:#111;color:white;padding:8px 12px;border-radius:6px;">← Back</a>
  <h2>Manage Restaurants</h2>

  <?php if ($message): ?><div class="message"><?= htmlspecialchars($message) ?></div><?php endif; ?>

  <form method="POST">
    <input type="text" name="name" placeholder="Restaurant Name" required>
    <input type="text" name="location" placeholder="Location" required>
    <input type="text" name="cuisine" placeholder="Cuisine" required>
    <input type="text" name="image" placeholder="Image URL (optional)">
    <button type="submit" name="add_restaurant">Add Restaurant</button>
  </form>

  <table>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Location</th>
      <th>Cuisine</th>
      <th>Actions</th>
    </tr>
    <?php while($r = $restaurants->fetch_assoc()): ?>
    <tr>
      <td><?= $r['id'] ?></td>
      <td><?= htmlspecialchars($r['name']) ?></td>
      <td><?= htmlspecialchars($r['location']) ?></td>
      <td><?= htmlspecialchars($r['cuisine']) ?></td>
      <td>
        <a href="manage_managers.php?restaurant_id=<?= $r['id'] ?>" class="manage">Manage Managers</a> |
        <a href="?delete=<?= $r['id'] ?>" class="delete" onclick="return confirm('Delete this restaurant?')">Delete</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

</body>
</html>

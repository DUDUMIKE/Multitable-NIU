<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

// Ensure only logged-in admins/hotel managers can access
if (!isset($_SESSION['admin'])) {
  header("Location: index.php");
  exit;
}

$restaurant_id = $_SESSION['admin']['restaurant_id'] ?? 1; // adjust based on your session setup
$message = "";

// Handle Add Service
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
  $name = $_POST['name'];
  $description = $_POST['description'];
  $price = $_POST['price'];
  $category = $_POST['category'];

  $stmt = $conn->prepare("INSERT INTO extra_services (restaurant_id, name, description, price, category)
                          VALUES (?,?,?,?,?)");
  $stmt->bind_param("issds", $restaurant_id, $name, $description, $price, $category);
  $stmt->execute();
  $stmt->close();
  $message = "Service added successfully!";
}

// Handle Delete
if (isset($_GET['delete'])) {
  $id = intval($_GET['delete']);
  $conn->query("DELETE FROM extra_services WHERE id = $id");
  $message = "Service deleted successfully!";
}

// Fetch Services
$stmt = $conn->prepare("SELECT * FROM extra_services WHERE restaurant_id = ?");
$stmt->bind_param("i", $restaurant_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Extra Services</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    body { font-family: "Poppins", sans-serif; padding: 20px; background: #f5f7fa; }
    .container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    h2 { margin-bottom: 20px; }
    .message { background: #e3fcef; color: #267a41; padding: 10px; border-radius: 6px; margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
    th { background: #f2f2f2; }
    form { margin-bottom: 30px; }
    input, textarea, select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 6px; margin-bottom: 10px; }
    button { background: #111; color: #fff; padding: 10px 15px; border: none; border-radius: 8px; cursor: pointer; }
    button:hover { background: #333; }
    a.delete { color: red; text-decoration: none; }
  </style>
</head>
<body>

<div class="container">
  <h2>Manage Extra Services</h2>

  <?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <form method="post">
    <h3>Add New Service</h3>
    <input type="text" name="name" placeholder="Service Name" required>
    <textarea name="description" placeholder="Description" rows="3"></textarea>
    <input type="number" name="price" step="0.01" placeholder="Price" required>
    <select name="category" required>
      <option value="">-- Select Category --</option>
      <option value="decorations">Decorations</option>
      <option value="entertainment">Entertainment</option>
      <option value="catering">Catering</option>
      <option value="special setup">Special Setup</option>
    </select>
    <button type="submit" name="add_service">Add Service</button>
  </form>

  <h3>Available Services</h3>
  <table>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Category</th>
      <th>Price</th>
      <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['category']) ?></td>
        <td>₹<?= number_format($row['price'], 2) ?></td>
        <td><a href="?delete=<?= $row['id'] ?>" class="delete" onclick="return confirm('Delete this service?')">Delete</a></td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>

</body>
</html>
<?php $stmt->close(); ?>

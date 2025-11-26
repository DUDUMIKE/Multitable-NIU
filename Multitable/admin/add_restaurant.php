<?php
// admin/add_restaurant.php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';
if (!isset($_SESSION['admin'])) { header("Location: index.php"); exit; }

$msg='';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? ''; $location = $_POST['location'] ?? ''; $cuisine = $_POST['cuisine'] ?? '';
    $desc = $_POST['description'] ?? ''; $image = $_POST['image'] ?? ''; $rating = floatval($_POST['rating'] ?? 4.5); $price = $_POST['price_range'] ?? '$$';
    $ins = $conn->prepare("INSERT INTO restaurants (name,location,cuisine,description,image,rating,price_range) VALUES (?,?,?,?,?,?,?)");
    $ins->bind_param('ssssdis',$name,$location,$cuisine,$desc,$image,$rating,$price);
    if ($ins->execute()) $msg = "Restaurant added.";
    else $msg = "Failed.";
    $ins->close();
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Add Restaurant</title><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<header><h2>Add Restaurant</h2><p><a href="dashboard.php">Back</a></p></header>
<main>
<?php if ($msg): ?><div class="form-message"><?=htmlspecialchars($msg)?></div><?php endif; ?>
<form method="post" class="admin-form">
  <label>Name</label><input name="name" required>
  <label>Location</label><input name="location">
  <label>Cuisine</label><input name="cuisine">
  <label>Description</label><textarea name="description"></textarea>
  <label>Image URL</label><input name="image" placeholder="assets/images/...">
  <label>Rating</label><input name="rating" value="4.5">
  <label>Price Range</label><input name="price_range" value="$$">
  <button class="primary-btn" type="submit">Add</button>
</form>
</main>
</body></html>

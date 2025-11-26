<?php
// admin/add_table.php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';
if (!isset($_SESSION['admin'])) { header("Location: index.php"); exit; }

$msg='';
$restQ = $conn->query("SELECT id,name FROM restaurants ORDER BY name");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $restaurant_id = intval($_POST['restaurant_id'] ?? 0);
    $table_name = $_POST['table_name'] ?? '';
    $table_type = $_POST['table_type'] ?? '';
    $capacity = intval($_POST['capacity'] ?? 2);
    $premium = floatval($_POST['premium_fee'] ?? 0);
    $ins = $conn->prepare("INSERT INTO tables (restaurant_id,table_name,table_type,capacity,premium_fee,available) VALUES (?,?,?,?,?,1)");
    $ins->bind_param('issid',$restaurant_id,$table_name,$table_type,$capacity,$premium);
    if ($ins->execute()) $msg = "Table added.";
    else $msg = "Failed.";
    $ins->close();
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Add Table</title><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<header><h2>Add Table</h2><p><a href="dashboard.php">Back</a></p></header>
<main>
<?php if ($msg): ?><div class="form-message"><?=htmlspecialchars($msg)?></div><?php endif; ?>
<form method="post" class="admin-form">
  <label>Restaurant</label>
  <select name="restaurant_id" required>
    <?php while ($r=$restQ->fetch_assoc()): ?>
      <option value="<?=$r['id']?>"><?=htmlspecialchars($r['name'])?></option>
    <?php endwhile; ?>
  </select>

  <label>Table Name (e.g., T1 / Window Table)</label><input name="table_name" required>
  <label>Table Type (e.g., Window View, Private Booth, Balcony)</label><input name="table_type" required>
  <label>Capacity</label><input name="capacity" type="number" value="2" min="1">
  <label>Premium Fee (INR)</label><input name="premium_fee" value="0">
  <button class="primary-btn" type="submit">Add Table</button>
</form>
</main>
</body></html>

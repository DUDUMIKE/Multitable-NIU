<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$restaurant_id = intval($_GET['restaurant_id'] ?? 0);
if (!$restaurant_id) die("Invalid restaurant ID");

$message = "";

// === Handle Actions ===

// Add new manager
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_manager'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($name && $email && $password) {
        $check = $conn->prepare("SELECT id FROM admins WHERE email=?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "⚠️ Email already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO admins (username, email, password, role, restaurant_id, approved, deleted) VALUES (?, ?, ?, 'manager', ?, 'approved', 0)");
            $stmt->bind_param("sssi", $name, $email, $hash, $restaurant_id);
            if ($stmt->execute()) $message = "✅ Manager added successfully.";
            else $message = "❌ Error adding manager.";
            $stmt->close();
        }
        $check->close();
    } else {
        $message = "⚠️ Please fill in all fields.";
    }
}

// Suspend manager
if (isset($_GET['suspend'])) {
    $id = intval($_GET['suspend']);
    $conn->query("UPDATE admins SET approved='suspended' WHERE id=$id AND role='manager'");
    $message = "⛔ Manager suspended.";
}

// Approve manager
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $conn->query("UPDATE admins SET approved='approved' WHERE id=$id AND role='manager'");
    $message = "✅ Manager approved.";
}

// Soft delete (unlink)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("UPDATE admins SET deleted=1, restaurant_id=NULL WHERE id=$id AND role='manager'");
    $message = "🗑️ Manager unlinked from restaurant (soft deleted).";
}

// Fetch restaurant + managers
$restaurant = $conn->query("SELECT name FROM restaurants WHERE id=$restaurant_id")->fetch_assoc()['name'];
$managers = $conn->query("
    SELECT id, username, email, approved, deleted
    FROM admins
    WHERE role='manager' AND (restaurant_id=$restaurant_id OR (restaurant_id IS NULL AND deleted=1))
    ORDER BY deleted ASC, approved DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Manage Managers | MultiTable</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body { font-family:"Poppins",sans-serif; background:#f6f8fa; }
.container { max-width:950px; margin:40px auto; background:white; padding:30px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.08); }
h2 { margin-bottom:20px; }
form { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:25px; }
input { padding:8px; border:1px solid #ccc; border-radius:6px; flex:1; }
button { background:#111; color:white; border:none; padding:8px 14px; border-radius:6px; cursor:pointer; }
button:hover { background:#333; }
table { width:100%; border-collapse:collapse; margin-top:15px; }
th, td { padding:10px; border-bottom:1px solid #eee; text-align:left; }
th { background:#111; color:white; }
.message { background:#f0f8ff; padding:10px; border-radius:6px; margin-bottom:15px; color:#111; }
.badge { padding:4px 8px; border-radius:5px; color:white; font-size:0.85rem; }
.badge.approved { background:green; }
.badge.suspended { background:orange; }
.badge.deleted { background:red; }
a.action { text-decoration:none; font-weight:bold; margin-right:8px; }
a.approve { color:green; }
a.suspend { color:orange; }
a.delete { color:red; }
a.action:hover { text-decoration:underline; }
.back { display:inline-block; background:#111; color:white; padding:8px 12px; border-radius:6px; text-decoration:none; }
</style>
</head>
<body>

<div class="container">
  <a href="manage_restaurants.php" class="back">← Back</a>
  <h2>Manage Managers for <?= htmlspecialchars($restaurant) ?></h2>

  <?php if ($message): ?><div class="message"><?= htmlspecialchars($message) ?></div><?php endif; ?>

  <form method="POST">
    <input type="text" name="name" placeholder="Manager Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" name="add_manager">Add Manager</button>
  </form>

  <table>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Email</th>
      <th>Status</th>
      <th>Actions</th>
    </tr>
    <?php while($m = $managers->fetch_assoc()): ?>
      <tr>
        <td><?= $m['id'] ?></td>
        <td><?= htmlspecialchars($m['username']) ?></td>
        <td><?= htmlspecialchars($m['email']) ?></td>
        <td>
          <?php if ($m['deleted']): ?>
            <span class="badge deleted">Deleted</span>
          <?php else: ?>
            <span class="badge <?= $m['approved'] ?>"><?= ucfirst($m['approved']) ?></span>
          <?php endif; ?>
        </td>
        <td>
          <?php if (!$m['deleted']): ?>
            <?php if ($m['approved'] === 'approved'): ?>
              <a href="?restaurant_id=<?= $restaurant_id ?>&suspend=<?= $m['id'] ?>" class="action suspend">Suspend</a>
            <?php else: ?>
              <a href="?restaurant_id=<?= $restaurant_id ?>&approve=<?= $m['id'] ?>" class="action approve">Approve</a>
            <?php endif; ?>
            <a href="?restaurant_id=<?= $restaurant_id ?>&delete=<?= $m['id'] ?>" class="action delete" onclick="return confirm('Unlink this manager from restaurant?');">Unlink</a>
          <?php else: ?>
            <em>Unlinked</em>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>

</body>
</html>

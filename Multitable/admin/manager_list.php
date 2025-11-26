<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'admin') {
  header("Location: index.php");
  exit;
}

// Handle actions
if (isset($_GET['action'], $_GET['id'])) {
  $id = intval($_GET['id']);
  $action = $_GET['action'];

  if ($action === 'approve') {
    $stmt = $conn->prepare("UPDATE admins SET status='approved' WHERE id=? AND role='manager'");
  } elseif ($action === 'suspend') {
    $stmt = $conn->prepare("UPDATE admins SET status='suspended' WHERE id=? AND role='manager'");
  } elseif ($action === 'delete') {
    $stmt = $conn->prepare("DELETE FROM admins WHERE id=? AND role='manager'");
  }
  if ($stmt) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
  }
  header("Location: manager_list.php");
  exit;
}

// Fetch managers
$result = $conn->query("
  SELECT a.*, r.name AS restaurant_name 
  FROM admins a 
  LEFT JOIN restaurants r ON a.restaurant_id = r.id 
  WHERE a.role='manager' 
  ORDER BY a.status, a.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Manage Hotel Managers | MultiTable</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body { font-family:"Poppins",sans-serif; background:#f8f9fa; padding:30px; }
h2 { color:#111; }
table { width:100%; border-collapse:collapse; margin-top:20px; background:white; border-radius:8px; overflow:hidden; }
th, td { padding:10px; border-bottom:1px solid #ddd; text-align:left; }
th { background:#111; color:white; }
.action-btns a {
  padding:6px 10px; border-radius:6px; color:#fff; text-decoration:none; margin-right:6px;
  font-size:0.9rem;
}
.approve { background:#28a745; }
.suspend { background:#f39c12; }
.delete { background:#c0392b; }
a:hover { opacity:0.85; }
</style>
</head>
<body>

<h2>Manage Hotel Managers</h2>
<a href="dashboard_admin.php" style="text-decoration:none;background:#111;color:#fff;padding:8px 12px;border-radius:6px;">← Back to Dashboard</a>

<table>
<tr>
  <th>ID</th>
  <th>Manager Name</th>
  <th>Email</th>
  <th>Hotel</th>
  <th>Status</th>
  <th>Action</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
  <td><?= $row['id'] ?></td>
  <td><?= htmlspecialchars($row['username']) ?></td>
  <td><?= htmlspecialchars($row['email']) ?></td>
  <td><?= htmlspecialchars($row['restaurant_name'] ?? '—') ?></td>
  <td style="color:
    <?= $row['status']=='approved'?'green':($row['status']=='pending'?'orange':'red') ?>;
    font-weight:600;">
    <?= ucfirst($row['status']) ?>
  </td>
  <td class="action-btns">
    <?php if ($row['status'] === 'pending'): ?>
      <a href="?action=approve&id=<?= $row['id'] ?>" class="approve">Approve</a>
    <?php elseif ($row['status'] === 'approved'): ?>
      <a href="?action=suspend&id=<?= $row['id'] ?>" class="suspend">Suspend</a>
    <?php elseif ($row['status'] === 'suspended'): ?>
      <a href="?action=approve&id=<?= $row['id'] ?>" class="approve">Reactivate</a>
    <?php endif; ?>
    <a href="?action=delete&id=<?= $row['id'] ?>" class="delete" onclick="return confirm('Delete this manager?')">Delete</a>
  </td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>

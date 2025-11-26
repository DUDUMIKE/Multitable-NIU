<?php
// admin/dashboard.php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';
if (!isset($_SESSION['admin'])) { header("Location: index.php"); exit; }

// handle marking booking paid
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'mark_paid' && isset($_POST['booking_id'])) {
        $id = intval($_POST['booking_id']);
        $u = $conn->prepare("UPDATE bookings SET payment_status='paid' WHERE id=?");
        $u->bind_param('i',$id); $u->execute(); $u->close();
    }
}

// fetch bookings
$q = $conn->query("SELECT b.*, u.name AS customer, r.name AS restaurant, t.table_name FROM bookings b
                   JOIN users u ON u.id=b.user_id
                   JOIN tables t ON t.id=b.table_id
                   JOIN restaurants r ON r.id=t.restaurant_id
                   ORDER BY b.created_at DESC");
?>


<!-- Added <a href="manage_services.php" class="btn">Manage Extra Services</a> -->
<!doctype html><html><head><meta charset="utf-8"><title>Admin Dashboard</title><link rel="stylesheet" href="../assets/css/style.css"></head><body>
<header><h2>Admin Dashboard</h2><p>Welcome, <?=htmlspecialchars($_SESSION['admin']['name'])?> | <a href="../logout.php">Logout</a></p></header>
<main class="admin-main">
  <section>
    <h3>Recent bookings</h3>
    <table class="admin-table">
      <thead><tr><th>ID</th><th>Customer</th><th>Restaurant</th><th>Table</th><th>When</th><th>Guests</th><th>Payment</th><th>Action</th></tr></thead>
      <tbody>
      <?php while ($bk = $q->fetch_assoc()): ?>
        <tr>
          <td><?=$bk['id']?></td>
          <td><?=htmlspecialchars($bk['customer'])?></td>
          <td><?=htmlspecialchars($bk['restaurant'])?></td>
          <td><?=htmlspecialchars($bk['table_name'])?></td>
          <td><?=htmlspecialchars($bk['booking_date'].' '.$bk['booking_time'])?></td>
          <td><?=intval($bk['guests'])?></td>
          <td><?=htmlspecialchars($bk['payment_status'])?></td>
          <td>
            <?php if ($bk['payment_status'] !== 'paid'): ?>
              <form method="post" style="display:inline">
                <input type="hidden" name="booking_id" value="<?=$bk['id']?>">
                <input type="hidden" name="action" value="mark_paid">
                <button class="primary-btn">Mark Paid</button>
              </form>
            <?php else: ?> — <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </section>

  <section>
    <h3>Manage</h3>
    <p><a href="add_restaurant.php">Add Restaurant</a> | <a href="add_table.php">Add Table</a> | <a href="manage_extras.php" class="admin-btn">Manage Extra Services</a></p> 
  </section>
</main>
</body></html>

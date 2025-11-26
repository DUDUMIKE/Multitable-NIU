<?php
// index.php
session_start();
require_once __DIR__ . '/includes/db_connect.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>TableBooker — Discover & Book Tables</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php
// simple header
?>
<header class="site-header">
  <div class="logo">
    <img src="https://img.icons8.com/ios-filled/50/FFFFFF/restaurant.png" alt="logo" />
    <div class="logo-text"><h1>BookBite</h1><p>Reserve tables at great restaurants</p></div>

  </div>

  <div class="header-actions">
    <?php if (isset($_SESSION['user'])): ?>
      <div class="user">Hello, <?= htmlspecialchars($_SESSION['user']['name']) ?> | <a href="logout.php">Logout</a></div>
    <?php else: ?>
      <a class="auth" href="login.php">Login</a>
      <a class="auth" href="register.php">Register</a>
    <?php endif; ?>
    <a class="admin-link" href="admin/index.php">Admin</a>
  </div>
</header>

<section class="hero">
<div class="hero-content" 
     style="background:url('assets/images/Heroimg.jpg') center/cover no-repeat;
            width:100%; height:400px;
            display:flex; flex-direction:column;
            justify-content:center; align-items:center;
            text-align:center; padding:0; margin:0;">
  <h2 style="color:white; margin:0;">Find the perfect table</h2>
  <p style="color:white; margin:0;">Filter by cuisine, location or table type and book in seconds.</p>
</div>

  
  <div class="filters">
    <input id="search" placeholder="Search restaurants, cuisines or table names...">
    <select id="cuisine">
      <option value="">All Cuisines</option>
      <?php
      $cQ = $conn->query("SELECT DISTINCT cuisine FROM restaurants ORDER BY cuisine");
      while ($cR = $cQ->fetch_assoc()) echo '<option>' . htmlspecialchars($cR['cuisine']) . '</option>';
      ?>
    </select>
    <select id="location">
      <option value="">All Locations</option>
      <?php
      $lQ = $conn->query("SELECT DISTINCT location FROM restaurants ORDER BY location");
      while ($lR = $lQ->fetch_assoc()) echo '<option>' . htmlspecialchars($lR['location']) . '</option>';
      ?>
    </select>
    
    <!-- Just added this block -->
    <select id="table_type">
    <option value="">All Table Types</option>
    <?php
    $tQ = $conn->query("SELECT DISTINCT table_type FROM tables WHERE table_type IS NOT NULL AND table_type != '' ORDER BY table_type");
    while ($tR = $tQ->fetch_assoc()) echo '<option>' . htmlspecialchars($tR['table_type']) . '</option>';
    ?>
  </select>

  </div>
</section>

<section id="restaurants" class="restaurants">
  <?php
  // initial load: show all available tables grouped by restaurant
  // We'll use filter.php via AJAX for live updates; but include initial HTML server-side for SEO/fallback.
  $sql = "SELECT r.*, t.id AS table_id, t.table_name, t.table_type, t.capacity, t.premium_fee
          FROM restaurants r
          JOIN tables t ON t.restaurant_id = r.id
          ORDER BY r.name, t.table_name";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  $res = $stmt->get_result();
  $lastRest = 0;
  while ($row = $res->fetch_assoc()):
  ?>
    <div class="restaurant-card">
      <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
      <div class="info">
        <h3><?= htmlspecialchars($row['name']) ?> <span class="rating">★ <?= htmlspecialchars($row['rating']) ?></span></h3>
        <p class="details"><?= htmlspecialchars($row['cuisine']) ?> · <?= htmlspecialchars($row['location']) ?> · <?= htmlspecialchars($row['price_range']) ?></p>
        <p class="desc"><?= htmlspecialchars($row['description']) ?></p>

        <div class="table-line">
          <div class="table-meta">
            <strong><?= htmlspecialchars($row['table_name']) ?></strong>
            <small><?= htmlspecialchars($row['table_type']) ?> · <?= intval($row['capacity']) ?> seats</small>
          </div>
          <div class="table-actions">
            <a class="view" href="javascript:void(0);" onclick="openBookingModal(<?= $row['table_id'] ?>,'<?= addslashes(htmlspecialchars($row['name'])) ?>','<?= addslashes(htmlspecialchars($row['table_name'])) ?>')">Book</a>
            <a class="price">₹<?= number_format($row['premium_fee'],2) ?></a>
          </div>
        </div>

      </div>
    </div>
  <?php endwhile; $stmt->close(); ?>
</section>

<!-- BOOKING MODAL -->
<div id="bookingModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeBookingModal()">&times;</span>
    <h3 id="restaurantTitle">Book a Table</h3>

    <form id="bookingForm" method="POST" action="booking.php">
      <input type="hidden" name="restaurant_id" id="restaurantId">

      <label>Date:</label>
      <input type="date" name="booking_date" required>

      <label>Time:</label>
      <input type="time" name="booking_time" required>

      <label>Guests:</label>
      <select name="guests" required>
        <option value="2">Couple (2)</option>
        <option value="4">3–5 Guests</option>
        <option value="8">5–10 Guests</option>
      </select>

      <label>Table Type:</label>
      <select name="table_id" id="tableSelect" required></select>
      

      <button type="submit" class="primary-btn">Confirm Booking</button>
    </form>
  </div>
</div>


<?php include __DIR__ . '/assets/js/main-modal.php'; /* small modal HTML is in main.js injection helper */ ?>

<script src="assets/js/main.js"></script>

<script>
function openBookingModal(tableId, restaurantName, tableName) {
  const modal = document.getElementById('bookingModal');
  document.getElementById('restaurantTitle').textContent = `${restaurantName} — ${tableName}`;
  document.getElementById('restaurantId').value = tableId;

  // Optional: load available table types dynamically (via PHP/AJAX)
  fetch(`get_tables.php?table_id=${tableId}`)  
    .then(res => res.json())
    .then(data => {
      const tableSelect = document.getElementById('tableSelect');
      tableSelect.innerHTML = '';
      data.forEach(tbl => {
        const opt = document.createElement('option');
        opt.value = tbl.id;
        opt.textContent = `${tbl.table_name} (${tbl.table_type})`;
        tableSelect.appendChild(opt);
      });
    });

  modal.style.display = 'block';
}

function closeBookingModal() {
  document.getElementById('bookingModal').style.display = 'none';
}

// Close modal if clicking outside
window.onclick = function(e) {
  const modal = document.getElementById('bookingModal');
  if (e.target == modal) modal.style.display = "none";
}
</script>

<!--simple Footer-->
<footer id="contact" class="footer">
  <p>contact us: +91 123 456 7890 | Book@BookBite.com</p>
  <p>&copy; 2024 BookBite. All Booking Reserved</p> 
</footer>



</body>
</html>

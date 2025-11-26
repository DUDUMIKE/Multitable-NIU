<?php
// booking.php
session_start();
require_once __DIR__ . '/includes/db_connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user']['id'];
    $table_id = intval($_POST['table_id']);
    $date = $_POST['booking_date'];
    $time = $_POST['booking_time'];
    $guests = intval($_POST['guests']);

    // Get restaurant_id for the table
    $stmt = $conn->prepare("SELECT restaurant_id FROM tables WHERE id = ?");
    $stmt->bind_param("i", $table_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) {
        die("Invalid table.");
    }
    $restaurant_id = $res->fetch_assoc()['restaurant_id'];
    $stmt->close();

    // Prevent double-booking (confirmed only)
    $check = $conn->prepare("
        SELECT id FROM bookings 
        WHERE table_id = ? AND booking_date = ? AND booking_time = ? 
        AND status = 'confirmed'
    ");
    $check->bind_param("iss", $table_id, $date, $time);
    $check->execute();
    $r = $check->get_result();
    if ($r->num_rows > 0) {
        echo "<script>alert('This table is already confirmed for that time. Please choose another.'); window.history.back();</script>";
        exit;
    }
    $check->close();

    // Insert new booking (pending)
    $stmt = $conn->prepare("
        INSERT INTO bookings (user_id, restaurant_id, table_id, booking_date, booking_time, guests, status)
        VALUES (?, ?, ?, ?, ?, ?, 'pending')
    ");
    $stmt->bind_param("iiissi", $user_id, $restaurant_id, $table_id, $date, $time, $guests);
    $stmt->execute();
    $booking_id = $stmt->insert_id;
    $stmt->close();

    // Optionally mark table as temporarily locked
    $up = $conn->prepare("UPDATE tables SET status='pending' WHERE id=?");
    $up->bind_param("i", $table_id);
    $up->execute();
    $up->close();

    // Redirect to booking summary
    header("Location: booking_summary.php?booking_id=$booking_id");
    exit;
}
?>

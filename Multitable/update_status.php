<?php
// update_status.php
session_start();
require_once __DIR__ . '/includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

$booking_id = intval($_POST['booking_id']);
$action = $_POST['action'];

$new_status = null;
if ($action === 'confirm') {
    $new_status = 'confirmed';
} elseif ($action === 'cancel') {
    $new_status = 'cancelled';
}

if ($new_status) {
    // Update booking
    $stmt = $conn->prepare("UPDATE bookings SET status=? WHERE id=?");
    $stmt->bind_param("si", $new_status, $booking_id);
    $stmt->execute();
    $stmt->close();

    // If cancelled, free up table
    if ($new_status === 'cancelled') {
        $stmt = $conn->prepare("
            UPDATE tables 
            SET status='available' 
            WHERE id=(SELECT table_id FROM bookings WHERE id=?)
        ");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $stmt->close();
    }

    // If confirmed, mark table as booked
    if ($new_status === 'confirmed') {
        $stmt = $conn->prepare("
            UPDATE tables 
            SET status='booked' 
            WHERE id=(SELECT table_id FROM bookings WHERE id=?)
        ");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: booking_summary.php?booking_id=$booking_id");
    exit;
} else {
    die("Invalid action");
}
?>

<?php
// generate_receipt.php
require_once __DIR__ . '/includes/db_connect.php';
session_start();

// Try to include FPDF from common locations
$fpdf_path1 = __DIR__ . '/libs/fpdf.php';
$fpdf_path2 = __DIR__ . '/libs/fpdf/fpdf.php';
if (file_exists($fpdf_path1)) {
    require_once $fpdf_path1;
} elseif (file_exists($fpdf_path2)) {
    require_once $fpdf_path2;
} else {
    die("FPDF library not found. Please download from https://www.fpdf.org and place fpdf.php into /libs/ (either libs/fpdf.php or libs/fpdf/fpdf.php).");
}

if (!isset($_GET['booking_id'])) {
    die("Missing booking_id");
}
$booking_id = intval($_GET['booking_id']);

// Fetch booking + restaurant + table + customer
$stmt = $conn->prepare("
  SELECT b.*, r.name AS restaurant_name, r.location AS restaurant_location, r.image AS restaurant_image,
         t.table_name, t.table_type, t.capacity, t.premium_fee,
         u.name AS customer_name, u.email AS customer_email, u.phone AS customer_phone
  FROM bookings b
  JOIN restaurants r ON b.restaurant_id = r.id
  JOIN tables t ON b.table_id = t.id
  JOIN users u ON b.user_id = u.id
  WHERE b.id = ?
");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$booking) {
    die("Booking not found.");
}

// Fetch extras added to booking
$extraQ = $conn->prepare("
  SELECT es.name, es.price
  FROM booking_services bs
  JOIN extra_services es ON bs.extra_service_id = es.id
  WHERE bs.booking_id = ?
");
$extraQ->bind_param("i", $booking_id);
$extraQ->execute();
$extraRes = $extraQ->get_result();
$extras = [];
$total_extras = 0.0;
while ($row = $extraRes->fetch_assoc()) {
    $extras[] = $row;
    $total_extras += floatval($row['price']);
}
$extraQ->close();

// Fetch latest payment record for this booking (if any)
$payQ = $conn->prepare("
  SELECT id, upi_id, amount, payment_date, status, payment_method, reference_no
  FROM payments
  WHERE booking_id = ?
  ORDER BY id DESC
  LIMIT 1
");
$payQ->bind_param("i", $booking_id);
$payQ->execute();
$payment = $payQ->get_result()->fetch_assoc();
$payQ->close();

// Compute totals
$table_fee = floatval($booking['premium_fee']);
$grand_total = $table_fee + $total_extras;

// Build PDF
class PDF extends FPDF {
    // header with optional logo
    function Header() {
        // override in main code where we have restaurant image path
    }
}

// Create pdf
$pdf = new PDF('P','mm','A4');
$pdf->SetAutoPageBreak(true, 18);
$pdf->AddPage();

// --- Header: logo / title ---
$pdf->SetFont('Helvetica','B',18);
$pdf->Cell(0, 8, 'Payment Receipt', 0, 1, 'C');
$pdf->Ln(3);

// Restaurant block
$pdf->SetFont('Helvetica','B',12);
$pdf->Cell(0, 6, htmlspecialchars($booking['restaurant_name']), 0, 1, 'C');
$pdf->SetFont('Helvetica','',10);
if (!empty($booking['restaurant_location'])) {
    $pdf->Cell(0, 5, htmlspecialchars($booking['restaurant_location']), 0, 1, 'C');
}
$pdf->Ln(6);

// Left / Right columns: Booking info and Customer
$pdf->SetFont('Helvetica','B',11);
$pdf->Cell(95, 6, 'Booking Details', 0, 0);
$pdf->Cell(0, 6, 'Customer', 0, 1);

$pdf->SetFont('Helvetica','',10);
$pdf->Cell(95, 6, 'Booking ID: #'.$booking['id'], 0, 0);
$customerName = htmlspecialchars($booking['customer_name'] ?? 'Guest');
$pdf->Cell(0, 6, 'Name: '.$customerName, 0, 1);

$pdf->Cell(95, 6, 'Date: '.$booking['booking_date'], 0, 0);
$customerEmail = htmlspecialchars($booking['customer_email'] ?? '');
$pdf->Cell(0, 6, 'Email: '.$customerEmail, 0, 1);

$pdf->Cell(95, 6, 'Time: '.$booking['booking_time'], 0, 0);
$customerPhone = htmlspecialchars($booking['customer_phone'] ?? '');
if ($customerPhone) $pdf->Cell(0, 6, 'Phone: '.$customerPhone, 0, 1);
else $pdf->Cell(0,6, '',0,1);

$pdf->Cell(95, 6, 'Table: '.$booking['table_name'].' ('.$booking['table_type'].')', 0, 0);
$pdf->Cell(0, 6, 'Guests: '.intval($booking['guests']), 0, 1);

$pdf->Ln(6);

// --- Charges table ---
$pdf->SetFont('Helvetica','B',11);
$pdf->Cell(0,6,'Charges',0,1);

$pdf->SetFont('Helvetica','',10);
$labelW = 140;
$amountW = 40;

// Table fee
$pdf->Cell($labelW, 6, 'Table — '.$booking['table_name'], 0, 0);
$pdf->Cell($amountW, 6, '₹'.number_format($table_fee,2), 0, 1, 'R');

// Extras
if (count($extras) > 0) {
    foreach ($extras as $ex) {
        $pdf->Cell($labelW, 6, 'Extra — '.htmlspecialchars($ex['name']), 0, 0);
        $pdf->Cell($amountW, 6, '₹'.number_format(floatval($ex['price']),2), 0, 1, 'R');
    }
} else {
    $pdf->Cell($labelW, 6, 'Extras', 0, 0);
    $pdf->Cell($amountW, 6, '₹0.00', 0, 1, 'R');
}

$pdf->Ln(3);
$pdf->SetFont('Helvetica','B',12);
$pdf->Cell($labelW, 8, 'Grand Total', 0, 0);
$pdf->Cell($amountW, 8, '₹'.number_format($grand_total,2), 0, 1, 'R');
$pdf->Ln(6);

// --- Payment info ---
$pdf->SetFont('Helvetica','B',11);
$pdf->Cell(0,6,'Payment',0,1);

$pdf->SetFont('Helvetica','',10);
if ($payment) {
    $pdf->Cell(95,6,'Payment ID: #'.$payment['id'],0,0);
    $pdf->Cell(0,6,'Method: '.strtoupper($payment['payment_method']),0,1);

    $pdf->Cell(95,6,'Amount: ₹'.number_format(floatval($payment['amount']),2),0,0);
    $pdf->Cell(0,6,'Status: '.strtoupper($payment['status']),0,1);

    if (!empty($payment['upi_id'])) {
        $pdf->Cell(0,6,'UPI ID: '.htmlspecialchars($payment['upi_id']),0,1);
    }
    if (!empty($payment['reference_no'])) {
        $pdf->Cell(0,6,'Reference: '.htmlspecialchars($payment['reference_no']),0,1);
    }
    $pdf->Cell(0,6,'Recorded at: '.($payment['payment_date'] ?? $payment['created_at'] ?? date('Y-m-d H:i:s')),0,1);
} else {
    $pdf->Cell(0,6,'No payment recorded yet',0,1);
}
$pdf->Ln(8);

// Footer notes
$pdf->SetFont('Helvetica','I',9);
$pdf->MultiCell(0,5,"This is an automatically generated receipt for your booking. Please bring this receipt (digital or printed) to the restaurant when collecting services. For cash payments, please present a valid ID at the time of payment.\n\nThank you for choosing ".$booking['restaurant_name'].".",0,'L');

// Output as download
$filename = 'receipt_booking_'.$booking['id'].'.pdf';
$pdf->Output('D', $filename);
exit;

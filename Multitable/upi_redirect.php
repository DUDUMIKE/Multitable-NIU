<?php
session_start();

if (!isset($_GET['booking_id']) || !isset($_GET['amount'])) {
    die("Invalid UPI request");
}

$booking_id = intval($_GET['booking_id']);
$amount = floatval($_GET['amount']);

// Your business UPI ID
$merchant_upi = "yourbusiness@upi";
$merchant_name = "Restaurant Booking";

// Create UPI deep link
$upi_link = "upi://pay?pa=$merchant_upi&pn=$merchant_name&am=$amount&tn=Booking%20ID%20$booking_id";

?>
<!DOCTYPE html>
<html>
<head>
<title>Redirecting to UPI...</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script>
    // Redirect automatically to UPI App
    window.onload = function() {
        window.location.href = "<?= $upi_link ?>";

        // Backup message
        setTimeout(() => {
            document.getElementById("msg").style.display = "block";
        }, 3000);
    }
</script>

<style>
body {
    font-family: Arial;
    background:#f8f9fa;
    text-align:center;
    padding-top:40px;
}
.card {
    background:#fff;
    padding:25px;
    margin:auto;
    width:90%;
    max-width:400px;
    border-radius:12px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}
</style>

</head>
<body>

<div class="card">
    <h2>Opening UPI App...</h2>
    <p>Please complete your payment.</p>

    <p id="msg" style="display:none;color:#333;font-weight:bold;">
        If nothing happened, click this button:<br><br>
        <a href="<?= $upi_link ?>" 
           style="background:#0a7cff;color:white;padding:12px 20px;border-radius:8px;">
           Pay Using UPI App
        </a>
    </p>
</div>

</body>
</html>

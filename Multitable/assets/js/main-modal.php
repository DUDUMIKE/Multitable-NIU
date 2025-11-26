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

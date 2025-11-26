// assets/js/main.js
const searchInput = document.getElementById('search');
const cuisineSelect = document.getElementById('cuisine');
const locationSelect = document.getElementById('location');
const tableTypeSelect = document.getElementById('table_type');
const restaurantsContainer = document.getElementById('restaurants');

function filterRestaurants() {
  const search = searchInput.value;
  const cuisine = cuisineSelect.value;
  const location = locationSelect.value;
  const tableType = tableTypeSelect.value;

  fetch(`filter.php?search=${encodeURIComponent(search)}&cuisine=${encodeURIComponent(cuisine)}&location=${encodeURIComponent(location)}&table_type=${encodeURIComponent(tableType)}`)
    .then(res => res.text())
    .then(data => restaurantsContainer.innerHTML = data);
}

searchInput.addEventListener('input', filterRestaurants);
cuisineSelect.addEventListener('change', filterRestaurants);
locationSelect.addEventListener('change', filterRestaurants);
tableTypeSelect.addEventListener('change', filterRestaurants);


// debounce helper
function debounce(fn, wait){
  let t;
  return function(...args){ clearTimeout(t); t = setTimeout(()=>fn.apply(this,args), wait); };
}

// Booking modal helpers
function openBookingModal(tableId, restaurantName, tableName){
  // create modal if not exist
  if (!document.getElementById('bookingModal')){
    const modal = document.createElement('div');
    modal.id = 'bookingModal';
    modal.className = 'modal';
    modal.innerHTML = `
      <div class="modal-content">
        <span class="close" onclick="closeBookingModal()">&times;</span>
        <h3 id="restaurantTitle"></h3>
        <form id="bookingForm">
          <input type="hidden" name="table_id" id="table_id">
          <label>Date</label><input type="date" name="booking_date" required>
          <label>Time</label><input type="time" name="booking_time" required>
          <label>Guests</label>
          <select name="guests"><option value="2">2</option><option value="4">4</option><option value="6">6</option></select>
          <label>Special requests (optional)</label><textarea name="special" rows="2"></textarea>
          <button type="submit" class="primary-btn">Confirm booking</button>
        </form>
      </div>`;
    document.body.appendChild(modal);

    document.getElementById('bookingForm').addEventListener('submit', function(e){
      e.preventDefault();
      const fd = new FormData(this);
      fetch('booking.php', {method:'POST', body:fd})
        .then(r => r.redirected ? window.location = r.url : r.text())
        .then(txt => { try{ const j=JSON.parse(txt); if (j.error) alert(j.error);}catch(e){} });
    });
  }
  document.getElementById('restaurantTitle').innerText = 'Book ' + tableName + ' @ ' + restaurantName;
  document.getElementById('table_id').value = tableId;
  document.getElementById('bookingModal').style.display = 'flex';
}
function closeBookingModal(){ const m=document.getElementById('bookingModal'); if(m) m.style.display='none'; }



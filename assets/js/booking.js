(function () {
  var form = document.querySelector("[data-booking-form]");
  if (!form) return;

  var destination = form.querySelector("[name='destination_id']");
  var hotelInputs = Array.from(form.querySelectorAll("[name='hotel_id']"));
  var nights = form.querySelector("[name='nights']");
  var rooms = form.querySelector("[name='rooms']");
  var guests = form.querySelector("[name='guests']");
  var checkIn = form.querySelector("[name='check_in_date']");
  var checkoutText = document.querySelector("[data-checkout-text]");
  var totalBox = document.querySelector("[data-total-box]");
  var totalText = document.querySelector("[data-total-text]");
  var payment = form.querySelector("[name='payment_method']");
  var gcashPanel = document.querySelector("[data-payment-panel='GCash']");
  var cardPanel = document.querySelector("[data-payment-panel='Credit / Debit Card']");

  function numberValue(input, fallback) {
    var value = parseInt(input.value || fallback, 10);
    return Number.isFinite(value) ? value : fallback;
  }

  function selectedHotel() {
    return hotelInputs.find(function (input) {
      return input.checked;
    });
  }

  var stayList = document.querySelector(".stay-list");

  function updateHotels() {
    var destinationId = destination.value;
    hotelInputs.forEach(function (input) {
      var card = input.closest(".hotel-card");
      var serves = (input.dataset.serves || "").split(",");
      var available = !destinationId || serves.indexOf(destinationId) !== -1;
      card.classList.toggle("hidden", !available);
      if (!available && input.checked) input.checked = false;
    });

    // List the nearest stays to the chosen destination first.
    if (destinationId && stayList) {
      function distanceFor(input) {
        try { return JSON.parse(input.dataset.distances || "{}")[destinationId]; }
        catch (e) { return undefined; }
      }
      hotelInputs
        .map(function (input) { return input.closest(".hotel-card"); })
        .filter(function (card) { return !card.classList.contains("hidden"); })
        .sort(function (a, b) {
          var da = distanceFor(a.querySelector("input"));
          var db = distanceFor(b.querySelector("input"));
          return (da == null ? 999 : da) - (db == null ? 999 : db);
        })
        .forEach(function (card) { stayList.appendChild(card); });
    }

    updateTotal();
  }

  function updateTotal() {
    var hotel = selectedHotel();
    if (!hotel) {
      totalBox.classList.remove("is-visible");
      return;
    }

    var countNights = Math.max(1, numberValue(nights, 1));
    var countRooms = Math.max(1, numberValue(rooms, 1));
    var price = parseFloat(hotel.dataset.price || "0");
    var total = price * countNights * countRooms;
    totalText.textContent = "₱" + total.toLocaleString("en-PH", { maximumFractionDigits: 0 });
    totalBox.classList.add("is-visible");

    if (checkIn.value) {
      // PARSE RANGE VALUE
      var startDateStr = checkIn.value.includes(' to ') ? checkIn.value.split(' to ')[0] : checkIn.value;
      var date = new Date(startDateStr + "T00:00:00");
     
      date.setDate(date.getDate() + countNights);
      var pad = function (n) { return String(n).padStart(2, "0"); };
      checkoutText.textContent = date.getFullYear() + "-" + pad(date.getMonth() + 1) + "-" + pad(date.getDate());
    } else {
      checkoutText.textContent = "Choose a check-in date";
    }
  }

  function updatePayment() {
    var value = payment.value;
    gcashPanel.classList.toggle("is-visible", value === "GCash");
    cardPanel.classList.toggle("is-visible", value === "Credit / Debit Card");
  }

  form.addEventListener("click", function (event) {
    var button = event.target.closest("[data-stepper]");
    if (!button) return;

    var target = form.querySelector("[name='" + button.dataset.stepper + "']");
    var delta = button.dataset.delta === "1" ? 1 : -1;
    var min = parseInt(target.min || "1", 10);
    var max = parseInt(target.max || "99", 10);
    target.value = Math.max(min, Math.min(max, numberValue(target, min) + delta));
    updateTotal();
  });

  destination.addEventListener("change", updateHotels);
  checkIn.addEventListener("change", updateTotal);
  nights.addEventListener("change", updateTotal);
  rooms.addEventListener("change", updateTotal);
  guests.addEventListener("change", updateTotal);
  payment.addEventListener("change", updatePayment);
  hotelInputs.forEach(function (input) {
    input.addEventListener("change", updateTotal);
  });

  updateHotels();
  updatePayment();

  // FLATPICKR LOGIC & API CHECK
  var alertBox = document.getElementById("booking-alert");
  
  var fp = flatpickr("#date-picker", {
    mode: "range",
    dateFormat: "Y-m-d",
    minDate: "today",
    onChange: function(selectedDates) {
      alertBox.style.display = "none"; // Hide alert if user changes dates
      if (selectedDates.length === 2) {
        var diffTime = Math.abs(selectedDates[1] - selectedDates[0]);
        var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        nights.value = diffDays;
        updateTotal();
      }
    }
  });

  async function fetchAvailability() {
    var hotel = selectedHotel();
    if (!hotel || !destination.value) return;
    
    var destId = destination.value;
    var hotelId = hotel.value;

    try {
      var response = await fetch(`/visit-albay/actions/get_availability.php?hotel_id=${hotelId}&destination_id=${destId}`);
      var takenRanges = await response.json();
      fp.set('disable', takenRanges);
      
      // Check if current selection violates new bounds
      if (checkIn.value) {
          var startDateStr = checkIn.value.includes(' to ') ? checkIn.value.split(' to ')[0] : checkIn.value;
          var isTaken = takenRanges.some(range => startDateStr >= range.from && startDateStr <= range.to);
          if (isTaken) {
              alertBox.style.display = "block";
              fp.clear();
          }
      }
    } catch (e) {
      console.error("Could not fetch availability", e);
    }
  }

  destination.addEventListener("change", fetchAvailability);
  hotelInputs.forEach(function (input) {
    input.addEventListener("change", fetchAvailability);
  });

})();
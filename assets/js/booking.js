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
  var peakNote = document.querySelector("[data-peak-note]");
  var seasons = window.VA_PEAK_SEASONS || [];

  function peakPct(value) {
    if (!value) return 0;
    for (var i = 0; i < seasons.length; i++) {
      if (value >= seasons[i].start && value <= seasons[i].end) return seasons[i].pct;
    }
    return 0;
  }

  function updateHotels() {
    var destinationId = destination.value;
    hotelInputs.forEach(function (input) {
      var card = input.closest(".hotel-card");
      var serves = (input.dataset.serves || "").split(",");
      var available = !destinationId || serves.indexOf(destinationId) !== -1;
      card.classList.toggle("hidden", !available);
      if (!available && input.checked) input.checked = false;
    });

    
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
    
    var total = 0;
    var hasPeak = false;

    
    if (checkIn.value) {
      var startDateStr = checkIn.value.split(" to ")[0];
      var startDate = new Date(startDateStr + "T00:00:00");
      
      for (var i = 0; i < countNights; i++) {
        var currentDate = new Date(startDate);
        currentDate.setDate(currentDate.getDate() + i);
        
        var pad = function (n) { return String(n).padStart(2, "0"); };
        var dateStr = currentDate.getFullYear() + "-" + pad(currentDate.getMonth() + 1) + "-" + pad(currentDate.getDate());
        
        var pct = peakPct(dateStr);
        if (pct > 0) hasPeak = true; 
        
        total += price * countRooms * (1 + pct / 100);
      }
    } else {
      total = price * countNights * countRooms; 
    }

    totalText.textContent = "₱" + total.toLocaleString("en-PH", { maximumFractionDigits: 0 });
    totalBox.classList.add("is-visible");

    if (peakNote) {
      peakNote.textContent = hasPeak ? "Peak season rate applied to applicable dates" : "";
      peakNote.hidden = !hasPeak;
    }

    if (checkIn.value) {
      var date = new Date(checkIn.value.split(" to ")[0] + "T00:00:00");
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




  var phoneInput = form.querySelector("[name='phone']");
  if (phoneInput) {
    phoneInput.addEventListener("input", function () {
      var val = this.value;
      
      var cleaned = val.replace(/[^\+0-9]/g, '');
      
      if (cleaned.indexOf('+') > 0) {
        cleaned = cleaned.replace(/\+/g, function(match, offset) {
          return offset === 0 ? '+' : '';
        });
      }
      
      if (val !== cleaned) {
        this.value = cleaned;
        this.setCustomValidity("Please input numbers and an optional starting '+' only.");
        this.reportValidity();
      } else {
        this.setCustomValidity("");
      }
    });
  }

  if (nights) {
    nights.addEventListener("input", function () {
      var typedNights = parseInt(this.value, 10);
      if (typedNights > 30) {
        this.value = 30;
        this.setCustomValidity("You must input 30 nights or less.");
        this.reportValidity();
        updateTotal();
      } else {
        this.setCustomValidity("");
      }
    });
  }
  // Validation 



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
})();

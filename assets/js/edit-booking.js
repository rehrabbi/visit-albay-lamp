// Live "updated total" preview for the My Bookings edit form.
// Mirrors the server-side math (price_per_night x nights x rooms, plus any
// peak-season surcharge tied to the check-in date) so the user sees the new
// price before submitting a change for approval.
(function () {
  function peso(amount) {
    return "₱" + Math.round(amount).toLocaleString("en-US");
  }

  var seasons = window.VA_PEAK_SEASONS || [];

  function peakPct(checkIn) {
    if (!checkIn) {
      return 0;
    }
    for (var i = 0; i < seasons.length; i++) {
      if (checkIn >= seasons[i].start && checkIn <= seasons[i].end) {
        return seasons[i].pct;
      }
    }
    return 0;
  }

  document.querySelectorAll(".edit-form").forEach(function (form) {
    var hotel = form.querySelector('select[name="hotel_id"]');
    var nights = form.querySelector('input[name="nights"]');
    var rooms = form.querySelector('input[name="rooms"]');
    var checkIn = form.querySelector('input[name="check_in_date"]');
    var box = form.querySelector("[data-edit-total]");
    if (!hotel || !nights || !rooms || !box) {
      return;
    }

    var newAmount = box.querySelector(".edit-total-new");
    var peakNote = box.querySelector(".edit-total-peak");
    var original = parseFloat(box.getAttribute("data-original-total")) || 0;
    var dest = form.querySelector('select[name="destination_id"]');

    // Snapshot the full hotel list so we can re-filter it per destination.
    var allHotels = Array.prototype.map.call(hotel.options, function (o) {
      return {
        value: o.value,
        price: o.dataset.price,
        serves: (o.dataset.serves || "").split(",").filter(Boolean),
        distances: JSON.parse(o.dataset.distances || "{}"),
        label: o.dataset.label || o.textContent.trim()
      };
    });

    // Show only the stays that serve the chosen destination, nearest first.
    function rebuildHotels() {
      if (!dest) return;
      var destId = dest.value;
      var prev = hotel.value;
      var avail = allHotels.filter(function (o) {
        return !destId || o.serves.indexOf(destId) !== -1;
      });
      avail.sort(function (a, b) {
        var da = a.distances[destId], db = b.distances[destId];
        return (da == null ? 999 : da) - (db == null ? 999 : db);
      });
      hotel.innerHTML = "";
      avail.forEach(function (o) {
        var opt = document.createElement("option");
        opt.value = o.value;
        opt.dataset.price = o.price;
        var km = o.distances[destId];
        opt.textContent = o.label + (km != null ? "  ·  " + km + " km away" : "");
        hotel.appendChild(opt);
      });
      if (avail.some(function (o) { return o.value === prev; })) hotel.value = prev;
      else if (hotel.options.length) hotel.selectedIndex = 0;
      // Fire change so the total recomputes and the calendar refreshes booked dates.
      hotel.dispatchEvent(new Event("change", { bubbles: true }));
    }

    function update() {
      var option = hotel.options[hotel.selectedIndex];
      var price = parseFloat(option && option.getAttribute("data-price")) || 0;
      var n = Math.max(1, parseInt(nights.value, 10) || 1);
      var r = Math.max(1, parseInt(rooms.value, 10) || 1);
      var pct = checkIn ? peakPct(checkIn.value) : 0;
      var total = price * n * r * (1 + pct / 100);

      newAmount.textContent = peso(total);
      box.classList.toggle("is-changed", Math.round(total) !== Math.round(original));

      if (peakNote) {
        peakNote.textContent = pct > 0 ? "incl. peak +" + pct + "%" : "";
        peakNote.hidden = pct === 0;
      }
    }

    hotel.addEventListener("change", update);
    nights.addEventListener("input", update);
    rooms.addEventListener("input", update);
    if (checkIn) {
      checkIn.addEventListener("input", update);
    }
    if (dest) {
      dest.addEventListener("change", rebuildHotels);
      rebuildHotels();
    } else {
      update();
    }
  });
})();

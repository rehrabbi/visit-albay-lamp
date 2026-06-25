// Custom inline date picker for check-in fields. Progressive enhancement:
// the native <input type="date"> stays in the DOM (it carries the value and
// still validates "required"); a styled calendar drives it. Peak-season days
// are shaded and a season badge appears once a date is chosen. Fully offline.
(function () {
  var MONTHS = ["January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"];
  var WEEKDAYS = ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"];
  var seasons = window.VA_PEAK_SEASONS || [];

  function peakPct(iso) {
    for (var i = 0; i < seasons.length; i++) {
      if (iso >= seasons[i].start && iso <= seasons[i].end) return seasons[i].pct;
    }
    return 0;
  }
  function pad(n) { return String(n).padStart(2, "0"); }
  function toISO(y, m, d) { return y + "-" + pad(m + 1) + "-" + pad(d); }
  function todayISO() { var t = new Date(); return toISO(t.getFullYear(), t.getMonth(), t.getDate()); }
  function friendly(iso) {
    var p = iso.split("-");
    var dt = new Date(+p[0], +p[1] - 1, +p[2]);
    return dt.toLocaleDateString("en-US", { weekday: "short", month: "short", day: "numeric", year: "numeric" });
  }

  var CAL_ICON = '<svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true">' +
    '<rect x="3" y="4.5" width="18" height="16" rx="2.5" fill="none" stroke="currentColor" stroke-width="1.8"/>' +
    '<path d="M3 9h18M8 2.5v4M16 2.5v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';

  document.querySelectorAll("input[data-calendar]").forEach(setup);

  function setup(input) {
    var min = todayISO();
    var state = { view: null, selected: input.value || "" };
    var bookedRanges = [];
    var form = input.closest("form");

    function isBooked(iso) {
      for (var i = 0; i < bookedRanges.length; i++) {
        if (iso >= bookedRanges[i].from && iso < bookedRanges[i].to) return true;
      }
      return false;
    }

    function hotelValue() {
      var sel = form && form.querySelector('select[name="hotel_id"]');
      if (sel) return sel.value;
      var radio = form && form.querySelector('input[name="hotel_id"]:checked');
      return radio ? radio.value : "";
    }
    function destValue() {
      var d = form && form.querySelector('[name="destination_id"]');
      return d ? d.value : "";
    }

    function fetchAvailability() {
      var h = hotelValue(), d = destValue();
      if (!h || !d) { bookedRanges = []; if (!pop.hidden) render(); return; }
      var url = new URL("actions/get_availability.php", window.location.href);
      url.searchParams.set("hotel_id", h);
      url.searchParams.set("destination_id", d);
      var ex = form && form.querySelector('input[name="booking_id"]');
      if (ex && ex.value) url.searchParams.set("exclude_booking_id", ex.value);
      fetch(url.toString(), { credentials: "same-origin" })
        .then(function (r) { return r.json(); })
        .then(function (ranges) { bookedRanges = Array.isArray(ranges) ? ranges : []; if (!pop.hidden) render(); })
        .catch(function () { bookedRanges = []; });
    }

    input.classList.add("calendar-native");
    // The native input is now visually hidden; drive "required" via a submit
    // guard (below) so the user gets a clear prompt instead of a silent block.
    input.removeAttribute("required");

    var wrap = document.createElement("div");
    wrap.className = "calendar";
    input.parentNode.insertBefore(wrap, input);
    wrap.appendChild(input);

    var trigger = document.createElement("button");
    trigger.type = "button";
    trigger.className = "calendar-trigger";
    trigger.innerHTML = CAL_ICON + '<span class="calendar-value"></span>';
    wrap.appendChild(trigger);

    var badge = document.createElement("span");
    badge.className = "season-badge";
    badge.hidden = true;
    wrap.appendChild(badge);

    var pop = document.createElement("div");
    pop.className = "calendar-popup";
    pop.hidden = true;
    wrap.appendChild(pop);

    function render() {
      if (!state.view) state.view = (state.selected || min).slice(0, 7);
      var y = +state.view.slice(0, 4), m = +state.view.slice(5, 7) - 1;
      var startDay = new Date(y, m, 1).getDay();
      var days = new Date(y, m + 1, 0).getDate();
      var canPrev = state.view > min.slice(0, 7);

      var html = '<div class="calendar-head">' +
        '<button type="button" class="cal-nav" data-nav="-1" aria-label="Previous month"' + (canPrev ? '' : ' disabled') + '>&lsaquo;</button>' +
        '<span class="cal-title">' + MONTHS[m] + ' ' + y + '</span>' +
        '<button type="button" class="cal-nav" data-nav="1" aria-label="Next month">&rsaquo;</button></div>';
      html += '<div class="calendar-weekdays">' + WEEKDAYS.map(function (w) { return '<span>' + w + '</span>'; }).join('') + '</div>';
      html += '<div class="calendar-grid">';
      for (var i = 0; i < startDay; i++) html += '<span class="cal-empty"></span>';
      for (var d = 1; d <= days; d++) {
        var iso = toISO(y, m, d);
        var booked = isBooked(iso);
        var disabled = iso < min || booked;
        var cls = "cal-day";
        if (disabled) cls += " is-disabled";
        if (booked) cls += " is-booked";
        if (iso === state.selected) cls += " is-selected";
        if (iso === min) cls += " is-today";
        if (peakPct(iso) > 0) cls += " is-peak";
        html += '<button type="button" class="' + cls + '" data-iso="' + iso + '"' +
          (disabled ? ' disabled' : '') + (booked ? ' title="Already booked"' : '') + '>' +
          d + (peakPct(iso) > 0 ? '<span class="peak-dot" aria-hidden="true"></span>' : '') + '</button>';
      }
      html += '</div><div class="calendar-legend"><span class="legend-dot"></span> Peak (+15%)' +
        '<span class="legend-dot legend-booked"></span> Booked</div>';
      pop.innerHTML = html;
    }

    function updateDisplay() {
      var val = state.selected;
      wrap.querySelector(".calendar-value").textContent = val ? friendly(val) : "Select a date";
      trigger.classList.toggle("is-empty", !val);
      if (!val) { badge.hidden = true; return; }
      badge.hidden = false;
      if (peakPct(val) > 0) {
        badge.textContent = "Peak season +" + peakPct(val) + "%";
        badge.className = "season-badge is-peak";
      } else {
        badge.textContent = "Regular season";
        badge.className = "season-badge is-regular";
      }
    }

    function open() { render(); pop.hidden = false; wrap.classList.add("is-open"); }
    function close() { pop.hidden = true; wrap.classList.remove("is-open"); }

    trigger.addEventListener("click", function (e) {
      e.preventDefault();
      pop.hidden ? open() : close();
    });

    pop.addEventListener("click", function (e) {
      // Keep clicks inside the popup from reaching the outside-click handler,
      // which would otherwise close the calendar when navigating months
      // (re-rendering detaches the clicked arrow from the DOM).
      e.stopPropagation();
      var nav = e.target.closest("[data-nav]");
      if (nav && !nav.disabled) {
        var y = +state.view.slice(0, 4), m = +state.view.slice(5, 7) - 1 + (+nav.dataset.nav);
        var nd = new Date(y, m, 1);
        state.view = nd.getFullYear() + "-" + pad(nd.getMonth() + 1);
        render();
        return;
      }
      var day = e.target.closest(".cal-day");
      if (day && !day.disabled) {
        state.selected = day.dataset.iso;
        input.value = state.selected;
        wrap.classList.remove("has-error");
        input.dispatchEvent(new Event("input", { bubbles: true }));
        input.dispatchEvent(new Event("change", { bubbles: true }));
        updateDisplay();
        close();
      }
    });

    document.addEventListener("click", function (e) {
      if (!wrap.contains(e.target)) close();
    });

    // Block submit (before validation.js disables the button) if no date is set.
    input.__openCalendar = open;
    if (form && !form.__calGuarded) {
      form.__calGuarded = true;
      form.addEventListener("submit", function (e) {
        var fields = form.querySelectorAll("input[data-calendar]");
        for (var i = 0; i < fields.length; i++) {
          if (!fields[i].value) {
            e.preventDefault();
            e.stopPropagation();
            var c = fields[i].closest(".calendar");
            if (c) c.classList.add("has-error");
            if (fields[i].__openCalendar) fields[i].__openCalendar();
            break;
          }
        }
      }, true);
    }

    // Refresh booked dates whenever the chosen stay or destination changes.
    if (form) {
      var destEl = form.querySelector('[name="destination_id"]');
      if (destEl) destEl.addEventListener("change", fetchAvailability);
      form.querySelectorAll('[name="hotel_id"]').forEach(function (el) {
        el.addEventListener("change", fetchAvailability);
      });
      fetchAvailability();
    }

    updateDisplay();
  }
})();

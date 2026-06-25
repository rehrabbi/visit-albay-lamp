(function () {
  function showTab(name) {
    document.querySelectorAll("[data-tab-target]").forEach(function (button) {
      button.classList.toggle("is-active", button.dataset.tabTarget === name);
    });

    document.querySelectorAll("[data-tab-panel]").forEach(function (panel) {
      panel.classList.toggle("is-active", panel.dataset.tabPanel === name);
    });
  }

  document.addEventListener("click", function (event) {
    var toggle = event.target.closest("[data-toggle-edit]");
    if (toggle) {
      var target = document.getElementById(toggle.dataset.toggleEdit);
      if (target) {
        var card = target.closest(".booking-card");
        var willOpen = !target.classList.contains("is-open");
        if (card) {
          // One request form at a time: close any open form in this card and
          // hide the action buttons so only the chosen field shows.
          card.querySelectorAll(".edit-form.is-open").forEach(function (f) {
            f.classList.remove("is-open");
          });
          var actions = card.querySelector(".card-actions");
          if (willOpen) {
            target.classList.add("is-open");
            if (actions) actions.classList.add("is-hidden");
          } else if (actions) {
            actions.classList.remove("is-hidden");
          }
        } else {
          target.classList.toggle("is-open");
        }
      }
    }

    var tab = event.target.closest("[data-tab-target]");
    if (tab) {
      showTab(tab.dataset.tabTarget);
    }
  });

  // Keep the admin on the same tab after a server redirect (?tab=...).
  var initialTab = new URLSearchParams(window.location.search).get("tab");
  if (initialTab && document.querySelector('[data-tab-target="' + initialTab + '"]')) {
    showTab(initialTab);
  }

  // Destinations category filter (client-side show/hide).
  var filterBar = document.querySelector("[data-filter-bar]");
  if (filterBar) {
    filterBar.addEventListener("click", function (event) {
      var chip = event.target.closest("[data-filter]");
      if (!chip) return;
      var category = chip.dataset.filter;
      filterBar.querySelectorAll("[data-filter]").forEach(function (c) {
        c.classList.toggle("is-active", c === chip);
      });
      document.querySelectorAll("[data-category]").forEach(function (card) {
        card.style.display = (category === "all" || card.dataset.category === category) ? "" : "none";
      });
    });
  }

  // Transparent hero header (home) turns solid once the page is scrolled.
  var heroHeader = document.querySelector(".site-header-hero");
  if (heroHeader) {
    var onHeroScroll = function () {
      heroHeader.classList.toggle("is-solid", window.scrollY > 60);
    };
    onHeroScroll();
    window.addEventListener("scroll", onHeroScroll, { passive: true });
  }

  // Trailing cursor (pointer devices only).
  if (window.matchMedia && window.matchMedia("(hover:hover) and (pointer:fine)").matches) {
    var cursor = document.createElement("div");
    cursor.id = "albay-cursor";
    document.body.appendChild(cursor);
    var cursorHidden = true;
    window.addEventListener("mousemove", function (e) {
      cursor.style.left = e.clientX + "px";
      cursor.style.top = e.clientY + "px";
      if (cursorHidden) { cursor.style.transform = "translate(-50%,-50%) scale(1)"; cursorHidden = false; }
    });
    document.addEventListener("mouseleave", function () {
      cursor.style.transform = "translate(-50%,-50%) scale(0)";
      cursorHidden = true;
    });
  }

  // Scroll-reveal: fade below-the-fold blocks up as they enter the viewport.
  if ("IntersectionObserver" in window) {
    var io = new IntersectionObserver(function (entries, obs) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) { entry.target.classList.add("in-view"); obs.unobserve(entry.target); }
      });
    }, { threshold: 0.08 });
    document.querySelectorAll("main .section, main .rail, main .exp-bands").forEach(function (el) {
      if (el.getBoundingClientRect().top > window.innerHeight * 0.85) {
        el.classList.add("reveal");
        io.observe(el);
      }
    });
  }

  // Subtle 3D tilt on cards.
  document.querySelectorAll(".tile, .dest-card").forEach(function (card) {
    card.addEventListener("mousemove", function (e) {
      var rect = card.getBoundingClientRect();
      var px = (e.clientX - rect.left) / rect.width - 0.5;
      var py = (e.clientY - rect.top) / rect.height - 0.5;
      card.style.transform = "perspective(800px) rotateX(" + (-py * 5).toFixed(2) + "deg) rotateY(" + (px * 5).toFixed(2) + "deg)";
    });
    card.addEventListener("mouseleave", function () { card.style.transform = ""; });
  });
})();

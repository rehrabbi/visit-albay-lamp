// Destination detail modal: opened by any [data-dest="slug"] trigger or window.openDestModal(slug).
(function () {
  var modal = document.getElementById('dest-modal');
  var data = window.ALBAY_DEST || {};
  if (!modal) {
    return;
  }

  var base = window.location.pathname.replace(/\/[^\/]*$/, '/');

  function esc(s) {
    return String(s == null ? '' : s).replace(/[&<>]/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;' }[c];
    });
  }

  function set(id, text) {
    var el = document.getElementById(id);
    if (el) el.textContent = text;
  }

  function open(slug) {
    var d = data[slug];
    if (!d) return;
    var img = document.getElementById('modal-img');
    img.src = d.image;
    img.alt = d.name;
    set('modal-cat', d.category);
    set('modal-town', d.town);
    set('modal-name', d.name);
    set('modal-desc', d.description);
    set('modal-duration', d.duration);
    set('modal-vibe', d.vibe);
    document.getElementById('modal-highlights').innerHTML =
      (d.highlights || []).map(function (h) { return '<li>' + esc(h) + '</li>'; }).join('');
    document.getElementById('modal-acts').innerHTML =
      (d.activities || []).map(function (a) { return '<span>' + esc(a) + '</span>'; }).join('');
    document.getElementById('modal-book').href = base + 'plan.php?destination=' + d.id;
    modal.hidden = false;
    document.body.style.overflow = 'hidden';
  }

  function close() {
    modal.hidden = true;
    document.body.style.overflow = '';
  }

  window.openDestModal = open;

  document.addEventListener('click', function (e) {
    var trigger = e.target.closest('[data-dest]');
    if (trigger) {
      e.preventDefault();
      open(trigger.getAttribute('data-dest'));
      return;
    }
    if (e.target.closest('[data-modal-close]')) {
      close();
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && !modal.hidden) close();
  });
})();

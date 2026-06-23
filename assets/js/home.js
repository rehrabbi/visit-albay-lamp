// Home page: interactive Leaflet map of Albay's signature destinations.
(function () {
  var el = document.getElementById('albay-map');
  if (!el || typeof L === 'undefined') {
    return;
  }

  // Resolve Leaflet's default marker icons from the local bundled copy.
  L.Icon.Default.imagePath = window.location.pathname.replace(/\/[^\/]*$/, '/') + 'assets/vendor/leaflet/images/';

  var destinations = [
    { slug: 'mayon', name: 'Mayon Volcano', lat: 13.2543, lng: 123.6857 },
    { slug: 'cagsawa', name: 'Cagsawa Ruins', lat: 13.1717, lng: 123.6736 },
    { slug: 'lignon', name: 'Lignon Hill', lat: 13.1408, lng: 123.7356 },
    { slug: 'daraga', name: 'Daraga Church', lat: 13.1578, lng: 123.6836 },
    { slug: 'sumlang', name: 'Sumlang Lake', lat: 13.2203, lng: 123.6636 },
    { slug: 'quitinday', name: 'Quitinday Green Hills', lat: 13.1994, lng: 123.5964 }
  ];

  var map = L.map('albay-map', { scrollWheelZoom: false }).setView([13.185, 123.655], 12);

  L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
    maxZoom: 19
  }).addTo(map);

  function pinIcon(name) {
    return L.divIcon({
      className: '',
      html: '<div style="transform:translate(-50%,calc(-100% - 8px));background:#cc7a33;color:#fff;border-radius:8px;padding:7px 13px;font-size:12px;font-weight:700;font-family:\'Hanken Grotesk\',sans-serif;white-space:nowrap;box-shadow:0 4px 16px rgba(0,0,0,.4);position:relative;cursor:pointer;line-height:1.3;display:inline-block;">' + name + '<span style="position:absolute;bottom:-8px;left:50%;transform:translateX(-50%);width:0;height:0;border-left:7px solid transparent;border-right:7px solid transparent;border-top:9px solid #cc7a33;display:block;"></span></div>',
      iconSize: [0, 0],
      iconAnchor: [0, 0]
    });
  }

  destinations.forEach(function (d) {
    var marker = L.marker([d.lat, d.lng], { icon: pinIcon(d.name) }).addTo(map);
    marker.on('click', function () {
      if (typeof window.openDestModal === 'function') {
        window.openDestModal(d.slug);
      }
    });
  });
})();

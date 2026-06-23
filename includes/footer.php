<footer class="footer-dark">
  <div class="footer-cols">
    <div class="footer-brand">
      <strong style="display:inline-flex;align-items:center;gap:9px;">
        <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path d="M10.9 5.6a1.3 1.3 0 0 1 2.2 0l7.2 13.4a1 1 0 0 1-.9 1.5H4.6a1 1 0 0 1-.9-1.5Z" fill="#cc7a33"/></svg>Visit Albay
      </strong>
      <p>The travel guide to Albay province, Bicol Region, Philippines &mdash; home of the world's most perfect volcanic cone.</p>
    </div>
    <nav class="footer-col" aria-label="Explore">
      <h4>Explore</h4>
      <a href="<?= h(url('destinations.php')) ?>">Destinations</a>
      <a href="<?= h(url('experiences.php')) ?>">Experiences</a>
      <a href="<?= h(url('plan.php')) ?>">Plan a visit</a>
    </nav>
    <div class="footer-col">
      <h4>Region</h4>
      <span>Legazpi</span>
      <span>Daraga</span>
      <span>Camalig</span>
    </div>
    <div class="footer-col">
      <h4>Best season</h4>
      <span>Nov &ndash; Apr</span>
      <span>Dry &amp; clear skies</span>
    </div>
  </div>
  <div class="footer-bottom">
    <span>&copy; 2026 Visit Albay &middot; Bicol Region, Philippines</span>
    <span>Photography &middot; Wikimedia Commons</span>
  </div>
</footer>
<script src="<?= h(url('assets/js/main.js')) ?>?v=<?= filemtime(__DIR__ . '/../assets/js/main.js') ?>"></script>
<?= $extraScripts ?? '' ?>
</body>
</html>

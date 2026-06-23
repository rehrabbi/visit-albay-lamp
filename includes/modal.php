<?php
// Shared destination detail modal. Expects $destinations (array of rows) in scope.
// Builds a slug-keyed dataset for the client and renders the (hidden) dialog shell.
$modalData = [];
foreach ($destinations as $d) {
    $modalData[$d['slug']] = [
        'id' => (int) $d['id'],
        'name' => $d['name'],
        'town' => $d['town'],
        'category' => $d['category'],
        'image' => $d['image_url'],
        'description' => $d['description'],
        'duration' => $d['duration'],
        'vibe' => $d['vibe'],
        'highlights' => json_list($d['highlights']),
        'activities' => json_list($d['activities']),
    ];
}
?>
<div class="modal-overlay" id="dest-modal" hidden>
  <div class="modal-backdrop" data-modal-close></div>
  <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="modal-name">
    <div class="modal-media">
      <img id="modal-img" src="" alt="">
      <button class="modal-close" type="button" data-modal-close aria-label="Close">&times;</button>
      <span class="modal-cat" id="modal-cat"></span>
      <div class="modal-media-text">
        <div class="modal-town" id="modal-town"></div>
        <h2 id="modal-name"></h2>
      </div>
    </div>
    <div class="modal-body">
      <p id="modal-desc"></p>
      <div class="modal-facts">
        <div><div class="k">Visit duration</div><div class="v" id="modal-duration"></div></div>
        <div><div class="k">Vibe</div><div class="v" id="modal-vibe"></div></div>
      </div>
      <div class="modal-label">Highlights</div>
      <ul id="modal-highlights"></ul>
      <div class="modal-label">Activities</div>
      <div class="modal-acts" id="modal-acts"></div>
      <div class="modal-actions">
        <a class="button button-primary" id="modal-book" href="<?= h(url('plan.php')) ?>">Book this trip &rarr;</a>
        <button class="button button-ghost" type="button" data-modal-close>Close</button>
      </div>
    </div>
  </div>
</div>
<script>window.ALBAY_DEST = <?= json_encode($modalData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;</script>

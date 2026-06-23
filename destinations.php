<?php
require_once __DIR__ . '/includes/app.php';

$pageTitle = 'Destinations - Visit Albay';
$active = 'destinations';
$destinations = all_destinations($pdo);
$validCats = ['Nature', 'Heritage', 'Adventure'];
$activeCat = (isset($_GET['category']) && in_array($_GET['category'], $validCats, true)) ? $_GET['category'] : 'all';

$extraScripts = '<script src="' . h(url('assets/js/modal.js')) . '?v=' . filemtime(__DIR__ . '/assets/js/modal.js') . '"></script>';

require __DIR__ . '/includes/header.php';
?>
<main class="section">
  <div class="page-head">
    <p class="eyebrow">Explore</p>
    <h1>Top destinations in Albay</h1>
    <p class="lead">From an active volcano to centuries-old ruins and quiet lakes &mdash; each spot has its own highlights, suggested activities, and the kind of traveler it suits best. Pick a favorite and book your visit.</p>
  </div>

  <div class="stat-strip">
    <div><div class="n">6</div><div class="l">Signature destinations</div></div>
    <div><div class="n">3</div><div class="l">Ways to experience Albay</div></div>
    <div><div class="n">1</div><div class="l">Perfect volcanic cone</div></div>
  </div>

  <div class="filter-bar" data-filter-bar>
    <span class="label">Filter</span>
    <button type="button" class="filter-chip<?= $activeCat === 'all' ? ' is-active' : '' ?>" data-filter="all">All</button>
    <button type="button" class="filter-chip<?= $activeCat === 'Nature' ? ' is-active' : '' ?>" data-filter="Nature">Nature</button>
    <button type="button" class="filter-chip<?= $activeCat === 'Heritage' ? ' is-active' : '' ?>" data-filter="Heritage">Heritage</button>
    <button type="button" class="filter-chip<?= $activeCat === 'Adventure' ? ' is-active' : '' ?>" data-filter="Adventure">Adventure</button>
  </div>

  <div class="dest-grid">
    <?php foreach ($destinations as $d): ?>
      <article class="dest-card" data-category="<?= h($d['category']) ?>"<?= ($activeCat !== 'all' && $d['category'] !== $activeCat) ? ' style="display:none"' : '' ?>>
        <div class="dest-media">
          <img src="<?= h($d['image_url']) ?>" alt="<?= h($d['name']) ?>">
          <span class="town"><?= h($d['town']) ?></span>
          <span class="cat"><?= h($d['category']) ?></span>
        </div>
        <div class="body">
          <h3><?= h($d['name']) ?></h3>
          <p class="tagline"><?= h($d['tagline']) ?></p>
          <p class="desc"><?= h($d['description']) ?></p>
          <div class="dest-facts">
            <div><div class="k">Visit</div><div class="v"><?= h($d['duration']) ?></div></div>
            <div><div class="k">Vibe</div><div class="v"><?= h($d['vibe']) ?></div></div>
          </div>
          <div class="dest-tags">
            <?php foreach (json_list($d['best_for']) as $tag): ?>
              <span><?= h($tag) ?></span>
            <?php endforeach; ?>
          </div>
          <div class="dest-actions">
            <a class="dest-book" href="<?= h(url('plan.php?destination=' . (int) $d['id'])) ?>">Book this trip <span aria-hidden="true">&rarr;</span></a>
            <button type="button" class="dest-more" data-dest="<?= h($d['slug']) ?>">More about this place</button>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</main>
<?php require __DIR__ . '/includes/modal.php'; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>

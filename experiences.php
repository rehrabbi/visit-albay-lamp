<?php
require_once __DIR__ . '/includes/app.php';

$pageTitle = 'Experience - Visit Albay';
$active = 'experience';
$destinations = all_destinations($pdo);

$bySlug = [];
foreach ($destinations as $d) {
    $bySlug[$d['slug']] = $d;
}

$bands = [
    [
        'n' => '01', 'title' => 'Nature', 'cat' => 'Nature', 'img' => $bySlug['sumlang']['image_url'] ?? '',
        'blurb' => 'Lakes, reflections, and the perfect cone &mdash; slow, scenic days that are easy on the eyes.',
        'places' => ['Mayon Volcano', 'Sumlang Lake'],
    ],
    [
        'n' => '02', 'title' => 'Heritage', 'cat' => 'Heritage', 'img' => $bySlug['cagsawa']['image_url'] ?? '',
        'blurb' => 'Volcanic-stone churches and a belfry that outlived an eruption. History you can walk through.',
        'places' => ['Cagsawa Ruins', 'Daraga Church'],
    ],
    [
        'n' => '03', 'title' => 'Adventure', 'cat' => 'Adventure', 'img' => $bySlug['quitinday']['image_url'] ?? '',
        'blurb' => 'Ziplines, viewdecks, and rolling green hills &mdash; for travelers who like to climb for the view.',
        'places' => ['Lignon Hill', 'Quitinday Green Hills'],
    ],
];

$weather = [
    ['mo' => 'Jan', 'sky' => 'Clear', 'temp' => '26°C', 'peak' => true, 'note' => 'Peak season'],
    ['mo' => 'Feb', 'sky' => 'Sunny', 'temp' => '27°C', 'peak' => true, 'note' => 'Peak season'],
    ['mo' => 'Mar', 'sky' => 'Sunny', 'temp' => '29°C', 'peak' => true, 'note' => 'Dry season'],
    ['mo' => 'Apr', 'sky' => 'Hot', 'temp' => '31°C', 'peak' => true, 'note' => 'Holy Week'],
    ['mo' => 'May', 'sky' => 'Mixed', 'temp' => '30°C', 'peak' => true, 'note' => 'Magayon Festival'],
    ['mo' => 'Jun', 'sky' => 'Rainy', 'temp' => '28°C', 'peak' => false, 'note' => 'Wet season starts'],
    ['mo' => 'Jul', 'sky' => 'Storms', 'temp' => '27°C', 'peak' => false, 'note' => 'Typhoon risk'],
    ['mo' => 'Aug', 'sky' => 'Storms', 'temp' => '27°C', 'peak' => false, 'note' => 'Typhoon risk'],
    ['mo' => 'Sep', 'sky' => 'Heavy', 'temp' => '27°C', 'peak' => false, 'note' => 'Ibalong Festival'],
    ['mo' => 'Oct', 'sky' => 'Mixed', 'temp' => '27°C', 'peak' => false, 'note' => 'Shoulder'],
    ['mo' => 'Nov', 'sky' => 'Clearing', 'temp' => '27°C', 'peak' => true, 'note' => 'Good visibility'],
    ['mo' => 'Dec', 'sky' => 'Clear', 'temp' => '26°C', 'peak' => true, 'note' => 'Peak season'],
];

require __DIR__ . '/includes/header.php';
?>
<main>
  <section class="section">
    <div class="page-head">
      <p class="eyebrow">How to travel</p>
      <h1>Three ways to experience Albay</h1>
      <p class="lead">Whatever you came for, there is a side of Albay that fits. Start with the kind of day you want, then pick the destinations that match.</p>
    </div>
  </section>

  <div class="exp-bands">
    <?php foreach ($bands as $i => $b): ?>
      <div class="exp-band<?= $i % 2 === 1 ? ' reverse' : '' ?>">
        <div class="exp-media">
          <img src="<?= h($b['img']) ?>" alt="<?= h($b['title']) ?> in Albay">
          <span class="tag"><?= h($b['n']) ?> / Experience</span>
        </div>
        <div class="exp-content">
          <h2><?= h($b['title']) ?></h2>
          <p><?= $b['blurb'] ?></p>
          <div class="k">Where to go</div>
          <div class="exp-places">
            <?php foreach ($b['places'] as $place): ?><span><?= h($place) ?></span><?php endforeach; ?>
          </div>
          <a class="exp-explore" href="<?= h(url('destinations.php?category=' . $b['cat'])) ?>">Explore <?= h($b['title']) ?> <span aria-hidden="true">&rarr;</span></a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <section class="section">
    <div class="rail-head" style="margin-bottom:36px;">
      <p class="eyebrow">Best time to visit</p>
      <h2>When to plan your Albay trip</h2>
      <p class="section-intro">The dry season (November&ndash;April) offers the clearest views of Mayon. April brings the Magayon Festival &mdash; the biggest cultural event in Albay.</p>
    </div>
    <div class="weather-grid">
      <?php foreach ($weather as $w): ?>
        <div class="weather-cell<?= $w['peak'] ? ' peak' : '' ?>">
          <div class="mo"><?= h($w['mo']) ?></div>
          <div class="sky"><?= h($w['sky']) ?></div>
          <div class="temp"><?= h($w['temp']) ?></div>
          <div class="note"><?= h($w['note']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="weather-legend">
      <div><span class="sw peak"></span>Peak season &middot; Best visibility</div>
      <div><span class="sw off"></span>Shoulder / Wet season</div>
    </div>
  </section>

  <section class="section">
    <div class="closing-cta">
      <h2>Not sure where to start?</h2>
      <a class="button button-primary" href="<?= h(url('plan.php')) ?>">Plan Your Visit <span aria-hidden="true">&rarr;</span></a>
    </div>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>

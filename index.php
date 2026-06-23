<?php
require_once __DIR__ . '/includes/app.php';

$pageTitle = 'Visit Albay - Tourism Website';
$active = 'home';
$heroHeader = true; // transparent nav floating over the hero
$destinations = all_destinations($pdo);

// Traveler testimonials (static marketing content).
$reviews = [
    [
        'name' => 'Sarah Reyes', 'origin' => 'Manila', 'dest' => 'Mayon Volcano',
        'quote' => 'Nothing prepared me for the actual scale of Mayon. The cone is even more perfect in person than in any photo. We woke up at 4am for sunrise and it was worth every second.',
        'avatar' => 'https://commons.wikimedia.org/wiki/Special:FilePath/Sumlang%20Lake%20(Camalig,%20Albay;%2004-20-2023).jpg?width=200',
    ],
    [
        'name' => 'James Okoro', 'origin' => 'Cebu City', 'dest' => 'Cagsawa Ruins',
        'quote' => 'Standing by the belfry with Mayon looming behind it was one of the most cinematic moments of my life. Albay is quietly one of the best provinces in the Philippines.',
        'avatar' => 'https://commons.wikimedia.org/wiki/Special:FilePath/Mt.Mayon%20with%20Cagsawa%20Ruins,%20Albay%20by%20Say%20Bernardo.jpg?width=200',
    ],
    [
        'name' => 'Ana Villanueva', 'origin' => 'Singapore', 'dest' => 'Sumlang Lake',
        'quote' => 'We took a bamboo raft at golden hour and Mayon reflected perfectly in the still water. It felt unreal. Come in dry season, the clarity is something else.',
        'avatar' => 'https://commons.wikimedia.org/wiki/Special:FilePath/Quitinday%20Green%20Hills%20south%20view%20(Camalig,%20Albay;%2004-22-2023).jpg?width=200',
    ],
];

$extraHead = '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">';
$extraScripts = '<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>'
    . '<script src="' . h(url('assets/js/modal.js')) . '?v=' . filemtime(__DIR__ . '/assets/js/modal.js') . '"></script>'
    . '<script src="' . h(url('assets/js/home.js')) . '?v=' . filemtime(__DIR__ . '/assets/js/home.js') . '"></script>';

require __DIR__ . '/includes/header.php';
?>
<main>
  <!-- HERO -->
  <section class="hero" aria-labelledby="hero-title">
    <img src="https://commons.wikimedia.org/wiki/Special:FilePath/Mayon%20Volcano%27s%20Crater.jpg?width=2000" alt="Mayon Volcano rising over Albay">
    <div class="hero-content">
      <p class="eyebrow">Albay &middot; Bicol &middot; Philippines</p>
      <h1 id="hero-title">Find your<br>Albay.</h1>
      <p>One province, every kind of day &mdash; an active volcano, centuries-old ruins, quiet lakes, and rolling green hills.</p>
      <div class="hero-cta">
        <a class="hero-btn" href="<?= h(url('plan.php')) ?>">Plan Your Visit <span aria-hidden="true">&rarr;</span></a>
        <a class="hero-link" href="<?= h(url('destinations.php')) ?>"><span class="hero-circle" aria-hidden="true">&rarr;</span>Explore destinations</a>
      </div>
    </div>
    <div class="hero-stats">
      <div class="stat"><div class="n">2,462 m</div><div class="l">Summit</div></div>
      <div class="stat"><div class="n">6 sites</div><div class="l">Signature stops</div></div>
      <div class="stat"><div class="n">1 cone</div><div class="l">Perfectly shaped</div></div>
    </div>
    <span class="hero-scroll" aria-hidden="true">Scroll to explore</span>
  </section>

  <!-- WHY ALBAY -->
  <section class="section" aria-labelledby="why-title">
    <div class="lead-split">
      <div>
        <p class="eyebrow">Why Albay</p>
        <h2 id="why-title">Nature, history, and bold Bicolano flavor.</h2>
      </div>
      <p>Everything in Albay sits in the shadow of the volcano &mdash; and it is closer, and more varied, than you would think.</p>
    </div>
    <div class="feature-grid">
      <article class="feature">
        <div class="num">01</div>
        <h3>The perfect cone</h3>
        <p>Mayon's near-symmetrical silhouette is unlike anything else on Earth, and visible from almost everywhere in the province.</p>
      </article>
      <article class="feature">
        <div class="num">02</div>
        <h3>Living history</h3>
        <p>From the 1814 Cagsawa Ruins to the hilltop Daraga Church, Albay's past is carved into volcanic stone.</p>
      </article>
      <article class="feature">
        <div class="num">03</div>
        <h3>Bicolano flavor</h3>
        <p>Taste Bicol Express, laing, and pili nuts &mdash; bold, coconut-rich cooking born of the region.</p>
      </article>
    </div>
  </section>

  <!-- SIX SIGNATURE DESTINATIONS -->
  <section class="rail" aria-labelledby="where-title">
    <div class="rail-head">
      <p class="eyebrow">Where to go</p>
      <div class="center-head">
        <h2 id="where-title">Six signature destinations</h2>
        <a class="button button-ghost" href="<?= h(url('destinations.php')) ?>">View all</a>
      </div>
    </div>
    <div class="tile-grid">
      <?php foreach (array_slice($destinations, 0, 6) as $d): ?>
        <a class="tile" href="<?= h(url('destinations.php')) ?>" data-dest="<?= h($d['slug']) ?>">
          <img src="<?= h($d['image_url']) ?>" alt="<?= h($d['name']) ?>">
          <span class="tile-tag"><?= h($d['category']) ?></span>
          <div class="tile-body">
            <span class="tile-town"><?= h($d['town']) ?></span>
            <div class="tile-row">
              <span class="tile-name"><?= h($d['name']) ?></span>
              <span class="tile-go" aria-hidden="true">&rarr;</span>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- EXPLORE BY EXPERIENCE -->
  <section class="section section-alt" aria-labelledby="exp-title">
    <div class="rail-head" style="margin-bottom:50px;">
      <p class="eyebrow">Find your trip</p>
      <h2 id="exp-title">Explore Albay by experience</h2>
    </div>
    <div class="tile-grid" style="padding:0;max-width:1120px;">
      <a class="tile" href="<?= h(url('experiences.php')) ?>">
        <img src="https://commons.wikimedia.org/wiki/Special:FilePath/Sumlang%20Lake%20(Camalig,%20Albay;%2004-20-2023).jpg?width=900" alt="Lakeside nature in Albay">
        <div class="tile-text">
          <h3>Nature</h3>
          <p>Lakes, reflections, and the perfect cone.</p>
          <span class="mono">Mayon &middot; Sumlang &rarr;</span>
        </div>
      </a>
      <a class="tile" href="<?= h(url('experiences.php')) ?>">
        <img src="https://commons.wikimedia.org/wiki/Special:FilePath/Mt.Mayon%20with%20Cagsawa%20Ruins,%20Albay%20by%20Say%20Bernardo.jpg?width=900" alt="Heritage ruins in Albay">
        <div class="tile-text">
          <h3>Heritage</h3>
          <p>Volcanic-stone churches and a belfry that outlived an eruption.</p>
          <span class="mono">Cagsawa &middot; Daraga &rarr;</span>
        </div>
      </a>
      <a class="tile" href="<?= h(url('experiences.php')) ?>">
        <img src="https://commons.wikimedia.org/wiki/Special:FilePath/Quitinday%20Green%20Hills%20south%20view%20(Camalig,%20Albay;%2004-22-2023).jpg?width=900" alt="Green hills adventure in Albay">
        <div class="tile-text">
          <h3>Adventure</h3>
          <p>Ziplines, viewdecks, and rolling green hills.</p>
          <span class="mono">Lignon &middot; Quitinday &rarr;</span>
        </div>
      </a>
    </div>
  </section>

  <!-- FLAVORS OF BICOL -->
  <section class="section" aria-labelledby="food-title">
    <div class="food-split">
      <div>
        <p class="eyebrow">Flavors of Bicol</p>
        <h2 id="food-title">Bold, spicy, and coconut-rich.</h2>
        <p class="intro">Bicolano cuisine is built on three pillars: coconut milk, fiery siling labuyo, and local produce. Every meal in Albay is as distinctive as the landscape.</p>
        <a class="link-mono" href="<?= h(url('destinations.php')) ?>">Explore the region &rarr;</a>
      </div>
      <div class="food-grid">
        <article class="food-card">
          <img src="https://commons.wikimedia.org/wiki/Special:FilePath/Bicol_express.jpg?width=600" alt="Bicol Express" onerror="this.style.display='none'">
          <div class="cap"><strong>Bicol Express</strong><p>Pork in coconut milk with siling labuyo &mdash; the dish that put Bicol on the map.</p></div>
        </article>
        <article class="food-card">
          <img src="https://commons.wikimedia.org/wiki/Special:FilePath/Laing.jpg?width=600" alt="Laing" onerror="this.style.display='none'">
          <div class="cap"><strong>Laing</strong><p>Dried taro leaves simmered in thick coconut milk. Earthy, creamy, and deeply Bicolano.</p></div>
        </article>
        <article class="food-card">
          <img src="https://commons.wikimedia.org/wiki/Special:FilePath/Pinangat.jpg?width=600" alt="Pinangat" onerror="this.style.display='none'">
          <div class="cap"><strong>Pinangat</strong><p>Taro and pork wrapped in coconut fronds &mdash; a slow, smoky Camalig specialty.</p></div>
        </article>
        <article class="food-card">
          <img src="https://commons.wikimedia.org/wiki/Special:FilePath/Pili_nut_(Canarium_ovatum).jpg?width=600" alt="Pili nuts" onerror="this.style.display='none'">
          <div class="cap"><strong>Pili Nuts</strong><p>Grown only in Bicol &mdash; buttery and rich. The souvenir everyone brings home.</p></div>
        </article>
      </div>
    </div>
  </section>

  <!-- PHOTO GALLERY -->
  <section class="section section-alt" aria-labelledby="gallery-title">
    <div class="rail-head" style="margin-bottom:36px;">
      <p class="eyebrow">The places</p>
      <h2 id="gallery-title">Albay in every frame.</h2>
    </div>
    <div class="gallery-grid">
      <div class="gallery-item tall">
        <img src="https://commons.wikimedia.org/wiki/Special:FilePath/Mayon%20Volcano%27s%20Crater.jpg?width=900" alt="Mayon Volcano">
        <span>Mayon Volcano</span>
      </div>
      <div class="gallery-item">
        <img src="https://commons.wikimedia.org/wiki/Special:FilePath/Mt.Mayon%20with%20Cagsawa%20Ruins,%20Albay%20by%20Say%20Bernardo.jpg?width=900" alt="Cagsawa Ruins">
        <span>Cagsawa Ruins</span>
      </div>
      <div class="gallery-item">
        <img src="https://commons.wikimedia.org/wiki/Special:FilePath/Sumlang%20Lake%20(Camalig,%20Albay;%2004-20-2023).jpg?width=900" alt="Sumlang Lake">
        <span>Sumlang Lake</span>
      </div>
      <div class="gallery-item">
        <img src="https://commons.wikimedia.org/wiki/Special:FilePath/The_Daraga_Church_in_Albay_Province.jpg?width=900" alt="Daraga Church">
        <span>Daraga Church</span>
      </div>
      <div class="gallery-item">
        <img src="https://commons.wikimedia.org/wiki/Special:FilePath/Quitinday%20Green%20Hills%20south%20view%20(Camalig,%20Albay;%2004-22-2023).jpg?width=900" alt="Quitinday Green Hills">
        <span>Quitinday Hills</span>
      </div>
    </div>
  </section>

  <!-- TRAVELER REVIEWS -->
  <section class="section" aria-labelledby="rev-title">
    <div class="rail-head" style="margin-bottom:44px;">
      <p class="eyebrow">Traveler reviews</p>
      <h2 id="rev-title">What visitors say about Albay</h2>
    </div>
    <div class="review-grid">
      <?php foreach ($reviews as $r): ?>
        <article class="review-card">
          <div class="stars" aria-label="Rated 5 out of 5">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
          <p class="quote">&ldquo;<?= h($r['quote']) ?>&rdquo;</p>
          <div class="review-author">
            <img src="<?= h($r['avatar']) ?>" alt="">
            <div>
              <div class="name"><?= h($r['name']) ?></div>
              <div class="who"><?= h($r['origin']) ?> &middot; <?= h($r['dest']) ?></div>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- INTERACTIVE MAP -->
  <section class="section section-alt" aria-labelledby="map-title">
    <div class="section-head">
      <div>
        <p class="eyebrow">Explore the province</p>
        <h2 id="map-title">Albay at a glance</h2>
      </div>
      <p>Click any pin to learn more about that destination and plan your visit.</p>
    </div>
    <div class="map-canvas" id="albay-map"></div>
  </section>

  <!-- CTA BAND -->
  <section class="section">
    <div class="cta-band">
      <div class="cta-inner">
        <div>
          <p class="eyebrow">Ready when you are</p>
          <h2>Book a trip around the perfect cone.</h2>
        </div>
        <a class="button button-ghost" href="<?= h(url('plan.php')) ?>">Plan Your Visit &rarr;</a>
      </div>
    </div>
  </section>
</main>
<?php require __DIR__ . '/includes/modal.php'; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>

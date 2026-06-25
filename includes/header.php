<?php
$pageTitle = $pageTitle ?? 'Visit Albay';
$active = $active ?? '';
$user = current_user($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($pageTitle) ?></title>
  <link rel="stylesheet" href="<?= h(url('assets/css/fonts.css')) ?>?v=<?= filemtime(__DIR__ . '/../assets/css/fonts.css') ?>">
  <link rel="stylesheet" href="<?= h(url('assets/css/styles.css')) ?>?v=<?= filemtime(__DIR__ . '/../assets/css/styles.css') ?>">
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Flatpickr colors */
    .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange, .flatpickr-day.selected:focus, .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover, .flatpickr-day.selected.prevMonthDay, .flatpickr-day.startRange.prevMonthDay, .flatpickr-day.endRange.prevMonthDay, .flatpickr-day.selected.nextMonthDay, .flatpickr-day.startRange.nextMonthDay, .flatpickr-day.endRange.nextMonthDay {
        background: #FF6B6B;
        border-color: #FF6B6B;
    }
    .flatpickr-day.disabled { color: #ccc !important; }
    #booking-alert { display: none; padding: 1rem; background: #ffebeb; border: 1px solid #ff6b6b; color: #d00; margin-top: 0.5rem; border-radius: 4px; font-size: 0.9rem; }
    
    /*  Shrink calendar popup  */
    .flatpickr-calendar {
        transform: scale(0.80); 
        transform-origin: top left; 
        font-family: inherit;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08) !important; 
    }
    

  </style>
  <?= $extraHead ?? '' ?>
</head>
<body>
<header class="site-header<?= !empty($heroHeader) ? ' site-header-hero' : '' ?>">
  <a class="brand" href="<?= h(url('index.php')) ?>"><svg width="17" height="17" viewBox="0 0 24 24" aria-hidden="true"><path d="M10.9 5.6a1.3 1.3 0 0 1 2.2 0l7.2 13.4a1 1 0 0 1-.9 1.5H4.6a1 1 0 0 1-.9-1.5Z" fill="currentColor"/></svg>Visit Albay</a>
  <nav class="nav-links" aria-label="Primary navigation">
    <a class="<?= $active === 'home' ? 'is-active' : '' ?>" href="<?= h(url('index.php')) ?>">Home<?php if ($active === 'home'): ?><span class="dot"></span><?php endif; ?></a>
    <a class="<?= $active === 'destinations' ? 'is-active' : '' ?>" href="<?= h(url('destinations.php')) ?>">Destinations<?php if ($active === 'destinations'): ?><span class="dot"></span><?php endif; ?></a>
    <a class="<?= $active === 'experience' ? 'is-active' : '' ?>" href="<?= h(url('experiences.php')) ?>">Experience<?php if ($active === 'experience'): ?><span class="dot"></span><?php endif; ?></a>
    <a class="<?= $active === 'plan' ? 'is-active' : '' ?>" href="<?= h(url('plan.php')) ?>">Plan<?php if ($active === 'plan'): ?><span class="dot"></span><?php endif; ?></a>
    <?php if ($user): ?>
      <a class="<?= $active === 'bookings' ? 'is-active' : '' ?>" href="<?= h(url('my-bookings.php')) ?>">My bookings<?php if ($active === 'bookings'): ?><span class="dot"></span><?php endif; ?></a>
      <?php if ($user['role'] === 'admin'): ?>
        <a class="<?= $active === 'admin' ? 'is-active' : '' ?>" href="<?= h(url('admin.php')) ?>">Admin<?php if ($active === 'admin'): ?><span class="dot"></span><?php endif; ?></a>
      <?php endif; ?>
    <?php endif; ?>
  </nav>
  <div class="account-actions">
    <?php if ($user): ?>
      <span class="account-name">Hi, <strong><?= h($user['username']) ?></strong></span>
      <form action="<?= h(url('logout.php')) ?>" method="post">
        <?= csrf_field() ?>
        <button class="button button-ghost" type="submit">Log out</button>
      </form>
    <?php else: ?>
      <a class="button button-primary" href="<?= h(url('login.php')) ?>">Log in</a>
    <?php endif; ?>
  </div>
</header>
<?php if ($flash = pull_flash()): ?>
  <div class="flash flash-<?= h($flash['type']) ?>" role="status"><?= h($flash['message']) ?></div>
<?php endif; ?>
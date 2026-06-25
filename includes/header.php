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
  <?= $extraHead ?? '' ?>
</head>
<body>
<header class="site-header<?= !empty($heroHeader) ? ' site-header-hero' : '' ?>">
  <a class="brand" href="<?= h(url('index.php')) ?>"><svg width="17" height="17" viewBox="0 0 24 24" aria-hidden="true"><path d="M10.9 5.6a1.3 1.3 0 0 1 2.2 0l7.2 13.4a1 1 0 0 1-.9 1.5H4.6a1 1 0 0 1-.9-1.5Z" fill="currentColor"/></svg>Visit Albay</a>
  <nav class="nav-links" aria-label="Primary navigation">
    <a class="<?= $active === 'home' ? 'is-active' : '' ?>" href="<?= h(url('index.php')) ?>">Home<?php if ($active === 'home'): ?><?php endif; ?></a>
    <a class="<?= $active === 'destinations' ? 'is-active' : '' ?>" href="<?= h(url('destinations.php')) ?>">Destinations<?php if ($active === 'destinations'): ?></span><?php endif; ?></a>
    <a class="<?= $active === 'experience' ? 'is-active' : '' ?>" href="<?= h(url('experiences.php')) ?>">Experience<?php if ($active === 'experience'): ?></span><?php endif; ?></a>
    <a class="<?= $active === 'plan' ? 'is-active' : '' ?>" href="<?= h(url('plan.php')) ?>">Book<?php if ($active === 'plan'): ?></span><?php endif; ?></a>
    <?php if ($user && $user['role'] === 'admin'): ?>
      <a class="<?= $active === 'admin' ? 'is-active' : '' ?>" href="<?= h(url('admin.php')) ?>">Admin<?php if ($active === 'admin'): ?></span><?php endif; ?></a>
    <?php endif; ?>
  </nav>
  <div class="account-actions">
    <?php if ($user): ?>
      <a class="account-bookings<?= $active === 'bookings' ? ' is-active' : '' ?>" href="<?= h(url('my-bookings.php')) ?>">My bookings</a>
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

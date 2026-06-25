<?php
require_once __DIR__ . '/includes/app.php';

if (current_user($pdo) && empty($_GET['stay'])) {
    redirect(safe_next($_GET['next'] ?? 'index.php'));
}

$pageTitle = 'Log in - Visit Albay';
$active = '';
$next = safe_next($_GET['next'] ?? 'index.php');
$startTab = (($_GET['tab'] ?? '') === 'signup') ? 'signup' : 'login';

require __DIR__ . '/includes/header.php'; 
?>

<style>
  /* Override the shared header to be transparent ONLY on the login page */
  header, .site-header, .navbar {
    position: absolute !important;
    top: 0;
    left: 0;
    width: 100%;
    background: transparent !important;
    box-shadow: none !important;
    border-bottom: none !important;
    z-index: 100;
  }
</style>

<main class="section auth-section glass-background">
  <div class="glass-layout-wrapper">
    
    <div class="glass-hero-text">
      <h2>Explore<br><span class="brand-yellow">Albay.</span></h2>
      <p>Where your dream destination becomes reality. Embark on a journey where every corner of the province is within your reach.</p>
    </div>

    <div class="auth-card glass-card">
      <p class="eyebrow glass-eyebrow">Visit Albay</p>
      <h1 class="auth-title">Welcome</h1>
      <p class="auth-sub">Log in or create an account to book trips and manage them.</p>

      <div class="auth-toggle glass-toggle" role="tablist" aria-label="Log in or sign up">
        <button type="button" class="<?= $startTab === 'login' ? 'is-active' : '' ?>" data-tab-target="login" role="tab">Log in</button>
        <button type="button" class="<?= $startTab === 'signup' ? 'is-active' : '' ?>" data-tab-target="signup" role="tab">Sign up</button>
      </div>

      <form class="tab-panel auth-form <?= $startTab === 'login' ? 'is-active' : '' ?>" data-tab-panel="login" action="<?= h(url('actions/login.php')) ?>" method="post" data-prevent-double-submit>
        <?= csrf_field() ?>
        <input type="hidden" name="next" value="<?= h($next) ?>">
        <label class="glass-label">
          Username
          <input class="glass-input" name="username" autocomplete="username" required placeholder="Enter your username">
        </label>
        <label class="glass-label">
          Password
          <input class="glass-input" name="password" type="password" autocomplete="current-password" required placeholder="••••••••">
        </label>
        <button class="button button-primary auth-submit glass-button" type="submit">SIGN IN</button>
      </form>

      <form class="tab-panel auth-form <?= $startTab === 'signup' ? 'is-active' : '' ?>" data-tab-panel="signup" action="<?= h(url('actions/signup.php')) ?>" method="post" data-prevent-double-submit>
        <?= csrf_field() ?>
        <input type="hidden" name="next" value="<?= h($next) ?>">
        <label class="glass-label">
          Username
          <input class="glass-input" name="username" autocomplete="username" minlength="3" required placeholder="Choose a username">
        </label>
        <label class="glass-label">
          Password
          <input class="glass-input" name="password" type="password" autocomplete="new-password" minlength="3" required placeholder="Choose a password">
        </label>
        <button class="button button-primary auth-submit glass-button" type="submit">CREATE ACCOUNT</button>
        <p class="auth-hint glass-notice">Pick a username and password (at least 3 characters each).</p>
      </form>
    </div>

  </div>
</main>
<script src="<?= h(url('assets/js/validation.js')) ?>?v=<?= filemtime(__DIR__ . '/assets/js/validation.js') ?>"></script>
<?php require __DIR__ . '/includes/footer.php'; ?>
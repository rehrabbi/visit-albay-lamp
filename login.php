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
<main class="section auth-section">
  <div class="auth-card">
    <p class="eyebrow">Visit Albay</p>
    <h1 class="auth-title">Welcome</h1>
    <p class="auth-sub">Log in or create an account to book trips and manage them.</p>

    <div class="auth-toggle" role="tablist" aria-label="Log in or sign up">
      <button type="button" class="<?= $startTab === 'login' ? 'is-active' : '' ?>" data-tab-target="login" role="tab">Log in</button>
      <button type="button" class="<?= $startTab === 'signup' ? 'is-active' : '' ?>" data-tab-target="signup" role="tab">Sign up</button>
    </div>

    <form class="tab-panel auth-form <?= $startTab === 'login' ? 'is-active' : '' ?>" data-tab-panel="login" action="<?= h(url('actions/login.php')) ?>" method="post" data-prevent-double-submit>
      <?= csrf_field() ?>
      <input type="hidden" name="next" value="<?= h($next) ?>">
      <label>
        Username
        <input name="username" autocomplete="username" required>
      </label>
      <label>
        Password
        <input name="password" type="password" autocomplete="current-password" required>
      </label>
      <button class="button button-primary auth-submit" type="submit">Log in</button>
      <p class="notice">Demo accounts: <strong>admin / admin</strong> or <strong>user / user</strong>.</p>
    </form>

    <form class="tab-panel auth-form <?= $startTab === 'signup' ? 'is-active' : '' ?>" data-tab-panel="signup" action="<?= h(url('actions/signup.php')) ?>" method="post" data-prevent-double-submit>
      <?= csrf_field() ?>
      <input type="hidden" name="next" value="<?= h($next) ?>">
      <label>
        Username
        <input name="username" autocomplete="username" minlength="3" required>
      </label>
      <label>
        Password
        <input name="password" type="password" autocomplete="new-password" minlength="3" required>
      </label>
      <button class="button button-primary auth-submit" type="submit">Create account</button>
      <p class="auth-hint">Pick a username and password (at least 3 characters each).</p>
    </form>
  </div>
</main>
<script src="<?= h(url('assets/js/validation.js')) ?>?v=<?= filemtime(__DIR__ . '/assets/js/validation.js') ?>"></script>
<?php require __DIR__ . '/includes/footer.php'; ?>

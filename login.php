<?php
require_once __DIR__ . '/php/functions.php';
require_guest('dashboard.php');

$pageTitle = 'Login | Roommates App';
$activePage = 'login';
$returnTo = trim((string)($_GET['return_to'] ?? ''));
require_once __DIR__ . '/partials/header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-7 col-xl-6">
    <div class="surface-card p-4 p-lg-5">
      <div class="section-kicker mb-2">Welcome back</div>
      <h1 class="h2 mb-3">Log in to access your dashboard.</h1>
      <p class="copy-muted mb-4">Use your email and password to authenticate. A successful login creates a PHP session.</p>
      <form action="php/login_user.php" method="post" class="row g-3">
        <?php if ($returnTo !== ''): ?>
          <input type="hidden" name="return_to" value="<?= e($returnTo) ?>">
        <?php endif; ?>
        <div class="col-12">
          <label class="form-label" for="email">Email</label>
          <input class="form-control" id="email" name="email" type="email" value="<?= e(old('email')) ?>" required>
        </div>
        <div class="col-12">
          <label class="form-label" for="password">Password</label>
          <input class="form-control" id="password" name="password" type="password" required>
        </div>
        <div class="col-12 d-flex flex-wrap gap-3 pt-2">
          <button type="submit" class="btn btn-accent btn-lg px-4">Log in</button>
          <a href="register.php" class="btn btn-outline-dark btn-lg px-4">Create account</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
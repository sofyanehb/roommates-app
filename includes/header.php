<?php
require_once __DIR__ . '/../config.php';

$pageTitle = $pageTitle ?? 'Roommates App';
$activePage = $activePage ?? '';
$flashes = get_flashes();
$cssVersion = @filemtime(__DIR__ . '/../css/styles.css') ?: time();
$notificationCount = 0;
if (is_logged_in()) {
  $notificationCount = unread_notification_count($pdo, (int)current_user_id());
  $messageCount = unread_message_count($pdo, (int)current_user_id());
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= e($pageTitle) ?></title>
    <script>
      (function () {
        var storageKey = "roommates-theme";
        var mediaQuery = window.matchMedia("(prefers-color-scheme: dark)");

        function readStoredTheme() {
          try {
            var value = localStorage.getItem(storageKey);
            return value === "light" || value === "dark" ? value : null;
          } catch (error) {
            return null;
          }
        }

        function writeStoredTheme(theme) {
          try {
            localStorage.setItem(storageKey, theme);
          } catch (error) {
            // Ignore storage write failures.
          }
        }

        function currentTheme() {
          return document.documentElement.getAttribute("data-theme") === "dark" ? "dark" : "light";
        }

        function applyTheme(theme) {
          document.documentElement.setAttribute("data-theme", theme);
          document.documentElement.style.colorScheme = theme;

          document.querySelectorAll("[data-theme-toggle]").forEach(function (button) {
            var nextLabel = theme === "dark" ? "Switch to light mode" : "Switch to dark mode";
            var hiddenText = button.querySelector(".theme-toggle-label");
            if (hiddenText) {
              hiddenText.textContent = nextLabel;
            }
            button.setAttribute("aria-pressed", theme === "dark" ? "true" : "false");
            button.setAttribute("aria-label", nextLabel);
            button.setAttribute("title", nextLabel);
            button.setAttribute("data-theme-state", theme);
          });
        }

        function resolveTheme() {
          var stored = readStoredTheme();
          if (stored) {
            return stored;
          }
          return mediaQuery.matches ? "dark" : "light";
        }

        function toggleTheme() {
          var nextTheme = currentTheme() === "dark" ? "light" : "dark";
          applyTheme(nextTheme);
          writeStoredTheme(nextTheme);
        }

        window.roommatesToggleTheme = toggleTheme;

        try {
          applyTheme(resolveTheme());
        } catch (error) {
          applyTheme("light");
        }

        document.addEventListener("DOMContentLoaded", function () {
          applyTheme(resolveTheme());
        });

        function onSystemThemeChange() {
          if (!readStoredTheme()) {
            applyTheme(mediaQuery.matches ? "dark" : "light");
          }
        }

        if (typeof mediaQuery.addEventListener === "function") {
          mediaQuery.addEventListener("change", onSystemThemeChange);
        } else if (typeof mediaQuery.addListener === "function") {
          mediaQuery.addListener(onSystemThemeChange);
        }
      })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?= ASSETS_URL ?>/css/styles.css?v=<?= (int)$cssVersion ?>" rel="stylesheet" />
  </head>

  <body class="app-shell">
    <?php $loggedIn = is_logged_in(); ?>
    <nav class="navbar navbar-expand-custom navbar-dark app-nav">
      <div class="container-fluid px-lg-4 px-3">
        <a class="navbar-brand brand-mark" href="index.php">Roommates App</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-collapse collapse" id="mainNav">
          <ul class="navbar-nav gap-lg-1 app-nav-links ms-auto">
            <li class="nav-item"><a class="nav-link <?= $activePage === 'home' ? 'active' : '' ?>" href="index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link <?= $activePage === 'search' ? 'active' : '' ?>" href="search.php">Search</a></li>
            <li class="nav-item"><a class="nav-link <?= $activePage === 'business_plan' ? 'active' : '' ?>" href="business_plan.php">Business plan</a></li>
            <li class="nav-item">
              <button type="button" class="nav-link theme-toggle-btn border-0 bg-transparent" data-theme-toggle data-theme-state="light" aria-pressed="false" aria-label="Switch to dark mode" title="Switch to dark mode" onclick="window.roommatesToggleTheme && window.roommatesToggleTheme()">
                <span class="theme-toggle-label visually-hidden">Switch to dark mode</span>
              </button>
            </li>
            <?php if (!$loggedIn): ?>
            <li class="nav-item"><a class="nav-link <?= $activePage === 'register' ? 'active' : '' ?>" href="register.php">Register</a></li>
            <li class="nav-item"><a class="nav-link <?= $activePage === 'login' ? 'active' : '' ?>" href="login.php">Login</a></li>
            <?php else: ?>
            <li class="nav-item"><a class="nav-link <?= $activePage === 'dashboard' ? 'active' : '' ?>" href="dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link <?= $activePage === 'add_listing' ? 'active' : '' ?>" href="add_listing.php">Add listing</a></li>
            <li class="nav-item"><a class="nav-link <?= $activePage === 'shortlist' ? 'active' : '' ?>" href="shortlist.php">Shortlist</a></li>
            <li class="nav-item"><a class="nav-link <?= $activePage === 'notifications' ? 'active' : '' ?>" href="notifications.php">Notifications<?= $notificationCount > 0 ? ' (' . $notificationCount . ')' : '' ?></a></li>
            <li class="nav-item"><a class="nav-link <?= $activePage === 'chat' ? 'active' : '' ?>" href="chat.php">Chat<?= $messageCount > 0 ? ' (' . $messageCount . ')' : '' ?></a></li>
            <li class="nav-item"><a class="nav-link <?= $activePage === 'profile' ? 'active' : '' ?>" href="profile.php">Profile</a></li>
            <li class="nav-item"><a class="nav-link <?= $activePage === 'contact' ? 'active' : '' ?>" href="contact.php">Contact</a></li>
            <?php if (is_admin()): ?>
            <li class="nav-item"><a class="nav-link <?= $activePage === 'admin' ? 'active' : '' ?>" href="admin.php">Admin</a></li>
            <li class="nav-item"><a class="nav-link <?= $activePage === 'admin_analytics' ? 'active' : '' ?>" href="admin_analytics.php">Analytics</a></li>
            <?php endif; ?>
            <li class="nav-item"><span class="nav-link disabled" aria-disabled="true">Hi, <?= e(current_user_name()) ?></span></li>
            <li class="nav-item">
              <form method="post" action="../php/logout.php" class="m-0">
                <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>" />
                <button type="submit" class="nav-link text-warning border-0 bg-transparent">Logout</button>
              </form>
            </li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </nav>
    <main class="py-lg-5 container py-4">
      <?php foreach ($flashes as $flash): ?>
      <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show shadow-sm" role="alert">
        <?= e($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php endforeach; ?>
    </main>
  </body>
</html>
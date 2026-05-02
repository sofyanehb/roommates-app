    <footer class="app-footer mt-5 py-5">
      <div class="container">
        <div class="row g-4 align-items-start">
          <div class="col-lg-5">
            <div class="brand-mark h4 mb-2">Roommates App</div>
            <p class="copy-muted mb-0">A structured roommate-matching platform designed to look credible in a business presentation while staying practical in PHP and MySQL.</p>
          </div>
          <div class="col-md-3 col-lg-2">
            <div class="footer-title mb-3">Product</div>
            <div class="d-grid gap-2 small">
              <a href="index.php">Home</a>
              <a href="search.php">Search</a>
              <a href="business_plan.php">Business plan</a>
              <a href="dashboard.php">Dashboard</a>
            </div>
          </div>
          <div class="col-md-3 col-lg-2">
            <div class="footer-title mb-3">Account</div>
            <div class="d-grid gap-2 small">
              <?php if (is_logged_in()): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="add_listing.php">Add listing</a>
                <a href="shortlist.php">Shortlist</a>
                <a href="notifications.php">Notifications</a>
                <a href="chat.php">Chat</a>
                <a href="profile.php">Profile</a>
                <a href="contact.php">Contact</a>
                <form method="post" action="../php/logout.php" class="m-0">
                  <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                  <button type="submit" class="footer-link-button">Logout</button>
                </form>
              <?php else: ?>
                <a href="register.php">Register</a>
                <a href="login.php">Login</a>
                <a href="search.php">Browse listings</a>
              <?php endif; ?>
            </div>
          </div>
          <div class="col-lg-3">
            <div class="footer-title mb-3">Positioning</div>
            <p class="copy-muted small mb-0">Trust-first design, clear search flows, and extensible architecture for future premium features.</p>
          </div>
        </div>
      </div>
    </footer>
    </main>
    <?php $jsVersion = @filemtime(__DIR__ . '/../js/app.js') ?: time(); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= ASSETS_URL ?>/js/app.js?v=<?= (int)$jsVersion ?>"></script>
    </body>

    </html>
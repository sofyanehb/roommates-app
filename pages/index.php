<?php
require_once __DIR__ . '/../config.php';

$overview = get_business_overview($pdo);
$loggedIn = is_logged_in();

$pageTitle = 'Roommates App | Find compatible roommates';
$activePage = 'home';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="hero-panel p-lg-5 mb-lg-5 mb-4 p-4">
  <div class="row align-items-center g-4 position-relative" style="z-index: 1">
    <div class="col-lg-7">
      <div class="section-kicker mb-3">Roommates platform for growth</div>
      <?php if ($loggedIn): ?>
      <h1 class="display-4 fw-bold mb-3">Welcome back, <?= e(current_user_name()) ?>.</h1>
      <?php else: ?>
      <h1 class="display-4 fw-bold mb-3">A cleaner way to match students with compatible housing.</h1>
      <?php endif; ?>
      <p class="lead-copy mb-4"><?php if ($loggedIn): ?> Continue your roommate workflow with your dashboard, chat inbox, and shortlist. Everything is synced to your account session. <?php else: ?> Built as a professional PHP and MySQL product concept, the platform combines secure onboarding, structured listings, and fast search to replace fragmented social-media roommate hunting. <?php endif; ?></p>
      <div class="d-flex flex-wrap gap-3">
        <?php if ($loggedIn): ?>
        <a class="btn btn-accent btn-lg px-4" href="dashboard.php">Go to dashboard</a>
        <a class="btn btn-ghost btn-lg px-4" href="chat.php">Open chat</a>
        <a class="btn btn-outline-dark btn-lg px-4" href="shortlist.php">Open shortlist</a>
        <a class="btn btn-outline-dark btn-lg px-4" href="search.php">Explore listings</a>
        <?php else: ?>
        <a class="btn btn-accent btn-lg px-4" href="register.php">Create account</a>
        <a class="btn btn-ghost btn-lg px-4" href="search.php">Explore listings</a>
        <a class="btn btn-outline-dark btn-lg px-4" href="business_plan.php">Business plan</a>
        <a class="btn btn-outline-dark btn-lg px-4" href="dashboard.php">View dashboard</a>
        <?php endif; ?>
      </div>
      <div class="d-flex mt-4 flex-wrap gap-2">
        <span class="pill-chip">Secure sessions</span>
        <span class="pill-chip">Filtered search</span>
        <span class="pill-chip">Responsive UI</span>
        <span class="pill-chip">Messaging-ready</span>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="card-soft p-lg-4 p-4">
        <div class="badge-soft mb-3">Business snapshot</div>
        <div class="d-grid gap-3">
          <div class="mini-metric">
            <span class="mini-metric-value"><?= $overview['users'] ?></span>
            <span class="mini-metric-label">Registered users</span>
          </div>
          <div class="mini-metric">
            <span class="mini-metric-value"><?= $overview['listings'] ?></span>
            <span class="mini-metric-label">Published listings</span>
          </div>
          <div class="mini-metric">
            <span class="mini-metric-value"><?= $overview['messages'] ?></span>
            <span class="mini-metric-label">Messages exchanged</span>
          </div>
          <div class="mini-metric">
            <span class="mini-metric-value"><?= $overview['cities'] ?></span>
            <span class="mini-metric-label">Cities covered</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="row g-3 g-lg-4 mb-lg-5 mb-4">
  <div class="col-md-3 col-6">
    <div class="metric-card h-100">
      <div class="metric-value">01</div>
      <div class="metric-label">Register with a verified profile and hashed password storage.</div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="metric-card h-100">
      <div class="metric-value">02</div>
      <div class="metric-label">Publish a listing tied to your account and current housing needs.</div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="metric-card h-100">
      <div class="metric-value">03</div>
      <div class="metric-label">Search with city and budget filters to narrow results quickly.</div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="metric-card h-100">
      <div class="metric-value">04</div>
      <div class="metric-label">Use messaging to move from discovery to direct contact.</div>
    </div>
  </div>
</section>

<section class="row g-4 mb-lg-5 mb-4">
  <div class="col-lg-8">
    <div class="surface-card p-lg-5 h-100 p-4">
      <div class="section-kicker mb-2">Why it looks business-ready</div>
      <h2 class="h1 mb-3">The product is positioned around trust, speed, and conversion.</h2>
      <div class="row g-3">
        <div class="col-md-6">
          <div class="feature-card h-100 p-4">
            <div class="badge-soft mb-3">Trust</div>
            <p class="copy-muted mb-0">Passwords are hashed, sessions protect private pages, and the data model is normalized for future growth.</p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="feature-card h-100 p-4">
            <div class="badge-soft mb-3">Speed</div>
            <p class="copy-muted mb-0">Structured filters, reusable helpers, and a direct user flow keep the experience simple and fast.</p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="feature-card h-100 p-4">
            <div class="badge-soft mb-3">Engagement</div>
            <p class="copy-muted mb-0">Listings, search, and optional messaging create a clean funnel from discovery to contact.</p>
          </div>
        </div>
        <div class="col-md-6">
          <div class="feature-card h-100 p-4">
            <div class="badge-soft mb-3">Scalability</div>
            <p class="copy-muted mb-0">The codebase separates pages, actions, and shared helpers, which makes future features easy to add.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="surface-card h-100 p-4">
      <h3 class="h4 mb-3">Featured cities</h3>
      <div class="d-grid gap-2">
        <a href="search.php?city=Casablanca" class="btn btn-outline-dark text-start">Casablanca</a>
        <a href="search.php?city=Rabat" class="btn btn-outline-dark text-start">Rabat</a>
        <a href="search.php?city=Marrakech" class="btn btn-outline-dark text-start">Marrakech</a>
        <a href="search.php?city=Tangier" class="btn btn-outline-dark text-start">Tangier</a>
      </div>
      <hr class="my-4" />
      <h3 class="h5 mb-3">Business model ready</h3>
      <ul class="list-unstyled d-grid copy-muted mb-0 gap-2">
        <li>Lead generation through listings and contact requests.</li>
        <li>Potential premium visibility for featured roommate profiles.</li>
        <li>Expandable foundation for moderation and verification.</li>
      </ul>
    </div>
  </div>
</section>

<section class="surface-card p-lg-5 p-4">
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
      <div class="section-kicker mb-2">Ready pages</div>
      <h2 class="h3 mb-0">Everything needed for a persuasive demo.</h2>
    </div>
    <a href="search.php" class="btn btn-accent px-4">Open search</a>
  </div>
  <div class="row g-3">
    <div class="col-md-4">
      <div class="feature-card h-100 p-4">
        <h3 class="h5">User journey</h3>
        <p class="copy-muted mb-0">Register, log in, publish a listing, and browse the marketplace from a single connected flow.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card h-100 p-4">
        <h3 class="h5">Operations</h3>
        <p class="copy-muted mb-0">The dashboard centralizes listings and messaging so the platform feels organized and credible.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card h-100 p-4">
        <h3 class="h5">Growth path</h3>
        <p class="copy-muted mb-0">Verification, admin moderation, and featured placements can be added without redesigning the project.</p>
      </div>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<?php
require_once __DIR__ . '/../config.php';

$overview = get_business_overview($pdo);

$pageTitle = 'Business Plan | Roommates App';
$activePage = 'business_plan';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="hero-panel p-lg-5 mb-lg-5 mb-4 p-4">
  <div class="row align-items-center g-4 position-relative" style="z-index: 1">
    <div class="col-lg-8">
      <div class="section-kicker mb-3">Business plan summary</div>
      <h1 class="display-5 fw-bold mb-3">A focused roommate marketplace for student housing.</h1>
      <p class="lead-copy mb-0">This page turns the application into a business-ready presentation with clear opportunity, revenue path, metrics, and rollout plan.</p>
    </div>
    <div class="col-lg-4">
      <div class="card-soft p-4">
        <div class="badge-soft mb-3">Current platform scale</div>
        <div class="d-grid gap-3">
          <div class="mini-metric">
            <span class="mini-metric-value"><?= $overview['users'] ?></span>
            <span class="mini-metric-label">Users</span>
          </div>
          <div class="mini-metric">
            <span class="mini-metric-value"><?= $overview['listings'] ?></span>
            <span class="mini-metric-label">Listings</span>
          </div>
          <div class="mini-metric">
            <span class="mini-metric-value"><?= $overview['messages'] ?></span>
            <span class="mini-metric-label">Messages</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="row g-4 mb-lg-5 mb-4">
  <div class="col-lg-8">
    <div class="surface-card p-lg-5 h-100 p-4">
      <div class="section-kicker mb-2">Opportunity</div>
      <h2 class="h2 mb-3">The market problem is simple and visible.</h2>
      <p class="copy-muted mb-0">Students still find roommates through unstructured social posts, private messages, and fragmented group chats. That creates low trust, wasted time, and weak matching. Roommates App provides a centralized, searchable, and session-protected alternative.</p>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="surface-card h-100 p-4">
      <div class="section-kicker mb-2">Target users</div>
      <ul class="list-unstyled d-grid copy-muted mb-0 gap-3">
        <li>Students looking for shared accommodation.</li>
        <li>Room owners needing structured visibility.</li>
        <li>Users who want city and budget-based filtering.</li>
      </ul>
    </div>
  </div>
</section>

<section class="row g-4 mb-lg-5 mb-4">
  <div class="col-md-4">
    <div class="feature-card h-100 p-4">
      <div class="badge-soft mb-3">Revenue model</div>
      <h3 class="h5">Freemium discovery</h3>
      <p class="copy-muted mb-0">Basic listings remain free, while premium visibility, featured placement, or verified profiles can be introduced later.</p>
    </div>
  </div>
  <div class="col-md-4">
    <div class="feature-card h-100 p-4">
      <div class="badge-soft mb-3">Monetization</div>
      <h3 class="h5">Marketplace services</h3>
      <p class="copy-muted mb-0">The platform can support lead generation, moderation services, and partner offers such as moving or furniture support.</p>
    </div>
  </div>
  <div class="col-md-4">
    <div class="feature-card h-100 p-4">
      <div class="badge-soft mb-3">Value proposition</div>
      <h3 class="h5">Trust and efficiency</h3>
      <p class="copy-muted mb-0">Password hashing, sessions, and structured filters create a cleaner experience than social-media-based alternatives.</p>
    </div>
  </div>
</section>

<section class="surface-card p-lg-5 mb-lg-5 mb-4 p-4">
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
      <div class="section-kicker mb-2">Key metrics</div>
      <h2 class="h3 mb-0">What investors or teachers can quickly understand.</h2>
    </div>
    <span class="badge-soft">Live from database</span>
  </div>
  <div class="row g-3">
    <div class="col-md-3 col-6">
      <div class="metric-card h-100">
        <div class="metric-value"><?= $overview['users'] ?></div>
        <div class="metric-label">Registered users</div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="metric-card h-100">
        <div class="metric-value"><?= $overview['listings'] ?></div>
        <div class="metric-label">Active listings</div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="metric-card h-100">
        <div class="metric-value"><?= $overview['cities'] ?></div>
        <div class="metric-label">Cities covered</div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="metric-card h-100">
        <div class="metric-value"><?= $overview['messages'] ?></div>
        <div class="metric-label">Conversations</div>
      </div>
    </div>
  </div>
</section>

<section class="row g-4 mb-lg-5 mb-4">
  <div class="col-lg-7">
    <div class="surface-card p-lg-5 h-100 p-4">
      <div class="section-kicker mb-2">Roadmap</div>
      <h2 class="h3 mb-3">A realistic next-phase plan.</h2>
      <div class="d-grid gap-3">
        <div class="feature-card p-3">
          <h3 class="h5 mb-2">Phase 1</h3>
          <p class="copy-muted mb-0">Launch the core registration, listings, and search experience for students.</p>
        </div>
        <div class="feature-card p-3">
          <h3 class="h5 mb-2">Phase 2</h3>
          <p class="copy-muted mb-0">Add moderation tools, verification signals, and better profile completeness scoring.</p>
        </div>
        <div class="feature-card p-3">
          <h3 class="h5 mb-2">Phase 3</h3>
          <p class="copy-muted mb-0">Introduce premium placement, analytics, and stronger onboarding for business growth.</p>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="surface-card h-100 p-4">
      <div class="section-kicker mb-2">Go-to-market</div>
      <h2 class="h4 mb-3">How the platform can spread.</h2>
      <ul class="list-unstyled d-grid copy-muted mb-0 gap-3">
        <li>Campus ambassadors and student communities.</li>
        <li>Social ads targeted by city and university.</li>
        <li>Referral incentives for listing creators.</li>
        <li>Landing pages optimized for conversion.</li>
      </ul>
    </div>
  </div>
</section>

<section class="surface-card p-lg-5 p-4">
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
      <div class="section-kicker mb-2">Business case</div>
      <h2 class="h3 mb-0">The project already looks like a product, not just a school exercise.</h2>
    </div>
    <a class="btn btn-accent px-4" href="search.php">See live listings</a>
  </div>
  <p class="copy-muted mb-0">The current implementation gives you a presentable narrative for a business plan: a clear problem, a simple product, measurable user activity, and obvious expansion paths for monetization and moderation.</p>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
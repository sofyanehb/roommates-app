<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../php/search_action.php';

$pageTitle = 'Search Listings | Roommates App';
$activePage = 'search';

$city = trim((string)($_GET['city'] ?? ''));
$budget = trim((string)($_GET['budget'] ?? ''));
$viewerId = is_logged_in() ? (int)current_user_id() : null;
$listings = search_listings($pdo, $city, $budget, $viewerId);
$resultCount = count($listings);
$currentSearchUrl = 'search.php' . ((isset($_SERVER['QUERY_STRING']) && (string)$_SERVER['QUERY_STRING'] !== '') ? '?' . (string)$_SERVER['QUERY_STRING'] : '');

$favoriteMap = [];
$viewerProfile = [];
if ($viewerId !== null) {
  $favoriteStmt = $pdo->
prepare('SELECT listing_id FROM favorites WHERE user_id = ?'); $favoriteStmt->execute([$viewerId]); foreach ($favoriteStmt->fetchAll() as $favRow) { $favoriteMap[(int)$favRow['listing_id']] = true; } $profileStmt = $pdo->prepare('SELECT sleep_schedule, smoking_preference, pet_preference, study_habit FROM users WHERE id = ? LIMIT 1'); $profileStmt->execute([$viewerId]); $viewerProfile = $profileStmt->fetch() ?: []; } require_once __DIR__ . '/../includes/header.php'; ?>
<section class="hero-panel p-lg-5 mb-4 p-4">
  <div class="row g-4 align-items-center position-relative" style="z-index: 1">
    <div class="col-lg-7">
      <div class="section-kicker mb-2">Search and filters</div>
      <h1 class="h2 mb-3">Find listings by city and maximum budget.</h1>
      <p class="lead-copy mb-0">Fast filters help users discover relevant roommates without scrolling through irrelevant posts.</p>
    </div>
    <div class="col-lg-5">
      <div class="card-soft p-4">
        <div class="badge-soft mb-3">Current result set</div>
        <div class="mini-metric">
          <span class="mini-metric-value"><?= $resultCount ?></span>
          <span class="mini-metric-label">Listings match your filters</span>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="surface-card p-lg-5 mb-4 p-4">
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
    <div>
      <div class="section-kicker mb-2">Search by location</div>
      <h2 class="h4 mb-0">Quick city shortcuts</h2>
    </div>
    <span class="badge-soft">Live filter enabled</span>
  </div>
  <div class="d-flex mb-4 flex-wrap gap-2">
    <a class="pill-chip pill-chip-link" href="search.php?city=Casablanca">Casablanca</a>
    <a class="pill-chip pill-chip-link" href="search.php?city=Rabat">Rabat</a>
    <a class="pill-chip pill-chip-link" href="search.php?city=Marrakech">Marrakech</a>
    <a class="pill-chip pill-chip-link" href="search.php?city=Tangier">Tangier</a>
    <a class="pill-chip pill-chip-link" href="search.php?city=Agadir">Agadir</a>
  </div>
  <form method="get" class="row g-3" data-live-filter>
    <div class="col-md-6">
      <label class="form-label" for="city">City</label>
      <input class="form-control" id="city" name="city" type="text" placeholder="Search by city" value="<?= e($city) ?>" />
    </div>
    <div class="col-md-6">
      <label class="form-label" for="budget">Maximum budget</label>
      <input class="form-control" id="budget" name="budget" type="number" min="1" step="1" placeholder="Budget limit" value="<?= e($budget) ?>" />
    </div>
    <div class="d-flex col-12 flex-wrap gap-3 pt-2">
      <button class="btn btn-accent px-4" type="submit">Apply filters</button>
      <a class="btn btn-outline-dark px-4" href="search.php">Reset</a>
    </div>
  </form>
  <?php if ($viewerId !== null): ?>
  <form method="post" action="../php/save_search.php" class="mt-3">
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>" />
    <input type="hidden" name="city" value="<?= e($city) ?>" />
    <input type="hidden" name="budget_max" value="<?= e($budget) ?>" />
    <input type="hidden" name="redirect_to" value="<?= e($currentSearchUrl) ?>" />
    <button class="btn btn-ghost px-4" type="submit">Save current search</button>
  </form>
  <?php endif; ?>
</section>

<section class="search-grid">
  <?php if (!$listings): ?>
  <div class="empty-state p-4 text-center" data-empty-state>
    <h2 class="h4">No results found</h2>
    <p class="copy-muted mb-3">Try a broader city name or increase the maximum budget.</p>
    <a class="btn btn-accent" href="search.php">Clear filters</a>
  </div>
  <?php endif; ?> <?php if ($listings): ?>
  <div class="surface-card d-flex justify-content-between align-items-center flex-wrap gap-2 p-3 px-4" data-results-summary>
    <span class="copy-muted">Showing <?= $resultCount ?> listing<?= $resultCount === 1 ? '' : 's' ?>.</span>
    <span class="badge-soft">Dynamic filtering active</span>
  </div>
  <?php endif; ?> <?php foreach ($listings as $listing): ?>
  <article class="listing-card p-4" data-listing-card data-city="<?= e((string)$listing['city']) ?>" data-budget="<?= e((string)$listing['budget']) ?>">
    <div class="d-flex justify-content-between align-items-start mb-3 gap-3">
      <div>
        <div class="d-flex mb-2 flex-wrap gap-2">
          <span class="badge-soft"><?= e((string)$listing['city']) ?></span>
          <?php if ((string)$listing['verification_status'] === 'verified'): ?>
          <span class="badge-soft">Verified</span>
          <?php endif; ?>
          <span class="badge-soft text-capitalize">
            <?= e((string)$listing['plan_tier']) ?>
            plan
          </span>
        </div>
        <h2 class="h4 mb-0"><?= e((string)$listing['name']) ?></h2>
      </div>
      <div class="text-end">
        <div class="copy-muted small">Budget</div>
        <strong>
          <?= number_format((int)$listing['budget']) ?>
          MAD
        </strong>
      </div>
    </div>
    <?php if (!empty($listing['image_path'])): $raw = (string)$listing['image_path']; if (preg_match('#^https?://#i', $raw)) { $imgSrc = $raw; } elseif (strpos($raw, ASSETS_URL) === 0 || strpos($raw, '/') === 0) { $imgSrc = $raw; } else { $imgSrc = rtrim(ASSETS_URL, '/') . '/' . ltrim($raw, '/'); } ?>
    <img src="<?= e($imgSrc) ?>" alt="Listing image" class="img-fluid mb-3 rounded" style="max-height: 220px; object-fit: cover; width: 100%" />
    <?php endif; ?>
    <div class="small text-muted mb-3">Move-in date: <?= e((string)$listing['move_in_date']) ?></div>
    <?php if ($viewerId !== null && $viewerProfile): ?> <?php $score = compatibility_score($viewerProfile, $listing); ?>
    <div class="small mb-2"><span class="badge-soft">Compatibility score: <?= $score ?>%</span></div>
    <?php endif; ?>
    <p class="mb-3"><?= nl2br(e((string)$listing['preferences'])) ?></p>
    <div class="d-flex flex-wrap gap-2">
      <a class="btn btn-accent btn-sm" href="listing.php?id=<?= (int)$listing['id'] ?>">View details</a>
      <?php $chatTarget = 'chat.php?receiver_id=' . (int)$listing['user_id']; $contactHref = $viewerId !== null ? $chatTarget : ('login.php?return_to=' . urlencode($chatTarget)); ?>
      <a class="btn btn-outline-dark btn-sm" href="<?= e($contactHref) ?>">Contact</a>
      <?php if ($viewerId !== null): ?>
      <form method="post" action="../php/toggle_favorite.php" class="m-0">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>" />
        <input type="hidden" name="listing_id" value="<?= (int)$listing['id'] ?>" />
        <input type="hidden" name="redirect_to" value="<?= e($currentSearchUrl) ?>" />
        <button class="btn btn-outline-dark btn-sm" type="submit"><?= isset($favoriteMap[(int)$listing['id']]) ? 'Unfavorite' : 'Favorite' ?></button>
      </form>
      <form method="post" action="../php/report_listing.php" class="m-0">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>" />
        <input type="hidden" name="listing_id" value="<?= (int)$listing['id'] ?>" />
        <button class="btn btn-outline-danger btn-sm" type="submit">Report</button>
      </form>
      <form method="post" action="../php/block_user.php" class="m-0">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>" />
        <input type="hidden" name="user_id" value="<?= (int)$listing['user_id'] ?>" />
        <button class="btn btn-outline-secondary btn-sm" type="submit">Block user</button>
      </form>
      <?php endif; ?>
    </div>
  </article>
  <?php endforeach; ?>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
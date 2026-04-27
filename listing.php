<?php
require_once __DIR__ . '/php/functions.php';

$listingId = (int)($_GET['id'] ?? 0);
if ($listingId < 1) {
  set_flash('warning', 'Listing not found.');
  redirect('search.php');
}

$stmt = $pdo->prepare('SELECT l.id, l.user_id, l.budget, l.move_in_date, l.preferences, l.status, l.expires_at, l.image_path, l.created_at,
  u.name, u.city, u.verification_status, u.plan_tier, u.sleep_schedule, u.smoking_preference, u.pet_preference, u.study_habit
  FROM listings l
  INNER JOIN users u ON l.user_id = u.id
  WHERE l.id = ?
  LIMIT 1');
$stmt->execute([$listingId]);
$listing = $stmt->fetch();

if (!$listing) {
  set_flash('warning', 'Listing not found.');
  redirect('search.php');
}

$viewerId = is_logged_in() ? (int)current_user_id() : null;
$isOwner = $viewerId !== null && $viewerId === (int)$listing['user_id'];
$isAdminViewer = is_logged_in() && is_admin();

if (!$isOwner && !$isAdminViewer) {
  $isOpen = (string)$listing['status'] === 'open';
  $notExpired = empty($listing['expires_at']) || (string)$listing['expires_at'] > date('Y-m-d H:i:s');
  if (!$isOpen || !$notExpired) {
    set_flash('warning', 'This listing is not publicly available.');
    redirect('search.php');
  }
}

if ($viewerId !== null && !$isOwner && is_user_blocked($pdo, $viewerId, (int)$listing['user_id'])) {
  set_flash('warning', 'This listing is unavailable.');
  redirect('search.php');
}

$relatedSql = 'SELECT l.id, l.user_id, l.budget, l.move_in_date, l.status, u.name, u.verification_status, u.plan_tier
  FROM listings l
  INNER JOIN users u ON l.user_id = u.id
  WHERE u.city = :city
    AND l.id <> :current_id
    AND l.status = "open"
    AND (l.expires_at IS NULL OR l.expires_at > NOW())';
$relatedParams = [
  'city' => (string)$listing['city'],
  'current_id' => (int)$listing['id'],
];

if ($viewerId !== null) {
  $relatedSql .= ' AND l.user_id <> :viewer_id
    AND NOT EXISTS (
      SELECT 1 FROM blocked_users b
      WHERE (b.user_id = :viewer_blocker AND b.blocked_user_id = l.user_id)
         OR (b.user_id = l.user_id AND b.blocked_user_id = :viewer_blocked)
    )';
  $relatedParams['viewer_id'] = $viewerId;
  $relatedParams['viewer_blocker'] = $viewerId;
  $relatedParams['viewer_blocked'] = $viewerId;
}

$relatedSql .= ' ORDER BY l.created_at DESC LIMIT 4';
$relatedStmt = $pdo->prepare($relatedSql);
$relatedStmt->execute($relatedParams);
$relatedListings = $relatedStmt->fetchAll() ?: [];

$viewerProfile = [];
$isFavorite = false;
if ($viewerId !== null) {
  $profileStmt = $pdo->prepare('SELECT sleep_schedule, smoking_preference, pet_preference, study_habit FROM users WHERE id = ? LIMIT 1');
  $profileStmt->execute([$viewerId]);
  $viewerProfile = $profileStmt->fetch() ?: [];

  $favoriteStmt = $pdo->prepare('SELECT id FROM favorites WHERE user_id = ? AND listing_id = ? LIMIT 1');
  $favoriteStmt->execute([$viewerId, $listingId]);
  $isFavorite = (bool)$favoriteStmt->fetch();
}

$pageTitle = 'Listing Details | Roommates App';
$activePage = 'search';

require_once __DIR__ . '/partials/header.php';
?>
<section class="hero-panel p-4 p-lg-5 mb-4">
  <div class="row g-4 align-items-center position-relative" style="z-index: 1;">
    <div class="col-lg-8">
      <div class="section-kicker mb-2">Listing details</div>
      <h1 class="h2 mb-2"><?= e((string)$listing['name']) ?> in <?= e((string)$listing['city']) ?></h1>
      <p class="lead-copy mb-0">A full view of listing terms, status, and compatibility context before contacting.</p>
    </div>
    <div class="col-lg-4">
      <div class="card-soft p-4">
        <div class="badge-soft mb-2 text-capitalize"><?= e((string)$listing['status']) ?></div>
        <div class="mini-metric">
          <span class="mini-metric-value"><?= number_format((int)$listing['budget']) ?></span>
          <span class="mini-metric-label">MAD monthly budget</span>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="surface-card p-4 p-lg-5 h-100">
      <?php if (!empty($listing['image_path'])): ?>
        <img src="<?= e((string)$listing['image_path']) ?>" alt="Listing image" class="img-fluid rounded mb-4" style="max-height: 380px; width: 100%; object-fit: cover;">
      <?php endif; ?>

      <div class="d-flex flex-wrap gap-2 mb-3">
        <span class="badge-soft"><?= e((string)$listing['city']) ?></span>
        <span class="badge-soft text-capitalize"><?= e((string)$listing['plan_tier']) ?> plan</span>
        <?php if ((string)$listing['verification_status'] === 'verified'): ?>
          <span class="badge-soft">Verified</span>
        <?php endif; ?>
      </div>

      <div class="small text-muted mb-2">Move-in date: <?= e((string)$listing['move_in_date']) ?></div>
      <div class="small text-muted mb-3">Expires at: <?= e((string)($listing['expires_at'] ?? 'N/A')) ?></div>

      <?php if ($viewerId !== null && $viewerProfile): ?>
        <?php $score = compatibility_score($viewerProfile, $listing); ?>
        <div class="small mb-3"><span class="badge-soft">Compatibility score: <?= $score ?>%</span></div>
      <?php endif; ?>

      <h2 class="h5 mb-2">Preferences</h2>
      <p class="copy-muted mb-4"><?= nl2br(e((string)$listing['preferences'])) ?></p>

      <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-dark" href="search.php">Back to search</a>
        <?php
          $chatTarget = 'chat.php?receiver_id=' . (int)$listing['user_id'];
          $contactHref = $viewerId !== null ? $chatTarget : ('login.php?return_to=' . urlencode($chatTarget));
        ?>
        <a class="btn btn-outline-dark" href="<?= e($contactHref) ?>">Contact owner</a>

        <?php if ($viewerId !== null && !$isOwner): ?>
          <form method="post" action="php/toggle_favorite.php" class="m-0">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="listing_id" value="<?= (int)$listing['id'] ?>">
            <input type="hidden" name="redirect_to" value="listing.php?id=<?= (int)$listing['id'] ?>">
            <button class="btn btn-outline-dark" type="submit"><?= $isFavorite ? 'Unfavorite' : 'Favorite' ?></button>
          </form>

          <form method="post" action="php/report_listing.php" class="m-0">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="listing_id" value="<?= (int)$listing['id'] ?>">
            <button class="btn btn-outline-danger" type="submit">Report</button>
          </form>

          <form method="post" action="php/block_user.php" class="m-0">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="user_id" value="<?= (int)$listing['user_id'] ?>">
            <button class="btn btn-outline-secondary" type="submit">Block user</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="surface-card p-4 h-100">
      <div class="section-kicker mb-2">Owner summary</div>
      <h2 class="h5 mb-1"><?= e((string)$listing['name']) ?></h2>
      <div class="small text-muted mb-3"><?= e((string)$listing['city']) ?></div>

      <div class="d-grid gap-2">
        <div class="feature-card p-3">
          <div class="small text-muted mb-1">Sleep schedule</div>
          <div class="text-capitalize"><?= e((string)$listing['sleep_schedule']) ?></div>
        </div>
        <div class="feature-card p-3">
          <div class="small text-muted mb-1">Smoking preference</div>
          <div class="text-capitalize"><?= e((string)$listing['smoking_preference']) ?></div>
        </div>
        <div class="feature-card p-3">
          <div class="small text-muted mb-1">Pet preference</div>
          <div class="text-capitalize"><?= e((string)$listing['pet_preference']) ?></div>
        </div>
        <div class="feature-card p-3">
          <div class="small text-muted mb-1">Study habit</div>
          <div class="text-capitalize"><?= e((string)$listing['study_habit']) ?></div>
        </div>
      </div>
    </div>
  </div>
</div>

<section class="surface-card p-4 p-lg-5 mt-4">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
      <div class="section-kicker mb-2">Related listings</div>
      <h2 class="h5 mb-0">More options in <?= e((string)$listing['city']) ?></h2>
    </div>
    <a class="btn btn-outline-dark btn-sm" href="search.php?city=<?= urlencode((string)$listing['city']) ?>">See all in city</a>
  </div>

  <?php if (!$relatedListings): ?>
    <div class="empty-state p-3 text-center">No related listings available right now.</div>
  <?php else: ?>
    <div class="d-grid gap-2">
      <?php foreach ($relatedListings as $related): ?>
        <article class="feature-card p-3">
          <div class="d-flex justify-content-between align-items-start gap-2">
            <div>
              <div class="fw-bold"><?= e((string)$related['name']) ?></div>
              <div class="small text-muted">Move-in: <?= e((string)$related['move_in_date']) ?></div>
            </div>
            <div class="text-end">
              <div class="small text-muted">Budget</div>
              <strong><?= number_format((int)$related['budget']) ?> MAD</strong>
            </div>
          </div>
          <div class="d-flex flex-wrap gap-2 mt-2">
            <span class="badge-soft text-capitalize"><?= e((string)$related['plan_tier']) ?> plan</span>
            <?php if ((string)$related['verification_status'] === 'verified'): ?>
              <span class="badge-soft">Verified</span>
            <?php endif; ?>
          </div>
          <div class="mt-3">
            <a class="btn btn-sm btn-accent" href="listing.php?id=<?= (int)$related['id'] ?>">View</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php require_once __DIR__ . '/partials/footer.php'; ?>

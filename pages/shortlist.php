<?php
require_once __DIR__ . '/../config.php';
require_login();

$pageTitle = 'Shortlist | Roommates App';
$activePage = 'shortlist';

$favoritesStmt = $pdo->prepare('SELECT l.id, l.user_id, l.budget, l.move_in_date, l.preferences, l.status, l.expires_at, u.name, u.city, u.verification_status
  FROM favorites f
  INNER JOIN listings l ON f.listing_id = l.id
  INNER JOIN users u ON l.user_id = u.id
  WHERE f.user_id = ?
  ORDER BY f.created_at DESC');
$favoritesStmt->execute([current_user_id()]);
$favorites = $favoritesStmt->fetchAll();

$savedStmt = $pdo->prepare('SELECT id, city, budget_max, created_at FROM saved_searches WHERE user_id = ? ORDER BY created_at DESC');
$savedStmt->execute([current_user_id()]);
$savedSearches = $savedStmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>
<section class="row g-4">
  <div class="col-lg-8">
    <div class="surface-card p-4 p-lg-5 h-100">
      <div class="section-kicker mb-2">Favorites</div>
      <h1 class="h3 mb-4">Your saved listings</h1>
      <div class="d-grid gap-3">
        <?php if (!$favorites): ?>
          <div class="empty-state p-4 text-center">No favorites yet. Save listings from the search page.</div>
        <?php endif; ?>
        <?php foreach ($favorites as $fav): ?>
          <article class="feature-card p-3">
            <div class="d-flex justify-content-between align-items-center gap-2">
              <h2 class="h6 mb-0"><?= e((string)$fav['name']) ?> - <?= e((string)$fav['city']) ?></h2>
              <span class="badge-soft"><?= (int)$fav['budget'] ?> MAD</span>
            </div>
            <div class="small text-muted mb-2">Move-in: <?= e((string)$fav['move_in_date']) ?> | Status: <?= e((string)$fav['status']) ?></div>
            <p class="copy-muted mb-2"><?= e((string)$fav['preferences']) ?></p>
            <div class="d-flex gap-2">
              <a class="btn btn-sm btn-accent" href="listing.php?id=<?= (int)$fav['id'] ?>">View details</a>
              <a class="btn btn-sm btn-outline-dark" href="chat.php?receiver_id=<?= (int)$fav['user_id'] ?>">Contact</a>
              <form method="post" action="../php/toggle_favorite.php" class="m-0">
                <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="listing_id" value="<?= (int)$fav['id'] ?>">
                <input type="hidden" name="redirect_to" value="shortlist.php">
                <button class="btn btn-sm btn-outline-danger" type="submit">Remove</button>
              </form>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="surface-card p-4 h-100">
      <div class="section-kicker mb-2">Saved searches</div>
      <p class="copy-muted">Get alerts when a new listing matches your city and budget preferences.</p>
      <form method="post" action="../php/save_search.php" class="d-grid gap-2 mb-3">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="redirect_to" value="shortlist.php">
        <input class="form-control" name="city" placeholder="City" required>
        <input class="form-control" name="budget_max" type="number" min="1" placeholder="Max budget">
        <button class="btn btn-accent" type="submit">Save search</button>
      </form>
      <div class="d-grid gap-2">
        <?php foreach ($savedSearches as $saved): ?>
          <div class="feature-card p-3">
            <div class="fw-bold"><?= e((string)$saved['city']) ?></div>
            <div class="small copy-muted">Budget max: <?= e((string)($saved['budget_max'] ?? 'Any')) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
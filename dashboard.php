<?php
require_once __DIR__ . '/php/functions.php';
require_login();

$pageTitle = 'Dashboard | Roommates App';
$activePage = 'dashboard';

$stmt = $pdo->prepare('SELECT id, budget, move_in_date, preferences, status, expires_at, image_path, created_at FROM listings WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([current_user_id()]);
$listings = $stmt->fetchAll();

$unreadNotifications = unread_notification_count($pdo, (int)current_user_id());

$messagesStmt = $pdo->prepare('SELECT m.message, m.sent_at, u.name AS receiver_name
  FROM messages m
  INNER JOIN users u ON m.receiver_id = u.id
  WHERE m.sender_id = ?
  ORDER BY m.sent_at DESC
  LIMIT 3');
$messagesStmt->execute([current_user_id()]);
$recentMessages = $messagesStmt->fetchAll();

$stats = get_user_overview($pdo, (int)current_user_id());
$stats['city'] = current_user_city();
$stats['name'] = current_user_name();

require_once __DIR__ . '/partials/header.php';
?>
<section class="hero-panel p-4 p-lg-5 mb-4">
  <div class="row align-items-center g-4 position-relative" style="z-index: 1;">
    <div class="col-lg-8">
      <div class="section-kicker mb-3">Protected dashboard</div>
      <h1 class="display-5 fw-bold mb-3">Hello, <?= e($stats['name']) ?>.</h1>
      <p class="lead-copy mb-0">Your current city is <?= e($stats['city'] ?: 'not set') ?>. This workspace gives you a clear view of your listings, messages, and profile readiness.</p>
    </div>
    <div class="col-lg-4">
      <div class="card-soft p-4">
        <div class="badge-soft mb-3">Account health</div>
        <div class="mini-metric mb-3">
          <span class="mini-metric-value"><?= $stats['profile_score'] ?>%</span>
          <span class="mini-metric-label">Profile completeness</span>
        </div>
        <div class="copy-muted small mb-0">A complete profile and a live listing create a stronger first impression for prospects.</div>
      </div>
    </div>
  </div>
</section>

<div class="row g-4 mb-4">
  <div class="col-md-4">
    <div class="metric-card h-100">
      <div class="metric-value"><?= count($listings) ?></div>
      <div class="metric-label">Active listings</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="metric-card h-100">
      <div class="metric-value"><?= (int)$stats['sent_messages'] ?></div>
      <div class="metric-label">Messages sent</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="metric-card h-100">
      <div class="metric-value"><?= (int)$stats['received_messages'] ?></div>
      <div class="metric-label">Messages received</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="metric-card h-100">
      <div class="metric-value"><?= (int)$unreadNotifications ?></div>
      <div class="metric-label">Unread notifications</div>
    </div>
  </div>
</div>

<div class="d-flex flex-wrap gap-3 mb-4">
  <a class="btn btn-accent px-4" href="add_listing.php">Add listing</a>
  <a class="btn btn-outline-dark px-4" href="search.php">Search listings</a>
  <a class="btn btn-outline-dark px-4" href="shortlist.php">Shortlist</a>
  <a class="btn btn-outline-dark px-4" href="notifications.php">Notifications</a>
  <a class="btn btn-outline-dark px-4" href="profile.php">Profile</a>
  <a class="btn btn-ghost px-4" href="contact.php">Contact</a>
</div>

<section class="row g-4 mb-4">
  <div class="col-lg-8">
    <div class="surface-card p-4 p-lg-5 h-100">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
          <div class="section-kicker mb-2">Your listings</div>
          <h2 class="h3 mb-0">Published roommate offers</h2>
        </div>
        <span class="badge-soft"><?= count($listings) ?> total</span>
      </div>

      <?php if (!$listings): ?>
        <div class="empty-state p-4 text-center">
          <h3 class="h5">No listings yet</h3>
          <p class="copy-muted mb-3">Create your first listing to describe your housing preferences and budget.</p>
          <a class="btn btn-accent" href="add_listing.php">Create listing</a>
        </div>
      <?php else: ?>
        <div class="search-grid">
          <?php foreach ($listings as $listing): ?>
            <article class="listing-card p-4">
              <?php if (!empty($listing['image_path'])): ?>
                <img src="<?= e((string)$listing['image_path']) ?>" alt="Listing media" class="img-fluid rounded mb-3" style="max-height: 210px; object-fit: cover; width: 100%;">
              <?php endif; ?>
              <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                  <div class="d-flex gap-2 mb-2">
                    <span class="badge-soft">Budget</span>
                    <span class="badge-soft text-capitalize"><?= e((string)$listing['status']) ?></span>
                  </div>
                  <h3 class="h4 mb-0"><?= number_format((int)$listing['budget']) ?> MAD</h3>
                </div>
                <div class="text-end copy-muted small">
                  <div>Move-in date</div>
                  <strong><?= e((string)$listing['move_in_date']) ?></strong>
                </div>
              </div>
              <p class="copy-muted mb-3"><?= nl2br(e((string)$listing['preferences'])) ?></p>
              <div class="small text-muted mb-1">Expires at <?= e((string)$listing['expires_at']) ?></div>
              <div class="small text-muted">Created at <?= e((string)$listing['created_at']) ?></div>
              <div class="mt-3">
                <a class="btn btn-outline-dark btn-sm" href="listing.php?id=<?= (int)$listing['id'] ?>">View details</a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="surface-card p-4 h-100">
      <div class="section-kicker mb-2">Recent messages</div>
      <h2 class="h4 mb-3">Latest outreach</h2>
      <?php if (!$recentMessages): ?>
        <div class="empty-state p-4 text-center">
          <p class="copy-muted mb-0">No messages yet. Use messaging to turn interest into direct contact.</p>
        </div>
      <?php else: ?>
        <div class="d-grid gap-3">
          <?php foreach ($recentMessages as $message): ?>
            <?php $messageText = (string)$message['message']; ?>
            <?php $messagePreview = function_exists('mb_strimwidth') ? mb_strimwidth($messageText, 0, 120, '...') : (strlen($messageText) > 120 ? substr($messageText, 0, 120) . '...' : $messageText); ?>
            <div class="feature-card p-3">
              <div class="badge-soft mb-2">To <?= e((string)$message['receiver_name']) ?></div>
              <p class="mb-2 small copy-muted"><?= e($messagePreview) ?></p>
              <div class="small text-muted"><?= e((string)$message['sent_at']) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <hr class="my-4">
      <h3 class="h5 mb-3">Quick actions</h3>
      <div class="d-grid gap-2">
        <a class="btn btn-outline-dark" href="add_listing.php">Create new listing</a>
        <a class="btn btn-outline-dark" href="search.php">Search market</a>
        <a class="btn btn-outline-dark" href="chat.php">Open chat</a>
      </div>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
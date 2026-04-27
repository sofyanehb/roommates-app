<?php
require_once __DIR__ . '/php/functions.php';
require_admin('dashboard.php');

$pageTitle = 'Admin | Roommates App';
$activePage = 'admin';

$overview = get_business_overview($pdo);

$usersStmt = $pdo->query('SELECT id, name, email, city, created_at FROM users ORDER BY created_at DESC LIMIT 8');
$recentUsers = $usersStmt->fetchAll();

$listingsStmt = $pdo->query('SELECT listings.id, listings.budget, listings.move_in_date, listings.preferences, listings.created_at, users.name AS owner_name, users.city AS owner_city
  FROM listings
  INNER JOIN users ON listings.user_id = users.id
  ORDER BY listings.created_at DESC
  LIMIT 8');
$recentListings = $listingsStmt->fetchAll();

$messagesStmt = $pdo->query('SELECT messages.id, messages.message, messages.sent_at, sender.name AS sender_name, receiver.name AS receiver_name
  FROM messages
  INNER JOIN users AS sender ON messages.sender_id = sender.id
  INNER JOIN users AS receiver ON messages.receiver_id = receiver.id
  ORDER BY messages.sent_at DESC
  LIMIT 8');
$recentMessages = $messagesStmt->fetchAll();

$reportsStmt = $pdo->query('SELECT r.id, r.reason, r.status, r.created_at, u.name AS reporter_name, l.id AS listing_id
  FROM listing_reports r
  INNER JOIN users u ON r.reporter_id = u.id
  INNER JOIN listings l ON r.listing_id = l.id
  WHERE r.status = "open"
  ORDER BY r.created_at DESC
  LIMIT 8');
$openReports = $reportsStmt->fetchAll();

$verificationStmt = $pdo->query('SELECT vr.id, vr.document_url, vr.note, vr.status, vr.created_at, u.id AS user_id, u.name, u.email
  FROM verification_requests vr
  INNER JOIN users u ON vr.user_id = u.id
  WHERE vr.status = "pending"
  ORDER BY vr.created_at DESC
  LIMIT 8');
$pendingVerifications = $verificationStmt->fetchAll();

require_once __DIR__ . '/partials/header.php';
?>
<section class="hero-panel p-4 p-lg-5 mb-4 mb-lg-5">
  <div class="row align-items-center g-4 position-relative" style="z-index: 1;">
    <div class="col-lg-8">
      <div class="section-kicker mb-3">Admin moderation</div>
      <h1 class="display-5 fw-bold mb-3">Monitor activity and keep the marketplace clean.</h1>
      <p class="lead-copy mb-0">This area is for moderation, visibility, and support. It helps demonstrate that the product is not only attractive, but operationally credible.</p>
    </div>
    <div class="col-lg-4">
      <div class="card-soft p-4">
        <div class="badge-soft mb-3">Overview</div>
        <div class="d-grid gap-3">
          <div class="mini-metric"><span class="mini-metric-value"><?= $overview['users'] ?></span><span class="mini-metric-label">Users</span></div>
          <div class="mini-metric"><span class="mini-metric-value"><?= $overview['listings'] ?></span><span class="mini-metric-label">Listings</span></div>
          <div class="mini-metric"><span class="mini-metric-value"><?= $overview['messages'] ?></span><span class="mini-metric-label">Messages</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="row g-4 mb-4">
  <div class="col-md-3 col-6">
    <div class="metric-card h-100">
      <div class="metric-value"><?= $overview['users'] ?></div>
      <div class="metric-label">Total users</div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="metric-card h-100">
      <div class="metric-value"><?= $overview['listings'] ?></div>
      <div class="metric-label">Total listings</div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="metric-card h-100">
      <div class="metric-value"><?= $overview['messages'] ?></div>
      <div class="metric-label">Total messages</div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="metric-card h-100">
      <div class="metric-value"><?= $overview['cities'] ?></div>
      <div class="metric-label">Cities</div>
    </div>
  </div>
</div>

<div class="d-flex flex-wrap gap-2 mb-4">
  <a class="btn btn-outline-dark" href="admin_analytics.php">Open analytics dashboard</a>
  <form method="post" action="php/admin_export.php" class="m-0">
    <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
    <button class="btn btn-outline-dark" type="submit">Export analytics CSV</button>
  </form>
</div>

<section class="row g-4 mb-4">
  <div class="col-lg-4">
    <div class="surface-card p-4 h-100">
      <div class="section-kicker mb-2">Recent users</div>
      <div class="d-grid gap-3">
        <?php foreach ($recentUsers as $user): ?>
          <div class="feature-card p-3">
            <div class="fw-bold"><?= e((string)$user['name']) ?></div>
            <div class="small text-muted"><?= e((string)$user['email']) ?></div>
            <div class="small copy-muted"><?= e((string)$user['city']) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="surface-card p-4 h-100">
      <div class="section-kicker mb-2">Recent listings</div>
      <div class="d-grid gap-3">
        <?php foreach ($recentListings as $listing): ?>
          <div class="feature-card p-3">
            <div class="d-flex justify-content-between gap-2 mb-2">
              <div class="fw-bold"><?= e((string)$listing['owner_name']) ?></div>
              <div><?= number_format((int)$listing['budget']) ?> MAD</div>
            </div>
            <div class="small text-muted mb-2"><?= e((string)$listing['owner_city']) ?> · <?= e((string)$listing['move_in_date']) ?></div>
            <p class="small copy-muted mb-3"><?= e((string)$listing['preferences']) ?></p>
            <form method="post" action="php/admin_action.php" class="m-0" onsubmit="return confirm('Delete this listing?');">
              <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="type" value="listing">
              <input type="hidden" name="target_id" value="<?= (int)$listing['id'] ?>">
              <button class="btn btn-outline-danger btn-sm" type="submit">Delete listing</button>
            </form>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="surface-card p-4 h-100">
      <div class="section-kicker mb-2">Recent messages</div>
      <div class="d-grid gap-3">
        <?php foreach ($recentMessages as $message): ?>
          <div class="feature-card p-3">
            <div class="d-flex justify-content-between gap-2 mb-2">
              <div class="fw-bold"><?= e((string)$message['sender_name']) ?> → <?= e((string)$message['receiver_name']) ?></div>
            </div>
            <p class="small copy-muted mb-3"><?= e((string)$message['message']) ?></p>
            <form method="post" action="php/admin_action.php" class="m-0" onsubmit="return confirm('Delete this message?');">
              <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="type" value="message">
              <input type="hidden" name="target_id" value="<?= (int)$message['id'] ?>">
              <button class="btn btn-outline-danger btn-sm" type="submit">Delete message</button>
            </form>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<section class="row g-4 mb-4">
  <div class="col-lg-6">
    <div class="surface-card p-4 h-100">
      <div class="section-kicker mb-2">Open reports</div>
      <div class="d-grid gap-3">
        <?php if (!$openReports): ?>
          <div class="empty-state p-3 text-center">No open reports.</div>
        <?php endif; ?>
        <?php foreach ($openReports as $report): ?>
          <div class="feature-card p-3">
            <div class="fw-bold mb-1">Listing #<?= (int)$report['listing_id'] ?> reported by <?= e((string)$report['reporter_name']) ?></div>
            <p class="small copy-muted mb-2"><?= e((string)$report['reason']) ?></p>
            <div class="d-flex gap-2">
              <form method="post" action="php/admin_action.php" class="m-0">
                <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="type" value="report_review">
                <input type="hidden" name="target_id" value="<?= (int)$report['id'] ?>">
                <button class="btn btn-sm btn-outline-dark" type="submit">Mark reviewed</button>
              </form>
              <form method="post" action="php/admin_action.php" class="m-0">
                <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="type" value="report_dismiss">
                <input type="hidden" name="target_id" value="<?= (int)$report['id'] ?>">
                <button class="btn btn-sm btn-outline-danger" type="submit">Dismiss</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="surface-card p-4 h-100">
      <div class="section-kicker mb-2">Verification queue</div>
      <div class="d-grid gap-3">
        <?php if (!$pendingVerifications): ?>
          <div class="empty-state p-3 text-center">No pending verification requests.</div>
        <?php endif; ?>
        <?php foreach ($pendingVerifications as $item): ?>
          <div class="feature-card p-3">
            <div class="fw-bold mb-1"><?= e((string)$item['name']) ?> (<?= e((string)$item['email']) ?>)</div>
            <a href="<?= e((string)$item['document_url']) ?>" target="_blank" rel="noopener">View document</a>
            <?php if (!empty($item['note'])): ?>
              <p class="small copy-muted mb-2 mt-2"><?= e((string)$item['note']) ?></p>
            <?php endif; ?>
            <div class="d-flex gap-2">
              <form method="post" action="php/admin_action.php" class="m-0">
                <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="type" value="verify_approve">
                <input type="hidden" name="target_id" value="<?= (int)$item['id'] ?>">
                <button class="btn btn-sm btn-success" type="submit">Approve</button>
              </form>
              <form method="post" action="php/admin_action.php" class="m-0">
                <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="type" value="verify_reject">
                <input type="hidden" name="target_id" value="<?= (int)$item['id'] ?>">
                <button class="btn btn-sm btn-outline-danger" type="submit">Reject</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
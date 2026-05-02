<?php
require_once __DIR__ . '/../config.php';
require_login();

$pageTitle = 'Notifications | Roommates App';
$activePage = 'notifications';

$stmt = $pdo->
prepare('SELECT id, title, body, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC'); $stmt->execute([current_user_id()]); $notifications = $stmt->fetchAll(); require_once __DIR__ . '/../includes/header.php'; ?>
<section class="surface-card p-lg-5 p-4">
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
      <div class="section-kicker mb-2">Notification center</div>
      <h1 class="h3 mb-0">All updates in one place</h1>
    </div>
    <form method="post" action="../php/mark_all_notifications_read.php" class="m-0">
      <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>" />
      <button type="submit" class="btn btn-outline-dark">Mark all as read</button>
    </form>
  </div>
  <div class="d-grid gap-3">
    <?php if (!$notifications): ?>
    <div class="empty-state p-4 text-center">No notifications yet.</div>
    <?php endif; ?> <?php foreach ($notifications as $notification): ?>
    <article class="feature-card <?= (int)$notification['is_read'] === 0 ? 'border border-success-subtle' : '' ?> p-3">
      <div class="d-flex justify-content-between align-items-center mb-1 gap-2">
        <h2 class="h6 mb-0"><?= e((string)$notification['title']) ?></h2>
        <span class="small text-muted"><?= e((string)$notification['created_at']) ?></span>
      </div>
      <p class="copy-muted mb-2"><?= e((string)$notification['body']) ?></p>
      <?php if ((int)$notification['is_read'] === 0): ?>
      <form method="post" action="../php/mark_notification_read.php" class="m-0">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>" />
        <input type="hidden" name="id" value="<?= (int)$notification['id'] ?>" />
        <button type="submit" class="btn btn-sm btn-outline-dark">Mark as read</button>
      </form>
      <?php endif; ?>
    </article>
    <?php endforeach; ?>
  </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<?php
require_once __DIR__ . '/php/functions.php';
require_login();

$pageTitle = 'Contact | Roommates App';
$activePage = 'contact';
$receiverId = (int)($_GET['receiver_id'] ?? 0);

$usersStmt = $pdo->prepare('SELECT u.id, u.name, u.city
  FROM users u
  WHERE u.id <> ?
    AND NOT EXISTS (
      SELECT 1 FROM blocked_users b
      WHERE (b.user_id = ? AND b.blocked_user_id = u.id)
         OR (b.user_id = u.id AND b.blocked_user_id = ?)
    )
  ORDER BY u.name ASC');
$usersStmt->execute([current_user_id(), current_user_id(), current_user_id()]);
$users = $usersStmt->fetchAll();

require_once __DIR__ . '/partials/header.php';
?>
<div class="row g-4">
  <div class="col-lg-7">
    <div class="surface-card p-4 p-lg-5 h-100">
      <div class="section-kicker mb-2">Optional messaging</div>
      <h1 class="h2 mb-3">Start a direct conversation with another user.</h1>
      <p class="copy-muted mb-4">This optional module stores the message in the database and helps move users from discovery to commitment without leaving the platform.</p>
      <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="chat.php" class="btn btn-outline-dark">Open chat inbox</a>
        <?php if ($receiverId > 0): ?>
          <a href="chat.php?receiver_id=<?= $receiverId ?>" class="btn btn-ghost">Open this thread</a>
        <?php endif; ?>
      </div>
      <form action="php/contact_action.php" method="post" class="row g-3">
        <div class="col-12">
          <label class="form-label" for="receiver_id">Receiver</label>
          <select class="form-select" id="receiver_id" name="receiver_id" required>
            <option value="">Select a user</option>
            <?php foreach ($users as $user): ?>
              <option value="<?= (int)$user['id'] ?>" <?= $receiverId === (int)$user['id'] ? 'selected' : '' ?>>
                <?= e((string)$user['name']) ?> (<?= e((string)$user['city']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label" for="message">Message</label>
          <textarea class="form-control" id="message" name="message" rows="6" minlength="5" required placeholder="Introduce yourself, mention your budget, and propose next steps."></textarea>
        </div>
        <div class="col-12 d-flex flex-wrap gap-3 pt-2">
          <button type="submit" class="btn btn-accent btn-lg px-4">Send message</button>
          <a href="dashboard.php" class="btn btn-outline-dark btn-lg px-4">Back to dashboard</a>
        </div>
      </form>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="surface-card p-4 h-100">
      <div class="badge-soft mb-3">Messaging workflow</div>
      <div class="d-grid gap-3">
        <div class="feature-card p-3">
          <h2 class="h5 mb-2">1. Discover</h2>
          <p class="copy-muted mb-0">Use search filters to find a compatible listing in the right city and budget range.</p>
        </div>
        <div class="feature-card p-3">
          <h2 class="h5 mb-2">2. Contact</h2>
          <p class="copy-muted mb-0">Send a short, professional message that confirms availability and interest.</p>
        </div>
        <div class="feature-card p-3">
          <h2 class="h5 mb-2">3. Convert</h2>
          <p class="copy-muted mb-0">Move the conversation to a call or meeting once the basic criteria match.</p>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
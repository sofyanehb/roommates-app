<?php
require_once __DIR__ . '/../config.php';
require_login();

$pageTitle = 'Chat | Roommates App';
$activePage = 'chat';
$currentUserId = (int)current_user_id();
$receiverId = filter_input(INPUT_GET, 'receiver_id', FILTER_VALIDATE_INT, ['options' =>
['min_range' => 1]]); $selectedReceiverId = ($receiverId !== false && $receiverId !== null) ? (int)$receiverId : null; $conversationsStmt = $pdo->prepare('SELECT u.id, u.name, u.city, MAX(m.sent_at) AS last_at, SUM(CASE WHEN m.receiver_id = ? AND m.sender_id = u.id AND m.is_read = 0 THEN 1 ELSE 0 END) AS unread_count FROM users u INNER JOIN messages m ON ((m.sender_id = u.id AND m.receiver_id = ?) OR (m.receiver_id = u.id AND m.sender_id = ?)) WHERE u.id <> ? GROUP BY u.id, u.name, u.city ORDER BY last_at DESC'); $conversationsStmt->execute([$currentUserId, $currentUserId, $currentUserId, $currentUserId]); $conversations = $conversationsStmt->fetchAll(); if ($selectedReceiverId !== null) { $participantStmt = $pdo->prepare('SELECT id, name, city FROM users WHERE id = ? LIMIT 1'); $participantStmt->execute([$selectedReceiverId]); $selectedUser = $participantStmt->fetch(); if (!$selectedUser || is_user_blocked($pdo, $currentUserId, $selectedReceiverId)) { set_flash('danger', 'Conversation is unavailable.'); redirect('chat.php'); } $markReadStmt = $pdo->prepare('UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ? AND is_read = 0'); $markReadStmt->execute([$selectedReceiverId, $currentUserId]); $messagesStmt = $pdo->prepare('SELECT m.id, m.sender_id, m.receiver_id, m.message, m.sent_at, s.name AS sender_name, r.name AS receiver_name FROM messages m INNER JOIN users s ON m.sender_id = s.id INNER JOIN users r ON m.receiver_id = r.id WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?) ORDER BY m.sent_at ASC'); $messagesStmt->execute([$currentUserId, $selectedReceiverId, $selectedReceiverId, $currentUserId]); $messages = $messagesStmt->fetchAll(); } else { $selectedUser = null; $messages = []; } require_once __DIR__ . '/../includes/header.php'; ?>
<section class="surface-card p-lg-5 p-4">
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
      <div class="section-kicker mb-2">Direct chat</div>
      <h1 class="h3 mb-0">Inbox and conversation threads</h1>
    </div>
    <a href="contact.php" class="btn btn-outline-dark">Open contact form</a>
  </div>

  <div class="row g-4">
    <div class="col-lg-4">
      <div class="card-soft h-100 p-3">
        <h2 class="h6 mb-3">Conversations</h2>
        <div class="d-grid gap-2">
          <?php if (!$conversations): ?>
          <div class="empty-state p-3 text-center">No conversations yet.</div>
          <?php endif; ?> <?php foreach ($conversations as $conv): ?>
          <a href="chat.php?receiver_id=<?= (int)$conv['id'] ?>" class="feature-card text-decoration-none text-dark <?= $selectedReceiverId === (int)$conv['id'] ? 'border border-success-subtle' : '' ?> p-3">
            <div class="d-flex justify-content-between align-items-center">
              <strong><?= e((string)$conv['name']) ?></strong>
              <?php if ((int)$conv['unread_count'] > 0): ?>
              <span class="badge-soft"><?= (int)$conv['unread_count'] ?></span>
              <?php endif; ?>
            </div>
            <div class="small text-muted"><?= e((string)$conv['city']) ?></div>
            <div class="small text-muted mt-1"><?= e((string)$conv['last_at']) ?></div>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="card-soft h-100 p-3">
        <?php if (!$selectedUser): ?>
        <div class="empty-state p-4 text-center">Select a conversation from the left panel to view messages.</div>
        <?php else: ?>
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
          <h2 class="h6 mb-0">Chat with <?= e((string)$selectedUser['name']) ?></h2>
          <span class="small text-muted"><?= e((string)$selectedUser['city']) ?></span>
        </div>

        <div class="d-grid mb-3 gap-2" style="max-height: 420px; overflow-y: auto">
          <?php if (!$messages): ?>
          <div class="empty-state p-3 text-center">No messages yet. Start the conversation below.</div>
          <?php endif; ?> <?php foreach ($messages as $msg): ?> <?php $isMine = (int)$msg['sender_id'] === $currentUserId; ?>
          <div class="<?= $isMine ? 'bg-success-subtle ms-4' : 'bg-light me-4' ?> rounded p-3">
            <div class="small fw-bold mb-1"><?= $isMine ? 'You' : e((string)$msg['sender_name']) ?></div>
            <div class="mb-1"><?= nl2br(e((string)$msg['message'])) ?></div>
            <div class="small text-muted"><?= e((string)$msg['sent_at']) ?></div>
          </div>
          <?php endforeach; ?>
        </div>

        <form action="../php/chat_send.php" method="post" class="d-grid gap-2">
          <input type="hidden" name="receiver_id" value="<?= (int)$selectedUser['id'] ?>" />
          <textarea class="form-control" name="message" rows="3" minlength="2" required placeholder="Type your message..."></textarea>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-accent">Send</button>
            <a href="chat.php" class="btn btn-outline-dark">Clear selection</a>
          </div>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
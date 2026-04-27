<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_login('../login.php');
require_post('../chat.php');

$senderId = (int)current_user_id();
$receiverId = (int)($_POST['receiver_id'] ?? 0);
$message = trim((string)($_POST['message'] ?? ''));

if ($receiverId < 1 || strlen($message) < 2 || $receiverId === $senderId) {
    set_flash('danger', 'Invalid chat message payload.');
    redirect('../chat.php');
}

$participantStmt = $pdo->prepare('SELECT id FROM users WHERE id = ? LIMIT 1');
$participantStmt->execute([$receiverId]);
if (!$participantStmt->fetch()) {
    set_flash('danger', 'Receiver not found.');
    redirect('../chat.php');
}

if (is_user_blocked($pdo, $senderId, $receiverId)) {
    set_flash('danger', 'This conversation is blocked.');
    redirect('../chat.php');
}

$insertStmt = $pdo->prepare('INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)');
$insertStmt->execute([$senderId, $receiverId, $message]);

create_notification($pdo, $receiverId, 'New chat message', 'Open chat to view your latest message.');
log_activity($pdo, $senderId, 'chat_message_sent', 'To user #' . $receiverId);

redirect('../chat.php?receiver_id=' . $receiverId);

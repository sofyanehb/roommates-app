<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_login('../pages/login.php');
require_post('../pages/contact.php');

$senderId = (int)current_user_id();
$receiverId = (int)($_POST['receiver_id'] ?? 0);
$message = trim((string)($_POST['message'] ?? ''));

if ($receiverId < 1 || strlen($message) < 2 || $receiverId === $senderId) {
    set_flash('danger', 'Please provide a valid receiver and message.');
    redirect('../pages/contact.php');
}

$userStmt = $pdo->
prepare('SELECT id FROM users WHERE id = ? LIMIT 1'); $userStmt->execute([$receiverId]); if (!$userStmt->fetch()) { set_flash('danger', 'Selected user was not found.'); redirect('../pages/contact.php'); } if (is_user_blocked($pdo, $senderId, $receiverId)) { set_flash('danger', 'You cannot message this user.'); redirect('../pages/contact.php'); } $insertStmt = $pdo->prepare('INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)'); $insertStmt->execute([$senderId, $receiverId, $message]); create_notification($pdo, $receiverId, 'New message', 'You have received a new chat message.'); log_activity($pdo, $senderId, 'message_sent', 'Message to user #' . $receiverId); set_flash('success', 'Message sent.'); redirect('../pages/chat.php?receiver_id=' . $receiverId);

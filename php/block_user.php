<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_login('../login.php');
require_post('../search.php');
require_csrf('../search.php');

$userId = (int)current_user_id();
$blockedUserId = (int)($_POST['user_id'] ?? 0);

if ($blockedUserId < 1 || $blockedUserId === $userId) {
    set_flash('warning', 'Invalid block request.');
    redirect('../search.php');
}

$existsStmt = $pdo->prepare('SELECT id FROM users WHERE id = ? LIMIT 1');
$existsStmt->execute([$blockedUserId]);
if (!$existsStmt->fetch()) {
    set_flash('danger', 'User not found.');
    redirect('../search.php');
}

$insertStmt = $pdo->prepare('INSERT IGNORE INTO blocked_users (user_id, blocked_user_id) VALUES (?, ?)');
$insertStmt->execute([$userId, $blockedUserId]);

log_activity($pdo, $userId, 'user_blocked', 'Blocked user #' . $blockedUserId);
set_flash('success', 'User blocked successfully.');
redirect('../search.php');

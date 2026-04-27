<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_login('../login.php');
require_post('../notifications.php');
require_csrf('../notifications.php');

$userId = (int)current_user_id();
$notificationId = (int)($_POST['id'] ?? 0);

if ($notificationId < 1) {
    set_flash('warning', 'Notification not found.');
    redirect('../notifications.php');
}

$stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?');
$stmt->execute([$notificationId, $userId]);

redirect('../notifications.php');

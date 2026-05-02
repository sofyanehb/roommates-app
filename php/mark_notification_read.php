<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_login('../pages/login.php');
require_post('../pages/notifications.php');
require_csrf('../pages/notifications.php');

$userId = (int)current_user_id();
$notificationId = (int)($_POST['id'] ?? 0);

if ($notificationId < 1) {
    set_flash('warning', 'Notification not found.');
    redirect('../pages/notifications.php');
}

$stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?');
$stmt->execute([$notificationId, $userId]);

redirect('../pages/notifications.php');

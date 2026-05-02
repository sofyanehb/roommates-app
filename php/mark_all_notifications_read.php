<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_login('../pages/login.php');
require_post('../pages/notifications.php');
require_csrf('../pages/notifications.php');

$markStmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?');
$markStmt->execute([(int)current_user_id()]);

set_flash('success', 'All notifications marked as read.');
redirect('../pages/notifications.php');

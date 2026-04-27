<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_login('../login.php');
require_post('../notifications.php');
require_csrf('../notifications.php');

$markStmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?');
$markStmt->execute([(int)current_user_id()]);

set_flash('success', 'All notifications marked as read.');
redirect('../notifications.php');

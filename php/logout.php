<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_post('../index.php');
require_csrf('../index.php');

if (is_logged_in()) {
    log_activity($pdo, current_user_id(), 'logout', 'User logged out');
}

logout_user_session();
session_start();
set_flash('success', 'You are now logged out.');
redirect('../index.php');

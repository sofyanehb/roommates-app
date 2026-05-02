<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_login('../pages/login.php');
require_post('../pages/profile.php');
require_csrf('../pages/profile.php');

$userId = (int)current_user_id();
$plan = trim((string)($_POST['plan_tier'] ?? ''));

if (!in_array($plan, ['free', 'pro', 'verified'], true)) {
    set_flash('danger', 'Invalid plan selected.');
    redirect('../pages/profile.php');
}

$stmt = $pdo->
prepare('UPDATE users SET plan_tier = ? WHERE id = ?'); $stmt->execute([$plan, $userId]); log_activity($pdo, $userId, 'plan_changed', 'Plan changed to ' . $plan); set_flash('success', 'Plan updated to ' . strtoupper($plan) . '.'); redirect('../pages/profile.php');

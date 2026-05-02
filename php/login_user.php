<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_guest('../pages/dashboard.php');
require_post('../pages/login.php');

$email = strtolower(trim((string)($_POST['email'] ?? '')));
$password = (string)($_POST['password'] ?? '');
$returnTo = trim((string)($_GET['return_to'] ?? $_POST['return_to'] ?? ''));

set_old_input(['email' =>
$email]); if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') { set_flash('danger', 'Invalid email or password.'); redirect('../pages/login.php'); } $stmt = $pdo->prepare('SELECT id, name, email, city, password FROM users WHERE email = ? LIMIT 1'); $stmt->execute([$email]); $user = $stmt->fetch(); if (!$user || !password_verify($password, (string)$user['password'])) { set_flash('danger', 'Invalid email or password.'); log_activity($pdo, null, 'login_failed', 'Email: ' . $email); redirect('../pages/login.php'); } login_user_session($user); log_activity($pdo, (int)$user['id'], 'login_success', 'User logged in'); $defaultTarget = '../pages/dashboard.php'; $target = sanitize_return_to($returnTo, 'dashboard.php'); if ($target !== 'dashboard.php') { if (str_starts_with($target, '/')) { redirect($target); } if (str_starts_with($target, 'pages/')) { redirect('../' . $target); } if (strpos($target, '/pages/') !== false) { redirect('/' . ltrim($target, '/')); } redirect('../pages/' . ltrim($target, '/')); } set_flash('success', 'Welcome back, ' . (string)$user['name'] . '.'); redirect($defaultTarget);

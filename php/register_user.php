<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_guest('../dashboard.php');
require_post('../register.php');

$name = trim((string)($_POST['name'] ?? ''));
$email = strtolower(trim((string)($_POST['email'] ?? '')));
$password = (string)($_POST['password'] ?? '');
$age = (int)($_POST['age'] ?? 0);
$gender = trim((string)($_POST['gender'] ?? ''));
$city = trim((string)($_POST['city'] ?? ''));

set_old_input([
    'name' => $name,
    'email' => $email,
    'age' => (string)$age,
    'gender' => $gender,
    'city' => $city,
]);

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6 || $age < 16 || $age > 99 || $city === '') {
    set_flash('danger', 'Please provide valid registration data.');
    redirect('../register.php');
}

if (!in_array($gender, ['Male', 'Female', 'Other'], true)) {
    set_flash('danger', 'Please choose a valid gender value.');
    redirect('../register.php');
}

$existsStmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$existsStmt->execute([$email]);
if ($existsStmt->fetch()) {
    set_flash('danger', 'An account with this email already exists.');
    redirect('../register.php');
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$insertStmt = $pdo->prepare('INSERT INTO users (name, email, password, age, gender, city) VALUES (?, ?, ?, ?, ?, ?)');
$insertStmt->execute([$name, $email, $hash, $age, $gender, $city]);

$userId = (int)$pdo->lastInsertId();
log_activity($pdo, $userId, 'register', 'New account created');

$newUserStmt = $pdo->prepare('SELECT id, name, email, city FROM users WHERE id = ? LIMIT 1');
$newUserStmt->execute([$userId]);
$user = $newUserStmt->fetch();

if ($user) {
    login_user_session($user);
}

set_flash('success', 'Account created successfully. Welcome!');
redirect('../dashboard.php');

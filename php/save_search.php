<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_login('../pages/login.php');
require_post('../pages/search.php');
require_csrf('../pages/search.php');

$city = trim((string)($_POST['city'] ?? ''));
$budgetRaw = trim((string)($_POST['budget_max'] ?? ''));
$budgetMax = ($budgetRaw !== '' && is_numeric($budgetRaw)) ? (int)$budgetRaw : null;
$redirectTo = sanitize_return_to((string)($_POST['redirect_to'] ?? ''), 'search.php');

if ($city === '' && $budgetMax === null) {
    set_flash('warning', 'Provide city or max budget to save a search.');
    redirect('../pages/search.php');
}

$stmt = $pdo->
prepare('INSERT INTO saved_searches (user_id, city, budget_max) VALUES (?, ?, ?)'); $stmt->execute([(int)current_user_id(), $city, $budgetMax]); log_activity($pdo, (int)current_user_id(), 'search_saved', 'City: ' . $city . '; Budget max: ' . ($budgetMax ?? 'any')); set_flash('success', 'Search preferences saved.'); redirect('../' . $redirectTo);

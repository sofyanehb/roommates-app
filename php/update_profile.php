<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_login('../pages/login.php');
require_post('../pages/profile.php');
require_csrf('../pages/profile.php');

$userId = (int)current_user_id();
$sleep = trim((string)($_POST['sleep_schedule'] ?? ''));
$smoking = trim((string)($_POST['smoking_preference'] ?? ''));
$pet = trim((string)($_POST['pet_preference'] ?? ''));
$study = trim((string)($_POST['study_habit'] ?? ''));

if (!in_array($sleep, ['early', 'late', 'flexible'], true)
    || !in_array($smoking, ['no', 'yes', 'occasionally'], true)
    || !in_array($pet, ['no_pets', 'pets_ok', 'pet_owner'], true)
    || !in_array($study, ['quiet', 'moderate', 'social'], true)) {
    set_flash('danger', 'Invalid profile preferences.');
    redirect('../pages/profile.php');
}

$stmt = $pdo->
prepare('UPDATE users SET sleep_schedule = ?, smoking_preference = ?, pet_preference = ?, study_habit = ? WHERE id = ?'); $stmt->execute([$sleep, $smoking, $pet, $study, $userId]); $_SESSION['user']['city'] = current_user_city(); log_activity($pdo, $userId, 'profile_updated', 'Compatibility preferences updated'); set_flash('success', 'Profile preferences updated.'); redirect('../pages/profile.php');

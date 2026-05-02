<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_login('../pages/login.php');
require_post('../pages/search.php');
require_csrf('../pages/search.php');

$userId = (int)current_user_id();
$listingId = (int)($_POST['listing_id'] ?? 0);
$redirectTo = sanitize_return_to((string)($_POST['redirect_to'] ?? ''), 'search.php');

if ($listingId < 1) {
    set_flash('danger', 'Listing not found.');
    redirect('../pages/search.php');
}

$ownerStmt = $pdo->
prepare('SELECT user_id FROM listings WHERE id = ? LIMIT 1'); $ownerStmt->execute([$listingId]); $listingOwner = $ownerStmt->fetchColumn(); if (!$listingOwner) { set_flash('danger', 'Listing no longer exists.'); redirect('../pages/search.php'); } $checkStmt = $pdo->prepare('SELECT id FROM favorites WHERE user_id = ? AND listing_id = ? LIMIT 1'); $checkStmt->execute([$userId, $listingId]); $current = $checkStmt->fetch(); if ($current) { $deleteStmt = $pdo->prepare('DELETE FROM favorites WHERE id = ?'); $deleteStmt->execute([(int)$current['id']]); set_flash('info', 'Listing removed from favorites.'); log_activity($pdo, $userId, 'favorite_removed', 'Listing #' . $listingId); } else { $insertStmt = $pdo->prepare('INSERT INTO favorites (user_id, listing_id) VALUES (?, ?)'); $insertStmt->execute([$userId, $listingId]); set_flash('success', 'Listing added to favorites.'); log_activity($pdo, $userId, 'favorite_added', 'Listing #' . $listingId); if ((int)$listingOwner !== $userId) { create_notification($pdo, (int)$listingOwner, 'New favorite', 'Someone added your listing to favorites.'); } } redirect('../' . $redirectTo);

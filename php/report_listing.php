<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_login('../pages/login.php');
require_post('../pages/search.php');
require_csrf('../pages/search.php');

$userId = (int)current_user_id();
$listingId = (int)($_POST['listing_id'] ?? 0);
$reason = trim((string)($_POST['reason'] ?? 'Suspicious listing'));

if ($listingId < 1) {
    set_flash('danger', 'Invalid listing report.');
    redirect('../pages/search.php');
}

$listingStmt = $pdo->
prepare('SELECT id, user_id FROM listings WHERE id = ? LIMIT 1'); $listingStmt->execute([$listingId]); $listing = $listingStmt->fetch(); if (!$listing) { set_flash('danger', 'Listing not found.'); redirect('../pages/search.php'); } if ((int)$listing['user_id'] === $userId) { set_flash('warning', 'You cannot report your own listing.'); redirect('../pages/search.php'); } $checkStmt = $pdo->prepare('SELECT id FROM listing_reports WHERE listing_id = ? AND reporter_id = ? AND status = "open" LIMIT 1'); $checkStmt->execute([$listingId, $userId]); if ($checkStmt->fetch()) { set_flash('info', 'You already reported this listing.'); redirect('../pages/search.php'); } $insertStmt = $pdo->prepare('INSERT INTO listing_reports (listing_id, reporter_id, reason) VALUES (?, ?, ?)'); $insertStmt->execute([$listingId, $userId, $reason]); log_activity($pdo, $userId, 'listing_reported', 'Listing #' . $listingId); set_flash('success', 'Listing reported. Our team will review it.'); redirect('../pages/search.php');

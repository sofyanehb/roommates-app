<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_login('../login.php');
require_post('../profile.php');
require_csrf('../profile.php');

$userId = (int)current_user_id();
$documentUrl = trim((string)($_POST['document_url'] ?? ''));
$note = trim((string)($_POST['note'] ?? ''));

if (!filter_var($documentUrl, FILTER_VALIDATE_URL)) {
    set_flash('danger', 'Please provide a valid document URL.');
    redirect('../profile.php');
}

$insertStmt = $pdo->prepare('INSERT INTO verification_requests (user_id, document_url, note, status) VALUES (?, ?, ?, "pending")');
$insertStmt->execute([$userId, $documentUrl, $note]);

$updateUserStmt = $pdo->prepare('UPDATE users SET verification_status = "pending" WHERE id = ?');
$updateUserStmt->execute([$userId]);

log_activity($pdo, $userId, 'verification_requested', 'Verification request submitted');
set_flash('success', 'Verification request submitted.');
redirect('../profile.php');

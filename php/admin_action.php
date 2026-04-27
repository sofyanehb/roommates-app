<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_admin('../dashboard.php', '../login.php');
require_post('../admin.php');
require_csrf('../admin.php');

$type = trim((string)($_POST['type'] ?? ''));
$targetId = (int)($_POST['target_id'] ?? 0);
$adminId = (int)current_user_id();

if ($type === '' || $targetId < 1) {
    set_flash('warning', 'Invalid admin action request.');
    redirect('../admin.php');
}

switch ($type) {
    case 'listing':
        $stmt = $pdo->prepare('DELETE FROM listings WHERE id = ?');
        $stmt->execute([$targetId]);
        log_activity($pdo, $adminId, 'admin_delete_listing', 'Listing #' . $targetId);
        set_flash('success', 'Listing deleted.');
        break;

    case 'message':
        $stmt = $pdo->prepare('DELETE FROM messages WHERE id = ?');
        $stmt->execute([$targetId]);
        log_activity($pdo, $adminId, 'admin_delete_message', 'Message #' . $targetId);
        set_flash('success', 'Message deleted.');
        break;

    case 'report_review':
        $stmt = $pdo->prepare('UPDATE listing_reports SET status = "reviewed" WHERE id = ?');
        $stmt->execute([$targetId]);
        log_activity($pdo, $adminId, 'admin_review_report', 'Report #' . $targetId);
        set_flash('success', 'Report marked as reviewed.');
        break;

    case 'report_dismiss':
        $stmt = $pdo->prepare('UPDATE listing_reports SET status = "dismissed" WHERE id = ?');
        $stmt->execute([$targetId]);
        log_activity($pdo, $adminId, 'admin_dismiss_report', 'Report #' . $targetId);
        set_flash('info', 'Report dismissed.');
        break;

    case 'verify_approve':
        $requestStmt = $pdo->prepare('SELECT id, user_id FROM verification_requests WHERE id = ? LIMIT 1');
        $requestStmt->execute([$targetId]);
        $request = $requestStmt->fetch();
        if (!$request) {
            set_flash('danger', 'Verification request not found.');
            redirect('../admin.php');
        }

        $approveStmt = $pdo->prepare('UPDATE verification_requests SET status = "approved", reviewed_by = ?, reviewed_at = NOW() WHERE id = ?');
        $approveStmt->execute([$adminId, $targetId]);

        $userStmt = $pdo->prepare('UPDATE users SET verification_status = "verified", plan_tier = "verified" WHERE id = ?');
        $userStmt->execute([(int)$request['user_id']]);

        create_notification($pdo, (int)$request['user_id'], 'Verification approved', 'Your account verification request has been approved.');
        log_activity($pdo, $adminId, 'admin_verify_approve', 'Request #' . $targetId);
        set_flash('success', 'Verification approved.');
        break;

    case 'verify_reject':
        $requestStmt = $pdo->prepare('SELECT id, user_id FROM verification_requests WHERE id = ? LIMIT 1');
        $requestStmt->execute([$targetId]);
        $request = $requestStmt->fetch();
        if (!$request) {
            set_flash('danger', 'Verification request not found.');
            redirect('../admin.php');
        }

        $rejectStmt = $pdo->prepare('UPDATE verification_requests SET status = "rejected", reviewed_by = ?, reviewed_at = NOW() WHERE id = ?');
        $rejectStmt->execute([$adminId, $targetId]);

        $userStmt = $pdo->prepare('UPDATE users SET verification_status = "rejected" WHERE id = ?');
        $userStmt->execute([(int)$request['user_id']]);

        create_notification($pdo, (int)$request['user_id'], 'Verification rejected', 'Your verification request was rejected. Please submit a clearer document.');
        log_activity($pdo, $adminId, 'admin_verify_reject', 'Request #' . $targetId);
        set_flash('info', 'Verification rejected.');
        break;

    default:
        set_flash('warning', 'Unknown admin action type.');
        break;
}

redirect('../admin.php');

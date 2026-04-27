<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';
require_admin('../dashboard.php', '../login.php');
require_post('../admin.php');
require_csrf('../admin.php');

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="roommates_analytics_' . date('Ymd_His') . '.csv"');

$output = fopen('php://output', 'w');
if ($output === false) {
    exit;
}

fputcsv($output, ['metric', 'value']);
fputcsv($output, ['users_total', fetch_count($pdo, 'SELECT COUNT(*) FROM users')]);
fputcsv($output, ['listings_total', fetch_count($pdo, 'SELECT COUNT(*) FROM listings')]);
fputcsv($output, ['messages_total', fetch_count($pdo, 'SELECT COUNT(*) FROM messages')]);
fputcsv($output, ['favorites_total', fetch_count($pdo, 'SELECT COUNT(*) FROM favorites')]);
fputcsv($output, ['saved_searches_total', fetch_count($pdo, 'SELECT COUNT(*) FROM saved_searches')]);
fputcsv($output, ['open_reports', fetch_count($pdo, 'SELECT COUNT(*) FROM listing_reports WHERE status = "open"')]);

fputcsv($output, []);
fputcsv($output, ['daily_activity_date', 'active_users', 'events']);

$stmt = $pdo->query('SELECT DATE(created_at) AS day, COUNT(DISTINCT user_id) AS active_users, COUNT(*) AS total_events FROM activity_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) GROUP BY DATE(created_at) ORDER BY day ASC');
foreach ($stmt->fetchAll() as $row) {
    fputcsv($output, [(string)$row['day'], (int)$row['active_users'], (int)$row['total_events']]);
}

fclose($output);
exit;

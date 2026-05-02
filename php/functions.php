<?php
declare(strict_types=1);

// Helper functions only. Session start, timezone and PDO
// are initialized in config.php so this file avoids side-effects
// and uses injected/explicit PDO parameters for DB access.

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flashes'][] = [
        'type' => $type,
        'message' => $message,
    ];
}

function get_flashes(): array
{
    $flashes = $_SESSION['flashes'] ?? [];
    unset($_SESSION['flashes']);
    return is_array($flashes) ? $flashes : [];
}

function set_old_input(array $input): void
{
    $_SESSION['old_input'] = $input;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function is_valid_csrf_token(?string $token): bool
{
    if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        return false;
    }
    if (!is_string($token) || $token === '') {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function require_csrf(string $fallbackPath = 'index.php'): void
{
    $token = (string)($_POST['_csrf'] ?? '');
    if (is_valid_csrf_token($token)) {
        return;
    }
    set_flash('danger', 'Invalid security token. Please try again.');
    redirect($fallbackPath);
}

function old(string $key, string $default = ''): string
{
    $oldInput = $_SESSION['old_input'] ?? [];
    if (!is_array($oldInput)) {
        return $default;
    }
    return isset($oldInput[$key]) ? trim((string)$oldInput[$key]) : $default;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user']) && isset($_SESSION['user']['id']);
}

function is_action_script_request(): bool
{
    $scriptName = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? ''));
    return strpos($scriptName, '/php/') !== false || str_starts_with(ltrim($scriptName, '/'), 'php/');
}

function sanitize_return_to(string $returnTo, string $default = 'dashboard.php'): string
{
    $returnTo = trim($returnTo);
    if ($returnTo === '') {
        return $default;
    }

    if (preg_match('/^https?:\/\//i', $returnTo)) {
        return $default;
    }

    $clean = ltrim(str_replace('\\', '/', $returnTo), '/');
    if ($clean === '' || strpos($clean, '..') !== false || str_starts_with($clean, 'php/')) {
        return $default;
    }

    if (!preg_match('/^[a-zA-Z0-9_\-\.\/\?=&%]+$/', $clean)) {
        return $default;
    }

    if (!preg_match('/\.php(\?|$)/', $clean)) {
        return $default;
    }

    return $clean;
}

function require_login(string $loginPath = 'login.php'): void
{
    if (is_logged_in()) {
        return;
    }

    $returnTo = (string)($_SERVER['REQUEST_URI'] ?? '');
    $target = $loginPath;
    $requestMethod = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    if ($returnTo !== '' && strpos($loginPath, '?') === false && $requestMethod === 'GET' && !is_action_script_request()) {
        $target .= '?return_to=' . urlencode($returnTo);
    }

    set_flash('warning', 'Please log in to continue.');
    redirect($target);
}

function require_guest(string $homePath = 'dashboard.php'): void
{
    if (!is_logged_in()) {
        return;
    }
    set_flash('info', 'You are already logged in.');
    redirect($homePath);
}

function require_admin(string $fallbackPath = 'dashboard.php', string $loginPath = 'login.php'): void
{
    require_login($loginPath);
    if (is_admin()) {
        return;
    }
    set_flash('danger', 'Admin access only.');
    redirect($fallbackPath);
}

function require_post(string $fallbackPath = 'index.php'): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        return;
    }
    set_flash('warning', 'Invalid request method.');
    redirect($fallbackPath);
}

function current_user_id(): ?int
{
    return isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : null;
}

function current_user_name(): string
{
    return isset($_SESSION['user']['name']) ? (string)$_SESSION['user']['name'] : '';
}

function current_user_city(): string
{
    return isset($_SESSION['user']['city']) ? (string)$_SESSION['user']['city'] : '';
}

function current_user_email(): string
{
    return isset($_SESSION['user']['email']) ? (string)$_SESSION['user']['email'] : '';
}

function is_admin(): bool
{
    return strtolower(current_user_email()) === 'admin@roommates.local';
}

function is_user_blocked(PDO $pdo, int $viewerId, int $otherUserId): bool
{
    $stmt = $pdo->prepare('SELECT 1 FROM blocked_users WHERE (user_id = ? AND blocked_user_id = ?) OR (user_id = ? AND blocked_user_id = ?) LIMIT 1');
    $stmt->execute([$viewerId, $otherUserId, $otherUserId, $viewerId]);
    return (bool)$stmt->fetchColumn();
}

function compatibility_score(array $viewerProfile, array $listingRow): int
{
    $checks = [
        ['sleep_schedule', 25],
        ['smoking_preference', 25],
        ['pet_preference', 25],
        ['study_habit', 25],
    ];

    $score = 0;
    foreach ($checks as [$field, $weight]) {
        if (!empty($viewerProfile[$field]) && !empty($listingRow[$field]) && (string)$viewerProfile[$field] === (string)$listingRow[$field]) {
            $score += (int)$weight;
        }
    }

    return $score;
}

function fetch_count(PDO $pdo, string $sql, array $params = []): int
{
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

function unread_notification_count(PDO $pdo, int $userId): int
{
    return fetch_count($pdo, 'SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0', [$userId]);
}

function unread_message_count(PDO $pdo, int $userId): int
{
    return fetch_count($pdo, 'SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0', [$userId]);
}

function create_notification(PDO $pdo, int $userId, string $title, string $body): void
{
    $stmt = $pdo->prepare('INSERT INTO notifications (user_id, title, body) VALUES (?, ?, ?)');
    $stmt->execute([$userId, $title, $body]);
}

function log_activity(PDO $pdo, ?int $userId, string $eventType, string $details = ''): void
{
    $stmt = $pdo->prepare('INSERT INTO activity_logs (user_id, event_type, details) VALUES (?, ?, ?)');
    $stmt->execute([$userId, $eventType, $details]);
}

function get_user_overview(PDO $pdo, int $userId): array
{
    $sentMessages = fetch_count($pdo, 'SELECT COUNT(*) FROM messages WHERE sender_id = ?', [$userId]);
    $receivedMessages = fetch_count($pdo, 'SELECT COUNT(*) FROM messages WHERE receiver_id = ?', [$userId]);
    $listingCount = fetch_count($pdo, 'SELECT COUNT(*) FROM listings WHERE user_id = ?', [$userId]);

    $profile = $pdo->prepare('SELECT city, sleep_schedule, smoking_preference, pet_preference, study_habit FROM users WHERE id = ? LIMIT 1');
    $profile->execute([$userId]);
    $row = $profile->fetch() ?: [];

    $fields = ['city', 'sleep_schedule', 'smoking_preference', 'pet_preference', 'study_habit'];
    $filled = 0;
    foreach ($fields as $field) {
        if (!empty($row[$field])) {
            $filled++;
        }
    }

    $profileScore = (int)round(($filled / count($fields)) * 100);

    return [
        'sent_messages' => $sentMessages,
        'received_messages' => $receivedMessages,
        'listing_count' => $listingCount,
        'profile_score' => $profileScore,
    ];
}

function get_business_overview(PDO $pdo): array
{
    return [
        'users' => fetch_count($pdo, 'SELECT COUNT(*) FROM users'),
        'listings' => fetch_count($pdo, 'SELECT COUNT(*) FROM listings'),
        'messages' => fetch_count($pdo, 'SELECT COUNT(*) FROM messages'),
        'cities' => fetch_count($pdo, 'SELECT COUNT(DISTINCT city) FROM users'),
    ];
}

function login_user_session(array $user): void
{
    $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'name' => (string)$user['name'],
        'email' => (string)$user['email'],
        'city' => (string)$user['city'],
    ];
}

function logout_user_session(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

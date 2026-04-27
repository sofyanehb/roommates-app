<?php
declare(strict_types=1);

if (!function_exists('search_listings')) {
    function search_listings(PDO $pdo, string $city = '', string $budget = '', ?int $viewerId = null): array
    {
        $sql = 'SELECT l.id, l.user_id, l.budget, l.move_in_date, l.preferences, l.status, l.expires_at, l.image_path,
                       u.name, u.city, u.plan_tier, u.verification_status, u.sleep_schedule, u.smoking_preference, u.pet_preference, u.study_habit
                FROM listings l
                INNER JOIN users u ON l.user_id = u.id
                WHERE l.status = :status
                  AND (l.expires_at IS NULL OR l.expires_at > NOW())';

        $params = ['status' => 'open'];

        if ($city !== '') {
            $sql .= ' AND u.city LIKE :city';
            $params['city'] = '%' . $city . '%';
        }

        if ($budget !== '' && is_numeric($budget)) {
            $sql .= ' AND l.budget <= :budget';
            $params['budget'] = (int)$budget;
        }

        if ($viewerId !== null) {
            $sql .= ' AND l.user_id <> :viewer_id';
            $sql .= ' AND NOT EXISTS (
                SELECT 1 FROM blocked_users b
                WHERE (b.user_id = :viewer_blocker AND b.blocked_user_id = l.user_id)
                   OR (b.user_id = l.user_id AND b.blocked_user_id = :viewer_blocked)
            )';
            $params['viewer_id'] = $viewerId;
            $params['viewer_blocker'] = $viewerId;
            $params['viewer_blocked'] = $viewerId;
        }

        $sql .= ' ORDER BY l.created_at DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll() ?: [];
    }
}

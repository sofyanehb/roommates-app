<?php
require_once __DIR__ . '/../config.php';
require_admin('dashboard.php');

$pageTitle = 'Admin Analytics | Roommates App';
$activePage = 'admin_analytics';

$sevenDayUsersStmt = $pdo->
query('SELECT DATE(created_at) AS day, COUNT(DISTINCT user_id) AS active_users FROM activity_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY DATE(created_at) ORDER BY day ASC'); $sevenDayEventsStmt = $pdo->query('SELECT DATE(created_at) AS day, COUNT(*) AS total_events FROM activity_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY DATE(created_at) ORDER BY day ASC'); $activeUsers = $sevenDayUsersStmt->fetchAll(); $eventCounts = $sevenDayEventsStmt->fetchAll(); $conversion = [ 'users' => fetch_count($pdo, 'SELECT COUNT(*) FROM users'), 'listings' => fetch_count($pdo, 'SELECT COUNT(*) FROM listings'), 'messages' => fetch_count($pdo, 'SELECT COUNT(*) FROM messages'), ]; require_once __DIR__ . '/../includes/header.php'; ?>
<section class="surface-card p-lg-5 mb-4 p-4">
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
      <div class="section-kicker mb-2">Analytics</div>
      <h1 class="h3 mb-0">Weekly activity and conversion signals</h1>
    </div>
    <form method="post" action="../php/admin_export.php" class="m-0">
      <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>" />
      <button class="btn btn-outline-dark" type="submit">Export CSV</button>
    </form>
  </div>
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="metric-card">
        <div class="metric-value"><?= $conversion['users'] ?></div>
        <div class="metric-label">Total users</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="metric-card">
        <div class="metric-value"><?= $conversion['listings'] ?></div>
        <div class="metric-label">Total listings</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="metric-card">
        <div class="metric-value"><?= $conversion['messages'] ?></div>
        <div class="metric-label">Messages sent</div>
      </div>
    </div>
  </div>
  <div class="row g-4">
    <div class="col-lg-6">
      <div class="card-soft p-3"><canvas id="activeUsersChart" height="180"></canvas></div>
    </div>
    <div class="col-lg-6">
      <div class="card-soft p-3"><canvas id="eventsChart" height="180"></canvas></div>
    </div>
  </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
  const activeLabels = <?= json_encode(array_column($activeUsers, 'day')) ?>;
  const activeData = <?= json_encode(array_map('intval', array_column($activeUsers, 'active_users'))) ?>;
  const eventLabels = <?= json_encode(array_column($eventCounts, 'day')) ?>;
  const eventData = <?= json_encode(array_map('intval', array_column($eventCounts, 'total_events'))) ?>;

  new Chart(document.getElementById('activeUsersChart'), {
    type: 'line',
    data: {
      labels: activeLabels,
      datasets: [{
        label: 'Active users',
        data: activeData,
        borderColor: '#0f766e',
        backgroundColor: 'rgba(15, 118, 110, 0.2)',
        fill: true,
        tension: 0.35
      }]
    }
  });

  new Chart(document.getElementById('eventsChart'), {
    type: 'bar',
    data: {
      labels: eventLabels,
      datasets: [{
        label: 'Platform events',
        data: eventData,
        backgroundColor: '#c2410c'
      }]
    }
  });
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
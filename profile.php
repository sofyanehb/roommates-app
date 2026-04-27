<?php
require_once __DIR__ . '/php/functions.php';
require_login();

$pageTitle = 'Profile | Roommates App';
$activePage = 'profile';

$userStmt = $pdo->prepare('SELECT id, name, email, city, plan_tier, verification_status, sleep_schedule, smoking_preference, pet_preference, study_habit FROM users WHERE id = ? LIMIT 1');
$userStmt->execute([current_user_id()]);
$user = $userStmt->fetch();

$verificationStmt = $pdo->prepare('SELECT id, document_url, note, status, created_at FROM verification_requests WHERE user_id = ? ORDER BY created_at DESC LIMIT 1');
$verificationStmt->execute([current_user_id()]);
$verificationRequest = $verificationStmt->fetch();

require_once __DIR__ . '/partials/header.php';
?>
<section class="hero-panel p-4 p-lg-5 mb-4">
  <div class="row g-4 align-items-center position-relative" style="z-index: 1;">
    <div class="col-lg-8">
      <div class="section-kicker mb-2">Profile and plans</div>
      <h1 class="h2 mb-2">Manage your compatibility profile and subscription plan.</h1>
      <p class="lead-copy mb-0">These settings power compatibility scoring, trust badges, and premium business features.</p>
    </div>
    <div class="col-lg-4">
      <div class="card-soft p-4">
        <div class="badge-soft mb-2">Current plan</div>
        <h2 class="h4 mb-1 text-capitalize"><?= e((string)$user['plan_tier']) ?></h2>
        <div class="small copy-muted">Verification: <?= e((string)$user['verification_status']) ?></div>
      </div>
    </div>
  </div>
</section>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="surface-card p-4 p-lg-5 h-100">
      <div class="section-kicker mb-2">Compatibility preferences</div>
      <form method="post" action="php/update_profile.php" class="row g-3">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <div class="col-md-6">
          <label class="form-label" for="sleep_schedule">Sleep schedule</label>
          <select id="sleep_schedule" name="sleep_schedule" class="form-select" required>
            <option value="early" <?= $user['sleep_schedule'] === 'early' ? 'selected' : '' ?>>Early</option>
            <option value="late" <?= $user['sleep_schedule'] === 'late' ? 'selected' : '' ?>>Late</option>
            <option value="flexible" <?= $user['sleep_schedule'] === 'flexible' ? 'selected' : '' ?>>Flexible</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="smoking_preference">Smoking preference</label>
          <select id="smoking_preference" name="smoking_preference" class="form-select" required>
            <option value="no" <?= $user['smoking_preference'] === 'no' ? 'selected' : '' ?>>No</option>
            <option value="yes" <?= $user['smoking_preference'] === 'yes' ? 'selected' : '' ?>>Yes</option>
            <option value="occasionally" <?= $user['smoking_preference'] === 'occasionally' ? 'selected' : '' ?>>Occasionally</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="pet_preference">Pet preference</label>
          <select id="pet_preference" name="pet_preference" class="form-select" required>
            <option value="no_pets" <?= $user['pet_preference'] === 'no_pets' ? 'selected' : '' ?>>No pets</option>
            <option value="pets_ok" <?= $user['pet_preference'] === 'pets_ok' ? 'selected' : '' ?>>Pets ok</option>
            <option value="pet_owner" <?= $user['pet_preference'] === 'pet_owner' ? 'selected' : '' ?>>Pet owner</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="study_habit">Study habit</label>
          <select id="study_habit" name="study_habit" class="form-select" required>
            <option value="quiet" <?= $user['study_habit'] === 'quiet' ? 'selected' : '' ?>>Quiet</option>
            <option value="moderate" <?= $user['study_habit'] === 'moderate' ? 'selected' : '' ?>>Moderate</option>
            <option value="social" <?= $user['study_habit'] === 'social' ? 'selected' : '' ?>>Social</option>
          </select>
        </div>
        <div class="col-12">
          <button class="btn btn-accent" type="submit">Save profile preferences</button>
        </div>
      </form>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="surface-card p-4 mb-4">
      <div class="section-kicker mb-2">Subscription plans</div>
      <form method="post" action="php/change_plan.php" class="d-grid gap-3">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <div class="feature-card p-3">
          <input type="radio" class="form-check-input me-2" name="plan_tier" value="free" id="planFree" <?= $user['plan_tier'] === 'free' ? 'checked' : '' ?>>
          <label for="planFree" class="fw-bold">Free</label>
          <p class="small copy-muted mb-0">Basic listing and search.</p>
        </div>
        <div class="feature-card p-3">
          <input type="radio" class="form-check-input me-2" name="plan_tier" value="pro" id="planPro" <?= $user['plan_tier'] === 'pro' ? 'checked' : '' ?>>
          <label for="planPro" class="fw-bold">Pro</label>
          <p class="small copy-muted mb-0">Priority listing exposure and analytics.</p>
        </div>
        <div class="feature-card p-3">
          <input type="radio" class="form-check-input me-2" name="plan_tier" value="verified" id="planVerified" <?= $user['plan_tier'] === 'verified' ? 'checked' : '' ?>>
          <label for="planVerified" class="fw-bold">Verified</label>
          <p class="small copy-muted mb-0">Trust badge and featured placement readiness.</p>
        </div>
        <button type="submit" class="btn btn-outline-dark">Update plan</button>
      </form>
    </div>

    <div class="surface-card p-4">
      <div class="section-kicker mb-2">Verification request</div>
      <form method="post" action="php/verification_request_action.php" class="d-grid gap-3">
        <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
        <input type="url" class="form-control" name="document_url" placeholder="Document link (Google Drive, etc.)" required>
        <textarea class="form-control" name="note" rows="3" placeholder="Optional note for reviewer"></textarea>
        <button type="submit" class="btn btn-accent">Submit verification</button>
      </form>
      <?php if ($verificationRequest): ?>
        <hr>
        <div class="small copy-muted">Last request: <?= e((string)$verificationRequest['status']) ?> (<?= e((string)$verificationRequest['created_at']) ?>)</div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
<?php
require_once __DIR__ . '/../config.php';
require_login();

$pageTitle = 'Add Listing | Roommates App';
$activePage = 'add_listing';

require_once __DIR__ . '/../includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-8 col-xl-7">
    <div class="surface-card p-lg-5 p-4">
      <div class="section-kicker mb-2">New listing</div>
      <h1 class="h2 mb-3">Describe the roommate profile you are looking for.</h1>
      <p class="copy-muted mb-4">The listing will be linked to your account and shown on your dashboard after submission.</p>
      <form action="../php/add_listing_action.php" method="post" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="budget">Monthly budget</label>
          <input class="form-control" id="budget" name="budget" type="number" min="1" step="1" required />
        </div>
        <div class="col-md-6">
          <label class="form-label" for="move_in_date">Move-in date</label>
          <input class="form-control" id="move_in_date" name="move_in_date" type="date" required />
        </div>
        <div class="col-md-6">
          <label class="form-label" for="status">Listing status</label>
          <select class="form-select" id="status" name="status" required>
            <option value="open">Open</option>
            <option value="reserved">Reserved</option>
            <option value="closed">Closed</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="expires_days">Auto-expire after (days)</label>
          <input class="form-control" id="expires_days" name="expires_days" type="number" min="7" max="180" value="30" required />
        </div>
        <div class="col-12">
          <label class="form-label" for="listing_image">Listing image (optional)</label>
          <input class="form-control" id="listing_image" name="listing_image" type="file" accept="image/png,image/jpeg,image/webp" />
        </div>
        <div class="col-12">
          <label class="form-label" for="preferences">Preferences</label>
          <textarea class="form-control" id="preferences" name="preferences" rows="6" minlength="10" required placeholder="Describe the location, lifestyle, and housing conditions you prefer."></textarea>
        </div>
        <div class="d-flex col-12 flex-wrap gap-3 pt-2">
          <button type="submit" class="btn btn-accent btn-lg px-4">Save listing</button>
          <a href="dashboard.php" class="btn btn-outline-dark btn-lg px-4">Back to dashboard</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<?php
require_once __DIR__ . '/php/functions.php';
require_guest('dashboard.php');

$pageTitle = 'Register | Roommates App';
$activePage = 'register';
$selectedGender = old('gender', 'Male');
require_once __DIR__ . '/partials/header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-8 col-xl-7">
    <div class="surface-card p-4 p-lg-5">
      <div class="section-kicker mb-2">Create account</div>
      <h1 class="h2 mb-3">Register to start publishing roommate listings.</h1>
      <p class="copy-muted mb-4">Fill in your profile details. The password is hashed before it is stored in the database.</p>
      <form action="php/register_user.php" method="post" class="row g-3">
        <div class="col-md-6">
          <label class="form-label" for="name">Full name</label>
          <input class="form-control" id="name" name="name" type="text" value="<?= e(old('name')) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="email">Email</label>
          <input class="form-control" id="email" name="email" type="email" value="<?= e(old('email')) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="password">Password</label>
          <input class="form-control" id="password" name="password" type="password" minlength="6" required>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="age">Age</label>
          <input class="form-control" id="age" name="age" type="number" min="16" max="99" value="<?= e(old('age')) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="gender">Gender</label>
          <select class="form-select" id="gender" name="gender" required>
            <option value="Male" <?= $selectedGender === 'Male' ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= $selectedGender === 'Female' ? 'selected' : '' ?>>Female</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label" for="city">City</label>
          <input class="form-control" id="city" name="city" type="text" value="<?= e(old('city')) ?>" required>
        </div>
        <div class="col-12 d-flex flex-wrap gap-3 pt-2">
          <button type="submit" class="btn btn-accent btn-lg px-4">Create account</button>
          <a href="login.php" class="btn btn-outline-dark btn-lg px-4">I already have an account</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
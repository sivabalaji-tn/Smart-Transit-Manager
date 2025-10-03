<?php
session_start();
if (isset($_SESSION['admin_logged_in'])) {
  header("Location: admin-dashboard.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login - Bus Tracker</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
  </style>
</head>
<body class="bg-dark text-light">

  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card bg-dark text-light border-secondary shadow-lg p-4" style="max-width: 400px; width: 100%;">
      <div class="text-center mb-4">
        <i class="bi bi-person-lock text-primary display-4"></i>
        <h3 class="text-light mt-2">Admin Login</h3>
        <p class="text-muted">Vehicle Manager Control Panel</p>
      </div>

      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>
          <div><?= htmlspecialchars($_GET['error']) ?></div>
        </div>
      <?php endif; ?>

      <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success d-flex align-items-center" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i>
          <div><?= htmlspecialchars($_GET['success']) ?></div>
        </div>
        <script>
          setTimeout(() => {
            window.location.href = "admin-dashboard.php";
          }, 1500);
        </script>
      <?php endif; ?>

      <form method="POST" action="admin-login-check.php">
        <div class="mb-3">
          <label class="form-label">Email Address</label>
          <div class="input-group">
           <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
            <input type="email" name="email" class="form-control" required>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input type="password" name="password" class="form-control" required>
          </div>
        </div>

        <button type="submit" class="btn btn-primary w-100">
          <i class="bi bi-box-arrow-in-right"></i> Login
        </button>
      </form>
    </div>
  </div>
</body>
</html>
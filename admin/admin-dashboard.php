<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
include '../includes/db.php';

// Check if the user is logged in as an Admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php?error=Access denied. Please log in.");
    exit;
}

// Retrieve admin name from session
$admin_name = $_SESSION['full_name'] ?? 'Administrator';

// --- Fetch Key Metrics ---
$metrics = [
    'total_routes' => 0,
    'total_buses' => 0,
    'total_drivers' => 0,
    'total_admins' => 0
];

try {
    $result_routes = $conn->query("SELECT COUNT(route_id) AS total FROM routes");
    if ($result_routes) $metrics['total_routes'] = $result_routes->fetch_assoc()['total'];

    $result_buses = $conn->query("SELECT COUNT(bus_id) AS total FROM buses WHERE status='Active'");
    if ($result_buses) $metrics['total_buses'] = $result_buses->fetch_assoc()['total'];

    $result_drivers = $conn->query("SELECT COUNT(id) AS total FROM admins WHERE role = 'Bus Driver'");
    if ($result_drivers) $metrics['total_drivers'] = $result_drivers->fetch_assoc()['total'];

    $result_admins = $conn->query("SELECT COUNT(id) AS total FROM admins WHERE role IN ('Shed Manager', 'Control Unit')");
    if ($result_admins) $metrics['total_admins'] = $result_admins->fetch_assoc()['total'];
} catch (Exception $e) {
    // Keep metrics at 0 if DB error
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
      body {
          background: linear-gradient(135deg, #1a1e22, #23272b);
          color: #f1f1f1;
      }
      .dashboard-header {
          background: #212529;
          border-bottom: 1px solid #444;
          color: #fff;
      }
      .dashboard-header h3 {
          color: #0d6efd;
      }
      .metric-card {
          background: linear-gradient(145deg, #2c3034, #262a2d);
          border-radius: 12px;
          border: 1px solid #444;
          transition: transform 0.2s, box-shadow 0.2s;
      }
      .metric-card:hover {
          transform: translateY(-4px);
          box-shadow: 0 6px 18px rgba(0,0,0,0.4);
      }
      .metric-card h5 {
          color: #adb5bd; /* lighter gray instead of muted */
          margin-top: 10px;
      }
      .metric-card h2 {
          color: #f8f9fa;
      }
      .nav-card {
          background: #2c3034;
          border-radius: 12px;
          border: 1px solid #444;
          transition: all 0.25s;
      }
      .nav-card:hover {
          box-shadow: 0 0 15px rgba(13,110,253,0.6);
          transform: translateY(-3px);
      }
      .icon-lg {
          font-size: 3rem;
      }
      .text-accent {
          color: #0d6efd;
      }
  </style>
</head>
<body>
  <header class="dashboard-header py-3 mb-4">
      <div class="container d-flex justify-content-between align-items-center">
          <h3 class="mb-0"><i class="bi bi-gear-fill me-2"></i> Admin Control Panel</h3>
          <div class="d-flex align-items-center">
              <span class="me-3 d-none d-md-inline">👋 Welcome, <strong><?= htmlspecialchars($admin_name) ?></strong></span>
              <a href="logout.php" class="btn btn-danger btn-sm">
                  <i class="bi bi-box-arrow-right"></i> Logout
              </a>
          </div>
      </div>
  </header>

  <div class="container">
      <div class="row g-4 mb-5">
          <div class="col-lg-3 col-md-6">
              <div class="metric-card p-4 text-center">
                  <i class="bi bi-signpost-split icon-lg text-success"></i>
                  <h5>Total Routes</h5>
                  <h2 class="display-5 fw-bold"><?= $metrics['total_routes'] ?></h2>
              </div>
          </div>
          <div class="col-lg-3 col-md-6">
              <div class="metric-card p-4 text-center">
                  <i class="bi bi-bus-front icon-lg text-primary"></i>
                  <h5>Active Buses</h5>
                  <h2 class="display-5 fw-bold"><?= $metrics['total_buses'] ?></h2>
              </div>
          </div>
          <div class="col-lg-3 col-md-6">
              <div class="metric-card p-4 text-center">
                  <i class="bi bi-person-badge icon-lg text-info"></i>
                  <h5>Total Drivers & Conductors</h5>
                  <h2 class="display-5 fw-bold"><?= $metrics['total_drivers'] ?></h2>
              </div>
          </div>
          <div class="col-lg-3 col-md-6">
              <div class="metric-card p-4 text-center">
                  <i class="bi bi-people icon-lg text-warning"></i>
                  <h5>System Admins</h5>
                  <h2 class="display-5 fw-bold"><?= $metrics['total_admins'] ?></h2>
              </div>
          </div>
      </div>

      <h2 class="mb-4 text-accent">⚙️ Management Modules</h2>
      <div class="row g-4">
          <div class="col-md-4">
              <a href="add-route.php" class="text-decoration-none">
                  <div class="nav-card p-4 h-100 text-center">
                      <i class="bi bi-geo-alt-fill icon-lg text-primary"></i>
                      <h4 class="mt-3 text-white">Route Creator</h4>
                      <p class="text-light small mb-0">Define new routes, stops, and schedule ETAs.</p>
                  </div>
              </a>
          </div>
          <div class="col-md-4">
              <a href="add-bus.php" class="text-decoration-none">
                  <div class="nav-card p-4 h-100 text-center">
                      <i class="bi bi-truck-flatbed icon-lg text-success"></i>
                      <h4 class="mt-3 text-white">Vehicle Assignment</h4>
                      <p class="text-light small mb-0">Assign buses to routes, drivers, and conductors.</p>
                  </div>
              </a>
          </div>
          <div class="col-md-4">
              <a href="admin-create.php" class="text-decoration-none">
                  <div class="nav-card p-4 h-100 text-center">
                      <i class="bi bi-person-fill-gear icon-lg text-warning"></i>
                      <h4 class="mt-3 text-white">User Management</h4>
                      <p class="text-light small mb-0">Create new admin, driver, and conductor accounts.</p>
                  </div>
              </a>
          </div>
      </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

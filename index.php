<?php
include 'includes/db.php';

// Get all routes
$routes = [];
$sql = "SELECT route_id, route_number, start_point, end_point FROM routes ORDER BY route_number ASC";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $routes[] = $row;
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Bus Tracking System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Animate.css -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <style>
      body {
          background: linear-gradient(135deg, #1a1e22, #2c3036);
          min-height: 100vh;
          overflow-x: hidden;
          font-family: "Segoe UI", sans-serif;
          color: #f1f1f1;
      }
      .card {
          background: #2a2f35;
          color: #fff;
          border-radius: 18px;
          padding: 2rem;
          max-width: 420px;
          width: 100%;
          border: 1px solid #444;
          box-shadow: 0 8px 20px rgba(0,0,0,0.4);
      }
      .bus-logo {
          width: 200px;
          display: block;
          margin: 0 auto 20px auto;
          position: relative;
          transform: translateX(-100%);
          animation: busEnter 1.5s forwards;
      }
      @keyframes busEnter {
          0% { transform: translateX(-100%); }
          80% { transform: translateX(15px); }
          100% { transform: translateX(0); }
      }
      @keyframes busExit {
          0% { transform: translateX(0); }
          100% { transform: translateX(120%); }
      }
      label {
          color: #f8f9fa;
          font-size: 0.95rem;
          margin-bottom: 6px;
      }
      .form-select {
          background-color: #343a40;
          color: #f8f9fa;
          border: 1px solid #555;
          border-radius: 10px;
          padding: 10px;
      }
      .form-select:focus {
          border-color: #0d6efd;
          box-shadow: 0 0 6px rgba(13,110,253,0.6);
      }
      .btn-primary {
          border-radius: 12px;
          font-weight: bold;
          transition: transform 0.2s ease, box-shadow 0.2s ease;
      }
      .btn-primary:hover {
          transform: scale(1.03);
          box-shadow: 0 6px 16px rgba(13,110,253,0.5);
      }
  </style>
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
      <div class="card text-center shadow-lg">

          <!-- Colorful Bus Image -->
          <img id="busImage" src="https://uploads.onecompiler.io/42ugmqeh6/43wpwpt89/bus_15167291.png" 
               alt="Bus" class="bus-logo">

          <h4 class="mb-4 text-white animate__animated animate__pulse animate__infinite">
              Select Your Bus & Route
          </h4>

          <form id="busForm" method="get" action="livetrack.php">
              <!-- Route Dropdown -->
              <div class="mb-3 text-start animate__animated animate__fadeInLeft">
                  <label for="routeSelect" class="form-label fw-bold">Select Route :</label>
                  <select class="form-select" id="routeSelect" name="route_id" required>
                      <option selected disabled value="">Choose Route</option>
                      <?php foreach ($routes as $r): ?>
                          <option value="<?= $r['route_id'] ?>">
                              <?= htmlspecialchars($r['route_number']) ?> 
                              (<?= htmlspecialchars($r['start_point']) ?> → <?= htmlspecialchars($r['end_point']) ?>)
                          </option>
                      <?php endforeach; ?>
                  </select>
              </div>

              <!-- Bus Dropdown -->
              <div class="mb-3 text-start animate__animated animate__fadeInRight">
                  <label for="busSelect" class="form-label fw-bold">Select Active Bus :</label>
                  <select class="form-select" id="busSelect" name="bus_id" required disabled>
                      <option selected disabled>Select Route First</option>
                  </select>
              </div>

              <button type="submit" class="btn btn-primary w-100 mt-3 animate__animated animate__bounceIn">
                  🚍 Track Bus
              </button>
          </form>
      </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
      document.addEventListener('DOMContentLoaded', function() {
          const busImage = document.getElementById("busImage");
          const routeSelect = document.getElementById("routeSelect");
          const busSelect = document.getElementById("busSelect");
          const form = document.getElementById("busForm");

          const apiPath = "admin/fetch-buses.php";

          // Load buses dynamically
          routeSelect.addEventListener("change", function() {
              const routeId = this.value;
              busSelect.disabled = true;
              busSelect.innerHTML = '<option>Loading buses...</option>';

              if (routeId) {
                  fetch(apiPath + "?route_id=" + routeId)
                      .then(res => res.json())
                      .then(data => {
                          busSelect.innerHTML = '';
                          if (data.length > 0) {
                              data.forEach((bus, index) => {
                                  const opt = document.createElement("option");
                                  opt.value = bus.bus_id;
                                  opt.textContent = bus.bus_number + " (" + bus.bus_name + ")";
                                  busSelect.appendChild(opt);
                                  if (index === 0) busSelect.value = bus.bus_id; // auto-select first
                              });
                              busSelect.disabled = false;
                          } else {
                              busSelect.innerHTML = '<option>No buses available</option>';
                          }
                      })
                      .catch(err => {
                          console.error(err);
                          busSelect.innerHTML = '<option>Error loading buses</option>';
                      });
              }
          });

          // Add exit animation before redirect
          form.addEventListener("submit", function(e) {
              if (!routeSelect.value || !busSelect.value) {
                  e.preventDefault();
                  alert("Please select a valid route and bus.");
                  return;
              }
              busImage.style.animation = "busExit 1s forwards";
          });
      });
  </script>
</body>
</html>

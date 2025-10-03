<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bus Tracking System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <style>
    body {
      background: linear-gradient(135deg, #03050e, #040811);
      font-family: Arial, sans-serif;
      overflow-x: hidden;
    }

    .card {
      border-radius: 15px;
      position: relative;
      z-index: 10;
      background: whitesmoke;
      color: black;
      padding: 1.5rem 1.5rem 2rem 1.5rem;
      overflow: hidden;
      max-width: 380px; 
      width: 100%;
    }

    .bus-logo {
      width: 180px; /* slightly smaller bus */
      display: block;
      margin: 0 auto 15px auto;
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

    .submit-btn {
      width: 100%;
      font-size: 16px;
      border-radius: 8px;
      background-color: white;
      color: #0d6efd;
      font-weight: bold;
      border: 2px solid #0d6efd;
      transition: 0.3s;
      padding: 10px;
    }

    .submit-btn:hover {
      background-color: #0d6efd;
      color: white;
      transform: scale(1.05);
    }
  </style>
</head>
<body>
  <!-- Navigation bar -->
  <nav class="navbar navbar-dark bg-dark shadow">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="#">🚍 Bus Tracking System</a>
    </div>
  </nav>

  <!-- Main Section -->
  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card text-center shadow-lg">

      <!-- Bus Image inside card -->
      <img id="busImage" src="https://uploads.onecompiler.io/42ugmqeh6/43wpwpt89/bus_15167291.png" alt="Bus" class="bus-logo">

      <h4 class="mb-4 animate_animated animatepulse animate_infinite">Select Your Bus & Route</h4>

      <!-- Form -->
      <form id="busForm">
        <div class="mb-3 text-start animate_animated animate_fadeInLeft">
          <label class="form-label fw-bold">Select Bus :</label>
          <select class="form-select">
            <option selected disabled>Choose Bus</option>
            <option value="bus1">Bus 1</option>
            <option value="bus2">Bus 2</option>
            <option value="bus3">Bus 3</option>
          </select>
        </div>

        <div class="mb-3 text-start animate_animated animate_fadeInRight">
          <label class="form-label fw-bold">Select Route :</label>
          <select class="form-select">
            <option selected disabled>Choose Route</option>
            <option value="route1">Route 1</option>
            <option value="route2">Route 2</option>
            <option value="route3">Route 3</option>
          </select>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn submit-btn animate_animated animate_bounceIn">
          SUBMIT
        </button>
      </form>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const bus = document.getElementById("busImage");

    document.getElementById("busForm").addEventListener("submit", function(e) {
      e.preventDefault();

      // Slide bus out to right inside card
      bus.style.animation = 'busExit 1s forwards';

      setTimeout(() => {
        window.location.href = "nextpage.php";
      }, 1000);
    });
  </script>
</body>
</html>
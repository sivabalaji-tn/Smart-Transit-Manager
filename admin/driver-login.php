<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bus Tracking Login</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    .login-box {
      position: relative;
      overflow: hidden;
    }
    #bus {
      width: 100px;
      position: absolute;
      bottom: 10px;
      left: 0;
      transform: translateX(-120%);
      transition: transform 10s ease-in-out;
    }
    #bus.center {
      transform: translateX(110px);
    }
    #bus.exit {
      transform: translateX(370px);
    }
  </style>
</head>
<body class="bg-dark d-flex justify-content-center align-items-center vh-100">

  <div class="card shadow-lg p-4 login-box" style="width: 360px; height: 420px;">
    <div class="text-center mb-4">
      <h4>Bus Tracker Login</h4>
    </div>

    <form onsubmit="startBusJourney(event)">
      <!-- Email -->
      <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" class="form-control" id="email" placeholder="Enter email" required>
      </div>

      <!-- Password -->
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" placeholder="Enter password" required>
      </div>

      <!-- Login Button -->
      <button id="loginBtn" type="submit" class="btn btn-primary w-100">
        Login
      </button>
    </form>

    <!-- 🚌 Your Bus PNG -->
    <img id="bus" src="https://uploads.onecompiler.io/42ugmqeh6/43wpwpt89/bus_15167291.png" alt="Bus">
  </div>

  <script>
    const bus = document.getElementById("bus");
    const loginBtn = document.getElementById("loginBtn");

    // On page load → bus drives from left to center
    window.onload = () => {
      setTimeout(() => {
        bus.classList.add("center");
      }, 300);
    };

    function startBusJourney(event) {
      event.preventDefault();

      loginBtn.innerHTML = "Loading...";
      loginBtn.disabled = true;

      // Bus leaves to right
      setTimeout(() => {
        bus.classList.remove("center");
        bus.classList.add("exit");
      }, 500);

      // Success after bus exits
      setTimeout(() => {
        loginBtn.innerHTML = "✅ Success!";
        loginBtn.classList.remove("btn-primary");
        loginBtn.classList.add("btn-success");

        // Redirect
        setTimeout(() => {
          window.location.href = "dashboard.html";
        }, 1000);
      }, 2500);
    }
  </script>
</body>
</html>

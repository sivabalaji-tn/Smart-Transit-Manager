<?php
session_start();
// Check if the driver is already logged in
if (isset($_SESSION['driver_logged_in'])) {
  header("Location: driver-dashboard.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Driver Portal Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        /* Base Styling - Dark Theme */
        body {
            background-color: #1a1e22 !important; 
        }
        .login-card {
            background-color: #212529; /* Dark Card Background */
            border-radius: 10px;
            border: 1px solid #444;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.6);
        }
        /* Input Field Styling for Dark Theme */
        .input-group-text {
            background-color: #2d3136;
            border: 1px solid #444;
            color: #0d6efd; /* Blue Icon Color */
        }
        .form-control {
            background-color: #fff; /* White input fields */
            color: #000;
            border: 1px solid #ccc;
        }

        /* Bus Animation Styles - RE-INTEGRATED AND FIXED */
        .login-box {
            position: relative;
            overflow: hidden; /* Crucial for animation */
        }
        #bus {
            width: 100px;
            position: absolute;
            bottom: 15px; 
            left: 0;
            transform: translateX(-120%); /* Off-screen start */
            transition: transform 3s ease-in-out; 
            z-index: 10;
        }
        #bus.center {
            transform: translateX(120px); 
        }
        #bus.exit {
            transform: translateX(390px); 
        }
        /* Alert Styling to float near the top */
        .top-alert {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1050;
            max-width: 90%;
            width: 400px;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger top-alert d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div><?= htmlspecialchars($_GET['error']) ?></div>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success top-alert d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div><?= htmlspecialchars($_GET['success']) ?></div>
        </div>
    <?php endif; ?>


    <div class="card login-card shadow-lg p-4 login-box" style="width: 380px; height: 530px;">
        
        <img id="bus" src="https://uploads.onecompiler.io/42ugmqeh6/43wpwpt89/bus_15167291.png" alt="Bus">

        <div class="text-center mb-4">
            <i class="bi bi-person-lock display-4 text-primary"></i>
            
            <h4 class="mt-2 text-white fw-bold">Bus Driver Login</h4>
            <p class="text-muted">Smart Transit Manager</p>
        </div>

        <form id="loginForm" action="driver-login-check.php" method="POST" onsubmit="startBusJourney(event)">
            <div class="mb-3">
                <label for="email" class="form-label text-white">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label text-white">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                </div>
            </div>

            <button id="loginBtn" type="submit" class="btn btn-primary w-100 mt-4">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </button>
        </form>
    </div>

    <script>
        const bus = document.getElementById("bus");
        const loginBtn = document.getElementById("loginBtn");
        const loginForm = document.getElementById("loginForm");
        let isFormSubmitted = false;

        // On page load → bus drives from left to center
        window.onload = () => {
            setTimeout(() => {
                bus.classList.add("center");
            }, 300);
        };

        function startBusJourney(event) {
            // Prevent immediate submission only if it's the initial submit action
            if (!isFormSubmitted) {
                event.preventDefault();
                isFormSubmitted = true;

                loginBtn.innerHTML = "loading...";
                loginBtn.disabled = true;
                
                // Bus leaves to right
                setTimeout(() => {
                    bus.classList.remove("center");
                    bus.classList.add("exit");
                }, 500);

                // Submit the form after the animation completes
                setTimeout(() => {
                    loginForm.submit(); 
                }, 2500);
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
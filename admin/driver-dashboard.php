<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
include '../includes/db.php';

// Check if the user is logged in and is a driver
if (!isset($_SESSION['driver_logged_in']) || $_SESSION['role'] !== 'Bus Driver') {
    header("Location: driver-login.php?error=Access denied. Please log in.");
    exit;
}

$driver_id = $_SESSION['user_id'];
$driver_name = $_SESSION['full_name'];

// SQL to fetch the assigned bus, route, and current STATUS/LOCATION
$dashboard_sql = "
    SELECT 
        b.bus_number,
        b.bus_id,
        b.status,             
        b.current_lat,        
        b.current_lon,
        r.route_number,
        r.start_point,
        r.end_point
    FROM 
        buses AS b
    LEFT JOIN 
        routes AS r ON b.route_id = r.route_id
    WHERE 
        b.driver_id = ? 
";
$stmt = $conn->prepare($dashboard_sql);
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$result = $stmt->get_result();
$assignment_data = $result->fetch_assoc();

$stmt->close();
$conn->close();

// Default assignment data if no bus is assigned
if (!$assignment_data) {
    $assignment_data = [
        'bus_number' => 'N/A', 
        'bus_id' => '0', 
        'status' => 'Inactive', 
        'current_lat' => '0.0', 
        'current_lon' => '0.0',
        'route_number' => 'Not Assigned', 
        'start_point' => 'N/A', 
        'end_point' => 'N/A'
    ];
}

// Pass key variables to JavaScript
$busId = json_encode($assignment_data['bus_id']);
$initialStatus = json_encode($assignment_data['status']);
$updateUrl = 'update-bus-data.php'; 
$statusUpdateUrl = 'update-bus-status.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #1a1e22;
        }
        .dashboard-card {
            background-color: #212529;
            border-radius: 10px;
            border: 1px solid #444;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.6);
        }
        .main-info-box {
            background-color: #343a40;
            border-radius: 8px;
        }
        .trip-status-btn {
            height: 150px;
            font-size: 1.8rem;
            font-weight: bold;
            transition: all 0.3s;
        }
        .emergency-btn {
            background-color: #dc3545; /* Bootstrap Danger Red */
            border-color: #dc3545;
            color: #fff;
            font-size: 1.5rem;
        }
        .emergency-btn:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        .time-box {
            font-size: 2.5rem;
            font-weight: 700;
            color: #0d6efd; /* Blue Accent */
        }
        .toggle-label {
            font-weight: 500;
            color: #f8f9fa;
        }
    </style>
</head>
<body class="bg-dark text-light py-5">
    <div class="container">
        <div class="dashboard-card p-4 mx-auto" style="max-width: 900px;">
            
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
                <h4 class="text-white mb-0">
                    <i class="bi bi-person-circle me-2 text-primary"></i> Driver Dashboard
                </h4>
                
                <div class="d-flex align-items-center">
                    <span class="me-3 d-none d-md-inline">Welcome Driver Mr/Mrs, <?= htmlspecialchars($driver_name) ?></span>
                    <a href="logout.php" class="btn btn-danger btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>

            <div class="text-center mb-4">
                <div class="time-box" id="live-clock"></div>
            </div>

            <div class="main-info-box p-3 mb-4">
                <h5 class="text-primary"><i class="bi bi-bus-front me-2"></i>Current Assignment</h5>
                <div class="row text-white small">
                    <div class="col-md-4">Bus No: <span class="fw-bold"><?= htmlspecialchars($assignment_data['bus_number']) ?></span></div>
                    <div class="col-md-8">Route: <span class="fw-bold"><?= htmlspecialchars($assignment_data['route_number'] ?: 'Not Assigned') ?></span> (<?= htmlspecialchars($assignment_data['start_point'] ?: 'N/A') ?> &rarr; <?= htmlspecialchars($assignment_data['end_point'] ?: 'N/A') ?>)</div>
                </div>
            </div>

            <div class="row g-4">
                
                <div class="col-md-6">
                    <button id="trip-toggle-btn" class="trip-status-btn btn btn-success w-100" 
                        data-status="off" onclick="toggleTripStatus()">
                        <i class="bi bi-play-circle me-2"></i> START BUS TRIP
                    </button>
                </div>

                <div class="col-md-6">
                    <div class="row g-3">
                        <div class="col-12">
                            <button class="emergency-btn btn w-100" onclick="handleMaintenanceStatus('Emergency')">
                                <i class="bi bi-megaphone-fill me-2"></i> EMERGENCY CONTACT
                            </button>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-warning w-100" onclick="handleMaintenanceStatus('Backup')">
                                <i class="bi bi-tools me-2"></i> BACKUP / SPARE BUS
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        // GLOBAL VARIABLES 
        let locationSenderInterval = null; 
        let watchId = null; 
        let latestLocation = { lat: null, lon: null }; 

        const busId = <?= $busId ?>; 
        const initialStatus = <?= $initialStatus ?>;
        const updateUrl = 'update-bus-data.php'; 
        const tripToggleBtn = document.getElementById('trip-toggle-btn');


        // LIVE CLOCK FUNCTIONALITY
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' });
            document.getElementById('live-clock').textContent = timeString;
        }
        setInterval(updateClock, 1000);
        updateClock(); // Initial call


        // --- API CALL HELPERS ---

        function sendStatusUpdate(status) {
            const data = `bus_id=${busId}&status=${status}`;
            fetch(updateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: data
            })
            .catch(error => console.error('Error updating status:', error));
        }

        function sendLocationUpdate(lat, lon) {
            const data = `bus_id=${busId}&lat=${lat}&lon=${lon}`;
            fetch(updateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: data
            })
            .catch(error => console.error('Error sending location:', error));
        }


        // --- GPS TRACKING LOGIC ---

        function startLocationWatch() {
            if (navigator.geolocation && busId !== '0') {
                const success = (position) => {
                    latestLocation.lat = position.coords.latitude;
                    latestLocation.lon = position.coords.longitude;
                };

                const error = (err) => {
                    console.error("Geolocation Watch Error:", err.message);
                    alert("GPS Error: Tracking has stopped due to loss of signal or permission.");
                    stopTrackingAndTrip(); 
                };

                const options = {
                    enableHighAccuracy: true,
                    timeout: 5000,
                    maximumAge: 0 
                };

                watchId = navigator.geolocation.watchPosition(success, error, options);

                locationSenderInterval = setInterval(() => {
                    if (latestLocation.lat !== null) {
                        sendLocationUpdate(latestLocation.lat, latestLocation.lon);
                    }
                }, 1000); 
                
                return true;
            }
            return false;
        }

        function stopTrackingAndTrip() {
            if (watchId !== null) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
            }
            if (locationSenderInterval !== null) {
                clearInterval(locationSenderInterval);
                locationSenderInterval = null;
            }

            // Send a final update with 0.0, 0.0 to indicate bus is offline
            sendLocationUpdate('0.0', '0.0'); 

            // Update UI and DB status
            tripToggleBtn.setAttribute('data-status', 'off');
            tripToggleBtn.classList.remove('btn-danger');
            tripToggleBtn.classList.add('btn-success');
            tripToggleBtn.innerHTML = '<i class="bi bi-play-circle me-2"></i> START BUS TRIP';
            
            // Set status to Inactive in DB
            sendStatusUpdate('Inactive');
        }


        // --- MAIN TOGGLE FUNCTIONS ---

        function initializeDashboard() {
            if (initialStatus === 'Active') {
                // Initialize button to RUNNING state
                tripToggleBtn.setAttribute('data-status', 'on');
                tripToggleBtn.classList.remove('btn-success');
                tripToggleBtn.classList.add('btn-danger');
                tripToggleBtn.innerHTML = '<i class="bi bi-stop-circle me-2"></i> END BUS TRIP';
                
                // Restart location tracking on refresh
                startLocationWatch(); 
            } else if (initialStatus === 'Maintenance') {
                 // Initialize button to INACTIVE state 
                tripToggleBtn.setAttribute('data-status', 'off');
                tripToggleBtn.classList.remove('btn-success');
                tripToggleBtn.classList.add('btn-secondary');
                tripToggleBtn.disabled = true;
                
                // Disable the emergency/backup buttons as well to prevent re-submitting maintenance status
                document.querySelectorAll('.emergency-btn, .btn-warning').forEach(btn => {
                    btn.disabled = true;
                });
            } else {
                // Default Inactive state
                tripToggleBtn.setAttribute('data-status', 'off');
                tripToggleBtn.classList.remove('btn-danger');
                tripToggleBtn.classList.add('btn-success');
                tripToggleBtn.innerHTML = '<i class="bi bi-play-circle me-2"></i> START BUS TRIP';
            }
        }
        document.addEventListener('DOMContentLoaded', initializeDashboard);


        function toggleTripStatus() {
            const status = tripToggleBtn.getAttribute('data-status');

            if (busId === '0') {
                alert("Error: No bus is currently assigned to your account. Cannot start trip.");
                return;
            }
            
            // If maintenance status is active, prevent starting the trip
            if (initialStatus === 'Maintenance') {
                alert("Cannot start trip: Bus is currently marked as Maintenance.");
                return;
            }

            if (status === 'off') {
                // START TRIP LOGIC
                if (startLocationWatch()) {
                    // Update UI and DB status
                    tripToggleBtn.setAttribute('data-status', 'on');
                    tripToggleBtn.classList.remove('btn-success');
                    tripToggleBtn.classList.add('btn-danger');
                    tripToggleBtn.innerHTML = '<i class="bi bi-stop-circle me-2"></i> END BUS TRIP';
                    alert('Bus Trip Started. Location tracking active!');
                    sendStatusUpdate('Active'); 
                } else {
                     alert("Cannot start trip: Geolocation is required.");
                }

            } else {
                // END TRIP LOGIC
                stopTrackingAndTrip();
                alert('Bus Trip Ended. Daily logs can now be submitted.');
            }
        }
        
        // --- HANDLER FOR EMERGENCY/BACKUP BUTTONS ---
        function handleMaintenanceStatus(buttonType) {
             if (busId === '0') {
                alert(`Error: No bus is currently assigned. Cannot set ${buttonType} status.`);
                return;
            }
            
            // If trip is currently active, stop it first before setting maintenance
            if (tripToggleBtn.getAttribute('data-status') === 'on') {
                stopTrackingAndTrip();
            }

            // Set status to Maintenance in DB
            sendStatusUpdate('Maintenance');
            
            // Visually disable the Start Trip button
            tripToggleBtn.disabled = true;
            tripToggleBtn.classList.remove('btn-success');
            tripToggleBtn.classList.add('btn-secondary');
            
            // Disable the emergency/backup buttons as well to prevent re-submitting maintenance status
            document.querySelectorAll('.emergency-btn, .btn-warning').forEach(btn => {
                btn.disabled = true;
            });

            alert(`${buttonType} Status Initiated. Bus status set to Maintenance. Contact Control Unit to revert.`);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
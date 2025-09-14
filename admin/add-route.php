<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
include '../includes/db.php';

if (isset($_POST['submit'])) {
    $route_name = htmlspecialchars($_POST['route_name']);
    $start_location = htmlspecialchars($_POST['start_location']);
    $end_location = htmlspecialchars($_POST['end_location']);
    $created_at = date('Y-m-d H:i:s');

    $conn->begin_transaction();

    try {
        // Insert route
        $sql = "INSERT INTO routes (route_number, start_point, end_point, created_at) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $route_name, $start_location, $end_location, $created_at);
        $stmt->execute();
        
        $route_id = $conn->insert_id;
        $stmt->close();

        // Insert stops
        if (isset($_POST['stop_name']) && is_array($_POST['stop_name'])) {
            $sql_stops = "INSERT INTO route_stops (route_id, stop_name, sequence_number, latitude, longitude, timestamp) 
                          VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_stops = $conn->prepare($sql_stops);
            
            $sequence = 1;
            foreach ($_POST['stop_name'] as $index => $stop_name) {
                $stop_name = htmlspecialchars(trim($stop_name));
                $latitude  = htmlspecialchars(trim($_POST['latitude'][$index]));
                $longitude = htmlspecialchars(trim($_POST['longitude'][$index]));
                $eta       = htmlspecialchars(trim($_POST['estimated_arrival_time'][$index]));

                if (!empty($stop_name) && !empty($latitude) && !empty($longitude)) {
                    // ✅ Correct bind_param types:
                    // i = integer, s = string, d = double (float)
                    // If latitude/longitude are VARCHAR in DB, replace "d d" with "s s"
                    $stmt_stops->bind_param("isidss", 
                        $route_id,   // i
                        $stop_name,  // s
                        $sequence,   // i
                        $latitude,   // d (or s if varchar)
                        $longitude,  // d (or s if varchar)
                        $eta         // s
                    );
                    
                    $stmt_stops->execute();
                    $sequence++;
                }
            }
            $stmt_stops->close();
        }
        
        $conn->commit();
        
        $_SESSION['message'] = 'Route added successfully!';
        $_SESSION['alert_type'] = 'success';

    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        
        $_SESSION['message'] = 'Error adding route: ' . $e->getMessage();
        $_SESSION['alert_type'] = 'danger';
    }

    $conn->close();
    header("Location: add-route.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Route</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .center-alert {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1050;
            width: 80%;
            max-width: 400px;
            text-align: center;
        }
    </style>
</head>
<a href="admin-dashboard.php" class="btn btn-primary position-fixed top-0 end-0 m-4 rounded-circle shadow-lg"
   style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;" 
   title="Go to Dashboard">
 <i class="bi bi-house-door-fill text-white fs-5"></i>
</a>
<body class="bg-dark text-light">
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['alert_type']; ?> alert-dismissible fade show center-alert" role="alert">
            <?php echo $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
            unset($_SESSION['message']);
            unset($_SESSION['alert_type']);
        ?>
    <?php endif; ?>
    
    <div class="container mt-5">
        <h1 class="text-white mb-4 text-center">Add Route</h1>
        
        <form action="add-route.php" method="POST" class="col-lg-8 mx-auto">
            <div class="row g-3">
                <div class="col-12 mb-3">
                    <label for="route_name" class="form-label">Route Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                        <input type="text" class="form-control" id="route_name" name="route_name" required>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="start_location" class="form-label">Start Location</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-arrow-right-circle-fill"></i></span>
                        <input type="text" class="form-control" id="start_location" name="start_location" required>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="end_location" class="form-label">End Location</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-arrow-left-circle-fill"></i></span>
                        <input type="text" class="form-control" id="end_location" name="end_location" required>
                    </div>
                </div>
                
                <hr class="my-3">
                
                <div class="col-12 mb-3">
                    <label for="num_stops" class="form-label">Number of Stops</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-list-ol"></i></span>
                        <input type="number" class="form-control" id="num_stops" min="1" max="50" value="1">
                    </div>
                </div>

                <div class="col-12" id="stops-container"></div>
            </div>
            
            <button type="submit" name="submit" class="btn btn-primary w-100 py-3 mt-4">Add Route</button>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const numStopsInput = document.getElementById('num_stops');
            const stopsContainer = document.getElementById('stops-container');

            function generateStopInputs(count) {
                stopsContainer.innerHTML = ''; 
                for (let i = 0; i < count; i++) {
                    const stopRow = document.createElement('div');
                    stopRow.className = 'row g-3 mb-3';
                    stopRow.innerHTML = `
                        <div class="col-md-6">
                            <label for="stop_name_${i}" class="form-label">Stop ${i + 1} Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                                <input type="text" class="form-control" id="stop_name_${i}" name="stop_name[]" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="latitude_${i}" class="form-label">Latitude</label>
                            <input type="text" class="form-control" id="latitude_${i}" name="latitude[]" required>
                        </div>
                        <div class="col-md-3">
                            <label for="longitude_${i}" class="form-label">Longitude</label>
                            <input type="text" class="form-control" id="longitude_${i}" name="longitude[]" required>
                        </div>
                        <div class="col-12">
                            <label for="eta_${i}" class="form-label">Estimated Arrival Time</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-clock-fill"></i></span>
                                <input type="time" class="form-control" id="eta_${i}" name="estimated_arrival_time[]" required>
                            </div>
                        </div>
                    `;
                    stopsContainer.appendChild(stopRow);
                }
            }

            generateStopInputs(numStopsInput.value);

            numStopsInput.addEventListener('input', function () {
                const count = parseInt(this.value, 10);
                if (!isNaN(count) && count > 0) {
                    generateStopInputs(count);
                }
            });
        });
    </script>
</body>
</html>

<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
include '../includes/db.php';

// Check if the form has been submitted
if (isset($_POST['submit'])) {
    $route_name = htmlspecialchars($_POST['route_name']);
    $start_location = htmlspecialchars($_POST['start_location']);
    $end_location = htmlspecialchars($_POST['end_location']);
    $stops_text = $_POST['stops'];
    $created_at = date('Y-m-d H:i:s');

    // Start a database transaction
    $conn->begin_transaction();

    try {
        // 1. Insert into the routes table
        $sql = "INSERT INTO routes (route_number, start_point, end_point, created_at) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $route_name, $start_location, $end_location, $created_at);
        $stmt->execute();
        
        // Get the ID of the newly created route
        $route_id = $conn->insert_id;
        $stmt->close();

        // 2. Insert into the route_stops table
        // Split stops by new line and filter out empty lines
        $stops_array = array_filter(explode("\n", $stops_text));
        
        $sql_stops = "INSERT INTO route_stops (route_id, stop_name, sequence_number) VALUES (?, ?, ?)";
        $stmt_stops = $conn->prepare($sql_stops);
        
        $sequence = 1;
        foreach ($stops_array as $stop) {
            $stop = htmlspecialchars(trim($stop));
            if (!empty($stop)) {
                $stmt_stops->bind_param("isi", $route_id, $stop, $sequence);
                $stmt_stops->execute();
                $sequence++;
            }
        }
        $stmt_stops->close();
        
        // Commit the transaction
        $conn->commit();
        
        $_SESSION['message'] = 'Route added successfully!';
        $_SESSION['alert_type'] = 'success';

    } catch (mysqli_sql_exception $e) {
        // Rollback the transaction on error
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
        .input-group-text {
            color: #28a745;
        }
    </style>
</head>
<a href="admin-dashboard.php" class="btn btn-primary position-fixed top-0 end-0 m-4 rounded-circle shadow-lg"
   style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;" 
   title="Go to Dashboard">
 <i class="bi bi-house-door-fill text-white fs-5"></i>
</a>
<body class="bg-dark text-light">
    <?php
    if (isset($_SESSION['message'])): 
    ?>
    <div class="alert alert-<?php echo $_SESSION['alert_type']; ?> alert-dismissible fade show center-alert" role="alert">
        <?php echo $_SESSION['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php
        unset($_SESSION['message']);
        unset($_SESSION['alert_type']);
    endif;
    ?>
    
    <div class="container mt-5">
        <h1 class="text-white mb-4 text-center">Add Route</h1>
        
        <form action="add-route.php" method="POST" class="col-lg-6 mx-auto">
            <div class="row g-3">
                <div class="col-12 mb-3">
                    <label for="route_name" class="form-label">Route Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" class="form-control" id="route_name" name="route_name" required>
                    </div>
                </div>
                
                <div class="col-12 mb-3">
                    <label for="start_location" class="form-label">Start Location</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-arrow-right-circle-fill"></i></span>
                        <input type="text" class="form-control" id="start_location" name="start_location" required>
                    </div>
                </div>

                <div class="col-12 mb-3">
                    <label for="end_location" class="form-label">End Location</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-arrow-left-circle-fill"></i></span>
                        <input type="text" class="form-control" id="end_location" name="end_location" required>
                    </div>
                </div>

                <div class="col-12 mb-3">
                    <label for="stops" class="form-label">Stops (One per line)</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-list-ul"></i></span>
                        <textarea class="form-control" id="stops" name="stops" rows="5" required placeholder="Example:&#10;Periyakulam&#10;Theni Old Bus Stand&#10;Cumbum"></textarea>
                    </div>
                </div>
            </div>
            
            <button type="submit" name="submit" class="btn btn-primary w-100 py-3 mt-4">Add Route</button>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
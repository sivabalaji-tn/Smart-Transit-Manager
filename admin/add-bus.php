<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
include '../includes/db.php';

// PHP logic for form submission
if (isset($_POST['submit'])) {
    $bus_number = htmlspecialchars($_POST['bus_number']);
    $bus_name = htmlspecialchars($_POST['bus_name']);
    $route_id = htmlspecialchars($_POST['route_id']);
    $driver_id = htmlspecialchars($_POST['driver_id']);
    $conductor_id = htmlspecialchars($_POST['conductor_id']);
    $capacity = htmlspecialchars($_POST['capacity']);
    $bus_type = htmlspecialchars($_POST['bus_type']);
    $notes = htmlspecialchars($_POST['notes']);
    $created_at = date('Y-m-d H:i:s');
    $year_of_manufacture = htmlspecialchars($_POST['year_of_manufacture']);

    try {
        $sql = "INSERT INTO buses 
        (bus_number, bus_name, route_id, driver_id, conductor_id, capacity, bus_type, notes, created_at, year_of_manufacture) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiiissssi", 
            $bus_number, 
            $bus_name, 
            $route_id, 
            $driver_id, 
            $conductor_id, 
            $capacity, 
            $bus_type, 
            $notes, 
            $created_at, 
            $year_of_manufacture
        );
        
        if ($stmt->execute()) {
            $_SESSION['message'] = 'Bus added successfully!';
            $_SESSION['alert_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Error adding bus: ' . $stmt->error;
            $_SESSION['alert_type'] = 'danger';
        }

        $stmt->close();

    } catch (mysqli_sql_exception $e) {
        $_SESSION['message'] = 'Error adding bus: ' . $e->getMessage();
        $_SESSION['alert_type'] = 'danger';
    }

    $conn->close();
    header("Location: add-bus.php");
    exit();
}

// Fetch available routes for the dropdown
$routes_sql = "SELECT route_id, route_number FROM routes ORDER BY route_number ASC";
$routes_result = $conn->query($routes_sql);

// Fetch available drivers and conductors (not assigned to other buses)
$assigned_admins_sql = "SELECT driver_id, conductor_id FROM buses";
$assigned_admins_result = $conn->query($assigned_admins_sql);
$assigned_ids = [];
if ($assigned_admins_result->num_rows > 0) {
    while ($row = $assigned_admins_result->fetch_assoc()) {
        $assigned_ids[] = $row['driver_id'];
        $assigned_ids[] = $row['conductor_id'];
    }
}
$assigned_ids_str = implode(',', array_filter($assigned_ids));

$admins_sql = "SELECT id, full_name, role FROM admins WHERE role IN ('Bus Driver', 'Bus Conductor')";
if (!empty($assigned_ids_str)) {
    $admins_sql .= " AND id NOT IN ($assigned_ids_str)";
}
$admins_sql .= " ORDER BY full_name ASC";
$admins_result = $conn->query($admins_sql);

$drivers = [];
$conductors = [];
if ($admins_result->num_rows > 0) {
    while ($row = $admins_result->fetch_assoc()) {
        if ($row['role'] == 'Bus Driver') {
            $drivers[] = $row;
        } else if ($row['role'] == 'Bus Conductor') {
            $conductors[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Bus</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #0d1117;
        }
        .container {
            margin-top: 3rem;
            max-width: 900px;
        }
        .input-group-text, .form-select, .form-control {
            background-color: #212529;
            border: 1px solid #495057;
            color: #f8f9fa;
        }
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: none;
        }
        .input-group-text i {
            color: #0d6efd;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
    </style>
</head>
<body class="text-light">
    <div class="container">
        <h1 class="mb-3"><i class="bi bi-bus-front me-2"></i>Add New Bus</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['alert_type']; ?> alert-dismissible fade show" role="alert">
                <?= $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); unset($_SESSION['alert_type']); ?>
        <?php endif; ?>

        <p class="text-muted">Enter complete bus details</p>

        <form action="add-bus.php" method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="bus_number" class="form-label">Bus Number</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-bus-front"></i></span>
                        <input type="text" class="form-control" id="bus_number" name="bus_number" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="bus_name" class="form-label">Bus Name (Optional)</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-tag-fill"></i></span>
                        <input type="text" class="form-control" id="bus_name" name="bus_name">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <label for="route_id" class="form-label">Assign Route</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-arrow-down-up"></i></span>
                        <select class="form-select" id="route_id" name="route_id" required>
                            <option selected disabled>Select Route</option>
                            <?php while ($route = $routes_result->fetch_assoc()): ?>
                                <option value="<?= $route['route_id'] ?>"><?= $route['route_number'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="driver_id" class="form-label">Driver</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
                        <select class="form-select" id="driver_id" name="driver_id" required>
                            <option selected disabled>Select Driver</option>
                            <?php foreach ($drivers as $driver): ?>
                                <option value="<?= $driver['id'] ?>"><?= $driver['full_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="conductor_id" class="form-label">Conductor</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
                        <select class="form-select" id="conductor_id" name="conductor_id" required>
                            <option selected disabled>Select Conductor</option>
                            <?php foreach ($conductors as $conductor): ?>
                                <option value="<?= $conductor['id'] ?>"><?= $conductor['full_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="capacity" class="form-label">Capacity</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-people-fill"></i></span>
                        <input type="number" class="form-control" id="capacity" name="capacity" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="year_of_manufacture" class="form-label">Year of Manufacture</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-calendar-date-fill"></i></span>
                        <input type="number" class="form-control" id="year_of_manufacture" name="year_of_manufacture" required min="1950" max="<?= date('Y') ?>">
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="bus_type" class="form-label">Bus Type</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-fuel-pump-fill"></i></span>
                        <select class="form-select" id="bus_type" name="bus_type" required>
                            <option selected disabled>Select Bus Type</option>
                            <option value="Non-AC">Non-AC</option>
                            <option value="AC">AC</option>
                            <option value="EV">EV</option>
                        </select>
                    </div>
                </div>

                <div class="col-12">
                    <label for="notes" class="form-label">Notes (Optional)</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
            </div>

            <button type="submit" name="submit" class="btn btn-primary w-100 py-3 mt-4">Add Bus</button>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

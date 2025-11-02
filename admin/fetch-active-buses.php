<?php
// FILE: admin/fetch-active-buses.php

header('Content-Type: application/json');
// Check the path to db.php - it should be one level up from the admin folder
include '../includes/db.php'; 

$route_id = isset($_GET['route_id']) ? intval($_GET['route_id']) : 0;
$buses = [];

try {
    if ($route_id > 0) {
        // Query to fetch ONLY buses marked as 'Active' on the route
        $sql = "SELECT bus_id, bus_number, bus_name FROM buses WHERE route_id = ? AND status = 'Active' ORDER BY bus_number ASC"; 
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $route_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            // Include bus_name for better display in the dropdown
            $buses[] = $row;
        }
        $stmt->close();
    }
} catch (Exception $e) {
    // If the database query fails, log it and return an empty array gracefully
    // error_log("Bus fetch error: " . $e->getMessage()); 
    $buses = []; 
}

$conn->close();
echo json_encode($buses);
?>
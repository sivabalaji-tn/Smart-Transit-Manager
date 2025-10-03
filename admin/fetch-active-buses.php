<?php
// FILE: fetch-active-buses.php (Temporary Debug Version)
header('Content-Type: application/json');
include 'includes/db.php'; 

$route_id = isset($_GET['route_id']) ? intval($_GET['route_id']) : 0;
$buses = [];

if ($route_id > 0) {
    // TEMPORARY: Fetch ALL buses on the route (status filter removed for testing)
    $sql = "SELECT bus_id, bus_number FROM buses WHERE route_id = ? ORDER BY bus_number ASC"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $route_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $buses[] = $row;
    }
    $stmt->close();
}

$conn->close();
echo json_encode($buses);
?>
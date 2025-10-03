<?php
header('Content-Type: application/json');
include '../includes/db.php';

$route_id = isset($_GET['route_id']) ? intval($_GET['route_id']) : 0;

$buses = [];
if ($route_id > 0) {
    $sql = "SELECT bus_id, bus_number, bus_name 
            FROM buses 
            WHERE route_id = ? AND status = 'Active'";
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

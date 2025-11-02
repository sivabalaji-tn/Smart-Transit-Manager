<?php
// FILE: get-bus-location.php (Public Read-Only API)
header('Content-Type: application/json');
date_default_timezone_set('Asia/Kolkata');
include 'includes/db.php'; // Adjust path if necessary

$bus_id = isset($_GET['bus_id']) ? intval($_GET['bus_id']) : 0;
$response = ['bus_id' => $bus_id, 'current_lat' => 0.0, 'current_lon' => 0.0, 'status' => 'Offline', 'emergency' => false];

if ($bus_id > 0) {
    // Select the current location, status, and check for emergency flag (hypothetical column for now)
    $sql = "SELECT current_lat, current_lon, status FROM buses WHERE bus_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bus_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        
        // Populate response with actual data
        $response['current_lat'] = $row['current_lat'] ?? '0.0';
        $response['current_lon'] = $row['current_lon'] ?? '0.0';
        $response['status'] = $row['status'] ?? 'Inactive';
        
        // Simple check: if status is Maintenance, we might flag it for the user view as a warning
        if ($response['status'] === 'Maintenance') {
            $response['status'] = 'Maintenance'; // Explicitly keep maintenance status
        }

        // NOTE: The emergency flag (if present in the DB) would be checked here.
        // For now, we leave it as false.

    }
    $stmt->close();
}

$conn->close();

echo json_encode($response);
?>
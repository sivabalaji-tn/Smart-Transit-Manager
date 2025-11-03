<?php
// FILE: get-bus-location.php
date_default_timezone_set('Asia/Kolkata');
// Ensure this path to db.php is correct. If this file is in the root, it should be 'includes/db.php'.
include 'includes/db.php'; 

header('Content-Type: application/json');

$bus_id = isset($_GET['bus_id']) ? intval($_GET['bus_id']) : 0;
$response = [
    'bus_id' => $bus_id, 
    'current_lat' => 0.0, 
    'current_lon' => 0.0, 
    'status' => 'Offline', 
    'emergency' => false
];

if ($bus_id > 0) {
    // Select the necessary columns from the buses table
    // NOTE: If you have an 'emergency' column, you must add it here.
    $sql = "SELECT current_lat, current_lon, status FROM buses WHERE bus_id = ?";
    
    // Check for connection success before preparing the statement
    if ($conn) {
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("i", $bus_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                
                // Coalesce (??) ensures we return "0.0" or "Inactive" instead of NULL
                $response['current_lat'] = $row['current_lat'] ?? '0.0';
                $response['current_lon'] = $row['current_lon'] ?? '0.0';
                $response['status'] = $row['status'] ?? 'Inactive';

                // Optional: Flag status change for the front-end
                if ($response['status'] === 'Maintenance' || $response['status'] === 'Active') {
                     // Keep the status as reported by the driver
                } else {
                    // Treat any non-standard status as offline for the user map
                    $response['status'] = 'Offline'; 
                }
            }
            $stmt->close();
        }
    }
}

// Do not suppress errors if PHP is failing before this point.
echo json_encode($response);
$conn->close();
?>
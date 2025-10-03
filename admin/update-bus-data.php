<?php
// FILE: update-bus-data.php (Consolidated API for Location and Status)
date_default_timezone_set('Asia/Kolkata');
include '../includes/db.php'; // Ensure this path is correct relative to the API file

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect Data
    $bus_id = isset($_POST['bus_id']) ? intval($_POST['bus_id']) : 0;
    
    // Status update (Optional/Conditional)
    $new_status = isset($_POST['status']) ? htmlspecialchars($_POST['status']) : null;
    $allowed_statuses = ['Active', 'Inactive', 'Maintenance'];

    // Location update (Optional/Conditional)
    $latitude = isset($_POST['lat']) ? htmlspecialchars($_POST['lat']) : null;
    $longitude = isset($_POST['lon']) ? htmlspecialchars($_POST['lon']) : null;
    
    $current_time = date('Y-m-d H:i:s');
    $response = ['status' => 'error', 'message' => 'No operation performed.'];

    if ($bus_id > 0) {
        $update_fields = [];
        $bind_types = "";
        $bind_params = [];

        // --- Build Location Update ---
        if ($latitude !== null && $longitude !== null) {
            $update_fields[] = "current_lat = ?";
            $update_fields[] = "current_lon = ?";
            $update_fields[] = "location_last_updated = ?";
            $bind_types .= "sss";
            $bind_params[] = $latitude;
            $bind_params[] = $longitude;
            $bind_params[] = $current_time;
        }

        // --- Build Status Update ---
        if ($new_status !== null && in_array($new_status, $allowed_statuses)) {
            $update_fields[] = "status = ?";
            $bind_types .= "s";
            $bind_params[] = $new_status;
        }
        
        // --- Execute Query ---
        if (!empty($update_fields)) {
            $sql = "UPDATE buses SET " . implode(', ', $update_fields) . " WHERE bus_id = ?";
            $bind_types .= "i"; // Add integer type for bus_id
            $bind_params[] = $bus_id;

            $stmt = $conn->prepare($sql);
            
            // Use call_user_func_array to bind parameters dynamically
            // The first element of the array is the type definition string.
            array_unshift($bind_params, $bind_types);
            call_user_func_array(array($stmt, 'bind_param'), $bind_params);

            if ($stmt->execute()) {
                $response = ['status' => 'success', 'message' => 'Bus data updated successfully.', 'updates' => count($update_fields)];
            } else {
                $response = ['status' => 'error', 'message' => 'SQL execution failed: ' . $stmt->error];
            }
            $stmt->close();
        } else {
            $response = ['status' => 'error', 'message' => 'No valid data provided for update.'];
        }
    } else {
        $response = ['status' => 'error', 'message' => 'Invalid Bus ID.'];
    }
}

$conn->close();
echo json_encode($response);
?>
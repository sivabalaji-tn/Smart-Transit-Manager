<?php
// FILE: update-bus-location.php (Temporary Debug)
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // If you see this output, the front-end successfully reached the API.
    echo json_encode(['status' => 'debug_received', 'data' => $_POST]);
} else {
    echo json_encode(['status' => 'debug_error', 'message' => 'Not POST']);
}
?>
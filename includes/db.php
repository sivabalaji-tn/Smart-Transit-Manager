<?php
$host = "localhost";
$user = "root";     // change if needed
$pass = "";         // change if needed
$db   = "transit_manager";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

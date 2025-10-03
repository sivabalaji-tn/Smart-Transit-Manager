<?php
session_start();

$redirect_url = 'admin-login.php';

if (isset($_SESSION['admin_logged_in'])) {
    $redirect_url = 'admin-login.php';
} elseif (isset($_SESSION['driver_logged_in'])) {
    $redirect_url = 'driver-login.php';
} else {
    $redirect_url = 'admin-login.php'; 
}

session_unset();

session_destroy();

header("Location: " . $redirect_url);
exit();
?>
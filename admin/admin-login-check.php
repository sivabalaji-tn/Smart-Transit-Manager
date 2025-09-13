<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
include '../includes/db.php';

// Check if the form was submitted via POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve user inputs
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    // Added security check: ensure fields are not empty
    if (empty($email) || empty($password)) {
        header("Location: admin-login.php?error=Email and password are required.");
        exit();
    }

    // Prepare a query to find the user in the 'admins' table
    $sql = "SELECT * FROM admins WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // This is the core verification step
        $admin_roles = ['Shed Manager', 'Control Unit', 'Vehicle Maintenance'];
        if (password_verify($password, $user['password']) && in_array($user['role'], $admin_roles)) {
            // Login is successful: create a session and redirect
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];

            // Update the last login time in the database
            $update_sql = "UPDATE admins SET last_login = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $current_time = date('Y-m-d H:i:s');
            $update_stmt->bind_param("si", $current_time, $user['id']);
            $update_stmt->execute();
            $update_stmt->close();

            // Redirect to the dashboard
            header("Location: admin-dashboard.php?success=Login successful.");
            exit();
        } else {
            // Invalid password or role, redirect with an error
            header("Location: admin-login.php?error=Invalid email or password.");
            exit();
        }
    } else {
        // No user found with that email, redirect with an error
        header("Location: admin-login.php?error=Invalid email or password.");
        exit();
    }
} else {
    // If the page is accessed directly, redirect to the login form
    header("Location: admin-login.php");
    exit();
}

$stmt->close();
$conn->close();
?>
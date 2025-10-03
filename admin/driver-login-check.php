<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
include '../includes/db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header("Location: driver-login.php?error=Email and password are required.");
        exit();
    }
    $sql = "SELECT id, password, full_name, role FROM admins WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        

        if (password_verify($password, $user['password'])) {
            
            if ($user['role'] === 'Bus Driver') {
                
                $_SESSION['driver_logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];

                $update_sql = "UPDATE admins SET last_login = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $current_time = date('Y-m-d H:i:s');
                $update_stmt->bind_param("si", $current_time, $user['id']);
                $update_stmt->execute();
                $update_stmt->close();

                $stmt->close();
                $conn->close();
                header("Location: driver-dashboard.php");
                exit();
            } else {

                $stmt->close();
                $conn->close();
                header("Location: driver-login.php?error=Access denied. Not a Bus Driver account.");
                exit();
            }
        } else {

            $stmt->close();
            $conn->close();
            header("Location: driver-login.php?error=Invalid email or password.");
            exit();
        }
    } else {

        $stmt->close();
        $conn->close();
        header("Location: driver-login.php?error=Invalid email or password.");
        exit();
    }
} else {

    header("Location: driver-login.php");
    exit();
}
?>
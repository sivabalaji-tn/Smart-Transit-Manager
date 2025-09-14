<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
include '../includes/db.php';

if (isset($_POST['submit'])) {
    $full_name = htmlspecialchars($_POST['full_name']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password']; 
    $dob = htmlspecialchars($_POST['dob']);
    $role = htmlspecialchars($_POST['role']);
    $id_card_no = htmlspecialchars($_POST['id_card_no']);
    $phone = htmlspecialchars($_POST['phone']);

    $status = 'active';
    $last_login = NULL;
    $created_at = date('Y-m-d H:i:s');

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO admins (full_name, email, password, dob, role, id_card_no, phone, status, last_login, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssssssssss", $full_name, $email, $hashed_password, $dob, $role, $id_card_no, $phone, $status, $last_login, $created_at);

        if ($stmt->execute()) {
            $_SESSION['message'] = 'Admin account created successfully!';
            $_SESSION['alert_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Error creating account: ' . $stmt->error;
            $_SESSION['alert_type'] = 'danger';
        }

        $stmt->close();
    } else {
        $_SESSION['message'] = 'Error in SQL query preparation: ' . $conn->error;
        $_SESSION['alert_type'] = 'danger';
    }

    $conn->close();
    header("Location: admin-create.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<a href="admin-dashboard.php" class="btn btn-primary position-fixed top-0 end-0 m-4 rounded-circle shadow-lg"
   style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;" 
   title="Go to Dashboard">
 <i class="bi bi-house-door-fill text-white fs-5"></i>
</a>
<body class="bg-dark text-light">
    <?php
    if (isset($_SESSION['message'])): 
    ?>
    <div class="alert alert-<?php echo $_SESSION['alert_type']; ?> alert-dismissible fade show text-center" role="alert">
        <?php echo $_SESSION['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php
        unset($_SESSION['message']);
        unset($_SESSION['alert_type']);
    endif;
    ?>
    
    <div class="container mt-5">
        <h1 class="text-white mb-4 text-center">Admin Portal</h1>
        
        <form action="admin-create.php" method="POST">
            <div class="row g-3">
                <div class="col-12 mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="dob" class="form-label">Date of Birth</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-calendar-date-fill"></i></span>
                        <input type="date" class="form-control" id="dob" name="dob" required>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="role" class="form-label">Role</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-badge-fill"></i></span>
                        <select class="form-select" id="role" name="role" required>
                            <option selected disabled>Select a role...</option>
                            <option value="Shed Manager">Shed Manager</option>
                            <option value="Control Unit">Control Unit</option>
                            <option value="Vehicle Maintenance">Vehicle Maintenance</option>
                            <option value="Bus Driver">Bus Driver</option>
                            <option value="Bus Conductor">Bus Conductor</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="id_card_no" class="form-label">ID Card Number</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-credit-card-fill"></i></span>
                        <input type="text" class="form-control" id="id_card_no" name="id_card_no" required>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                </div>
            </div>

            <button type="submit" name="submit" class="btn btn-primary w-100 py-3 mt-4">Create Admin</button>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
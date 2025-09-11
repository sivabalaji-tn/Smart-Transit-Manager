<?php include("../includes/db.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Admin | Smart Transit Manager</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/css/styles.css" rel="stylesheet">
</head>
<body>
  <!-- ✅ Toast Container -->
  <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
    <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          <i class="bi bi-check-circle-fill"></i> Admin created successfully!
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>

    <div id="errorToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          <i class="bi bi-exclamation-triangle-fill"></i> Error creating admin!
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  </div>
  <!-- ✅ End Toast Container -->

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-10 col-lg-8">
        <div class="card">
          <div class="form-header text-center">
            <h4><i class="bi bi-person-plus"></i> Create New Admin</h4>
          </div>
          <div class="card-body">
            <div class="progress mb-4">
              <div id="formProgress" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0"
                   aria-valuemin="0" aria-valuemax="100">0%</div>
            </div>

            <form method="POST" action="" enctype="multipart/form-data">
              <div class="mb-3">
                <label class="form-label"><i class="bi bi-person"></i> Full Name</label>
                <input type="text" class="form-control" name="full_name" required>
              </div>

              <div class="mb-3">
                <label class="form-label"><i class="bi bi-envelope"></i> Email</label>
                <input type="email" class="form-control" name="email" required>
              </div>

              <div class="mb-3">
                <label class="form-label"><i class="bi bi-lock"></i> Password</label>
                <input type="password" class="form-control" name="password" required>
              </div>

              <div class="mb-3">
                <label class="form-label"><i class="bi bi-calendar"></i> Date of Birth</label>
                <input type="date" class="form-control" name="dob">
              </div>

              <div class="mb-3">
                <label class="form-label"><i class="bi bi-briefcase"></i> Role</label>
                <select class="form-select" name="role" required>
                  <option value="">-- Select Role --</option>
                  <option>Shed Manager</option>
                  <option>Control Unit</option>
                  <option>Vehicle Maintenance</option>
                  <option>Bus Driver</option>
                  <option>Bus Conductor</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label"><i class="bi bi-credit-card"></i> ID Card No</label>
                <input type="text" class="form-control" name="id_card_no">
              </div>

              <div class="mb-3">
                <label class="form-label"><i class="bi bi-telephone"></i> Phone</label>
                <input type="text" class="form-control" name="phone">
              </div>

              <div class="mb-3">
                <label class="form-label"><i class="bi bi-geo-alt"></i> Address</label>
                <textarea class="form-control" name="address" rows="2"></textarea>
              </div>

              <div class="mb-3">
                <label class="form-label"><i class="bi bi-image"></i> Profile Picture</label>
                <input type="file" class="form-control" name="profile_pic">
              </div>

              <div class="mb-3">
                <label class="form-label"><i class="bi bi-toggle-on"></i> Status</label>
                <select class="form-select" name="status">
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                  <option value="suspended">Suspended</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label"><i class="bi bi-clock"></i> Shift Time</label>
                <input type="text" class="form-control" name="shift_time" placeholder="e.g. Morning 6AM - 2PM">
              </div>

              <div class="d-grid">
                <button type="submit" name="create_admin" class="btn btn-primary">
                  <i class="bi bi-check-circle"></i> Create Admin
                </button>
              </div>
            </form>

            <?php
            if (isset($_POST['create_admin'])) {
              $full_name = $_POST['full_name'];
              $email = $_POST['email'];
              $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
              $dob = $_POST['dob'];
              $role = $_POST['role'];
              $id_card_no = $_POST['id_card_no'];
              $phone = $_POST['phone'];
              $address = $_POST['address'];
              $status = $_POST['status'];
              $shift_time = $_POST['shift_time'];

              $profile_pic = null;
              if (!empty($_FILES['profile_pic']['name'])) {
                $target_dir = "../uploads/";
                if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                $file_name = time() . "_" . basename($_FILES["profile_pic"]["name"]);
                $target_file = $target_dir . $file_name;
                if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                  $profile_pic = $target_file;
                }
              }

              $sql = "INSERT INTO admins 
              (full_name, email, password, dob, role, id_card_no, phone, address, profile_pic, status, shift_time) 
              VALUES 
              ('$full_name', '$email', '$password', '$dob', '$role', '$id_card_no', '$phone', '$address', '$profile_pic', '$status', '$shift_time')";

              if ($conn->query($sql) === TRUE) {
                  echo "<script>
                    var toastEl = document.getElementById('successToast');
                    var toast = new bootstrap.Toast(toastEl, { delay: 3000 });
                    toast.show();
                  </script>";
              } else {
                  echo "<script>
                    var toastEl = document.getElementById('errorToast');
                    var toast = new bootstrap.Toast(toastEl, { delay: 3000 });
                    toast.show();
                  </script>";
              }
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const form = document.querySelector("form");
    const inputs = form.querySelectorAll("input, select, textarea");
    const progressBar = document.getElementById("formProgress");

    inputs.forEach(input => {
      input.addEventListener("input", updateProgress);
    });

    function updateProgress() {
      let filled = 0;
      inputs.forEach(input => {
        if (input.value.trim() !== "") filled++;
      });
      let percent = Math.round((filled / inputs.length) * 100);
      progressBar.style.width = percent + "%";
      progressBar.innerText = percent + "%";
      progressBar.setAttribute("aria-valuenow", percent);
    }
  </script>
</body>
</html>

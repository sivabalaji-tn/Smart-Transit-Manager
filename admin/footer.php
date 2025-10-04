<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer Component</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        footer {
            background: #212529; 
            color: #fff;
            padding: 60px 0 20px 0;
            font-family: 'Poppins', sans-serif;
        }
        footer a {
            color: #adb5bd;
            text-decoration: none;
            transition: color 0.3s;
        }
        footer a:hover {
            color: #0d6efd; 
        }
        .footer-logo img {
            max-width: 120px;
            margin-bottom: 15px;
            transition: transform 0.3s;
        }
        .footer-logo img:hover {
            transform: scale(1.1);
        }
        .footer-social a {
            display: inline-block;
            width: 45px;
            height: 45px;
            margin: 0 6px;
            border: 1px solid #fff;
            border-radius: 50%;
            text-align: center;
            line-height: 45px;
            color: #fff;
            transition: all 0.3s;
        }
        .footer-social a:hover {
            background: #0d6efd;
            border-color: #0d6efd;
            transform: translateY(-3px);
        }
        .footer-links p {
            margin: 5px 0;
            font-size: 14px;
        }
        .footer-links h5 {
            margin-bottom: 15px;
            color: #fff;
            font-weight: 600;
        }
        .copyright {
            color: #adb5bd; 
            font-size: 13px;
        }
    </style>
</head>
<body>
    
    <footer class="mt-auto">
        <div class="container text-center text-md-start">

            <div class="footer-logo text-center mb-5">
                <img src="assets/img/bus-logo.png" class="rounded-circle mb-3" alt="Bus Tracker Logo">
                <p style="color:#adb5bd; font-size:14px; font-weight:bold; margin-top:5px;">Smart Transit Manager</p>
            </div>

            <div class="footer-social text-center mt-4">
                <a href="#"><i class="bi bi-facebook"></i></a>
                <a href="#"><i class="bi bi-twitter"></i></a>
                <a href="#"><i class="bi bi-bus-front-fill"></i></a>
                <a href="#"><i class="bi bi-google"></i></a>
            </div>

            <div class="copyright text-center mt-3">
                © 2025 Bus Tracking System. All Rights Reserved.
            </div>

        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
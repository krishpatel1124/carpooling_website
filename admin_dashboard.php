<?php
// admin_dashboard.php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap + Google Fonts + Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f2f3f8;
        }

        .navbar {
            background: linear-gradient(to right, #6C63FF, #3F3D56);
        }

        .navbar-brand,
        .nav-link,
        .btn-danger {
            color: white !important;
        }

        .dashboard-header {
            background: linear-gradient(to right, #6C63FF, #3F3D56);
            color: white;
            padding: 40px 0;
            text-align: center;
            border-bottom-left-radius: 50px;
            border-bottom-right-radius: 50px;
            position: relative;
        }

        .dashboard-header img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 4px solid white;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .dashboard-cards {
            margin-top: -60px;
        }

        .card {
            border: none;
            border-radius: 16px;
            transition: transform 0.3s ease;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card i {
            font-size: 2rem;
            color: #6C63FF;
        }

        .card-title {
            font-weight: 600;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="#">Admin Panel</a>
        <button class="navbar-toggler bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon text-white"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="admin_ride_management.php">Ride Management</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_user_management.php">User Management</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_report.php">Reports</a>
                </li>
            </ul>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</nav>

<!-- Header -->
<div class="dashboard-header">
    <img src="images/OIP.jpeg" alt="Admin">
    <h2>Welcome, Admin</h2>
    <p>Manage rides, users, and view reports</p>
</div>

<!-- Dashboard Cards -->
<div class="container dashboard-cards mt-4">
    <div class="row g-4 justify-content-center">
        <div class="col-md-4">
            <div class="card text-center p-4">
                <i class="bi bi-car-front-fill mb-3"></i>
                <h5 class="card-title">Ride Management</h5>
                <p class="card-text">Approve, monitor, or delete ride listings.</p>
                <a href="admin_ride_management.php" class="btn btn-primary">Manage Rides</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center p-4">
                <i class="bi bi-people-fill mb-3"></i>
                <h5 class="card-title">User Management</h5>
                <p class="card-text">Manage registered drivers and passengers.</p>
                <a href="admin_user_management.php" class="btn btn-primary">Manage Users</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center p-4">
                <i class="bi bi-bar-chart-line-fill mb-3"></i>
                <h5 class="card-title">Reports & Feedback</h5>
                <p class="card-text">Review feedback and generate reports.</p>
                <a href="admin_report.php" class="btn btn-primary">View Reports</a>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

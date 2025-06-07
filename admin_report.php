<?php
// admin_report.php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch ride details
$rides = $conn->query("SELECT * FROM rides");

// Fetch user details
$users = $conn->query("SELECT * FROM users");

// Total users
$total_users = $conn->query("SELECT COUNT(*) AS total_users FROM users")->fetch_assoc()['total_users'];

// Users by role
$users_by_role = [];
$result = $conn->query("SELECT Role, COUNT(*) AS count FROM users GROUP BY Role");
while ($row = $result->fetch_assoc()) {
    $users_by_role[$row['Role']] = $row['count'];
}

// Total rides
$total_rides = $conn->query("SELECT COUNT(*) AS total_rides FROM rides")->fetch_assoc()['total_rides'];

// Rides by location
$rides_by_location = [];
$result = $conn->query("SELECT StartLocation, COUNT(*) AS count FROM rides GROUP BY StartLocation");
while ($row = $result->fetch_assoc()) {
    $rides_by_location[$row['StartLocation']] = $row['count'];
}

// Fetch feedbacks (Only if Feedback column exists, else remove it)
$reviews = $conn->query("
    SELECT r.ReviewID, r.RideID, r.PassengerID, u.Name, r.Rating 
    FROM review r 
    JOIN users u ON r.PassengerID = u.UserID
");

// Calculate average rating
$avg_rating_result = $conn->query("SELECT AVG(Rating) as avg_rating FROM review");
$avg_rating = round($avg_rating_result->fetch_assoc()['avg_rating'], 2);
?>

<?php include "admin_header.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Reports</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f4f8;
        }
        h2, h3 {
            color: #333;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .table thead {
            background-color: #0d6efd;
            color: white;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4 text-center">📊 Admin Reports</h2>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text display-6 fw-bold"><?= $total_users ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-success mb-3">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Rides</h5>
                    <p class="card-text display-6 fw-bold"><?= $total_rides ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Users by Role -->
    <div class="card mb-4">
        <div class="card-body">
            <h3 class="card-title">👥 Users by Role</h3>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Role</th>
                    <th>Total Users</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users_by_role as $role => $count): ?>
                    <tr>
                        <td><?= ucfirst($role) ?></td>
                        <td><?= $count ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Rides by Location -->
    <div class="card mb-4">
        <div class="card-body">
            <h3 class="card-title">🗺️ Rides by Start Location</h3>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Start Location</th>
                    <th>Total Rides</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rides_by_location as $location => $count): ?>
                    <tr>
                        <td><?= $location ?></td>
                        <td><?= $count ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Ride Report -->
    <div class="card mb-4">
        <div class="card-body">
            <h3 class="card-title">🚗 Rides Report</h3>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                    <tr>
                        <th>Ride ID</th>
                        <th>Driver ID</th>
                        <th>Pickup Location</th>
                        <th>Dropoff Location</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($ride = $rides->fetch_assoc()): ?>
                        <tr>
                            <td><?= $ride['RideID'] ?></td>
                            <td><?= $ride['DriverID'] ?></td>
                            <td><?= $ride['StartLocation'] ?></td>
                            <td><?= $ride['EndLocation'] ?></td>
                            <td><span class="badge bg-info text-dark"><?= ucfirst($ride['Status']) ?></span></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- User Report -->
    <div class="card mb-4">
        <div class="card-body">
            <h3 class="card-title">📋 User Report</h3>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?= $user['UserID'] ?></td>
                            <td><?= $user['Name'] ?></td>
                            <td><?= $user['Email'] ?></td>
                            <td><span class="badge bg-secondary"><?= ucfirst($user['Role']) ?></span></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Feedback Report -->
    <div class="card mb-5">
        <div class="card-body">
            <h3 class="card-title">⭐ Feedback Report (Avg Rating: <?= $avg_rating ?> / 5)</h3>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                    <tr>
                        <th>Review ID</th>
                        <th>Ride ID</th>
                        <th>Passenger Name</th>
                        <th>Rating</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($review = $reviews->fetch_assoc()): ?>
                        <tr>
                            <td><?= $review['ReviewID'] ?></td>
                            <td><?= $review['RideID'] ?></td>
                            <td><?= $review['Name'] ?></td>
                            <td><span class="badge bg-warning text-dark"><?= $review['Rating'] ?> ★</span></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="text-center mb-5">
        <a href="admin_dashboard.php" class="btn btn-outline-primary px-4 py-2">⬅ Back to Dashboard</a>
    </div>
</div>
</body>
</html>

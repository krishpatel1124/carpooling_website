<?php
session_start();

if (!isset($_SESSION['UserID']) || $_SESSION['Role'] != 'Driver') {
    header("Location: login.php");
    exit();
}

include 'db.php';

$user_id = $_SESSION['UserID'];
$sql = "SELECT * FROM rides WHERE DriverID = $user_id ORDER BY DateTime DESC";
$result = mysqli_query($conn, $sql);
?>
<?php include 'driver_header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .dashboard-header {
            background: linear-gradient(to right, #007bff, #00c6ff);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
        }
        .ride-card {
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }
        .ride-card:hover {
            transform: scale(1.02);
        }
        .create-ride-btn {
            font-size: 1.2rem;
            padding: 12px 25px;
            border-radius: 30px;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="dashboard-header">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['Name']); ?> 👋</h2>
        <p class="lead mb-0">Here's a summary of your active rides</p>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Your Active Rides</h4>
        <a href="create_ride.php" class="btn btn-success create-ride-btn">+ Create Ride</a>
    </div>

    <div class="row">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($ride = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card ride-card h-100">
                        <div class="card-body">
                            <h5 class="card-title text-primary">
                                <?php echo htmlspecialchars($ride['StartLocation']) . " → " . htmlspecialchars($ride['EndLocation']); ?>
                            </h5>
                            <p class="card-text mb-1"><strong>Date:</strong> <?php echo date('d M Y', strtotime($ride['DateTime'])); ?></p>
                            <p class="card-text mb-1"><strong>Time:</strong> <?php echo date('H:i', strtotime($ride['DateTime'])); ?></p>
                            <p class="card-text mb-1"><strong>Fare per Seat:</strong> ₹<?php echo htmlspecialchars($ride['FarePerSeat']); ?></p>
                            <p class="card-text mb-3"><strong>Seats Available:</strong> <?php echo htmlspecialchars($ride['AvailableSeats']); ?></p>
                            <a href="manage_ride_requests.php?ride_id=<?php echo $ride['RideID']; ?>" class="btn btn-outline-primary w-100">Manage Ride</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    You have not created any rides yet.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include 'driver_footer.php'; ?>

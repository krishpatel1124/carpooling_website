<?php
session_start();

if (file_exists(__DIR__ . '/db.php')) {
    include __DIR__ . '/db.php';
} else {
    die("Database connection file not found.");
}

if (!isset($_SESSION['UserID']) || $_SESSION['Role'] !== 'Driver') {
    header("Location: login.php");
    exit();
}

$driverID = $_SESSION['UserID'];

// Handle Ride Deletion
if (isset($_POST['delete_ride'])) {
    $rideID = $_POST['ride_id'];
    $deleteQuery = "DELETE FROM rides WHERE RideID = ? AND DriverID = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("ii", $rideID, $driverID);
    $stmt->execute();
}

// Handle Ride Update
if (isset($_POST['update_ride'])) {
    $rideID = $_POST['ride_id'];
    $startLocation = trim($_POST['start_location']);
    $endLocation = trim($_POST['end_location']);
    $dateTime = $_POST['date_time'];
    $availableSeats = (int)$_POST['available_seats'];

    $currentTime = date('Y-m-d\TH:i');

    if ($dateTime <= $currentTime) {
        $error = "Please select a future date and time.";
    } elseif ($availableSeats < 1 || $availableSeats > 30) {
        $error = "Seats must be between 1 and 30.";
    } else {
        $updateQuery = "UPDATE rides SET StartLocation = ?, EndLocation = ?, DateTime = ?, AvailableSeats = ? WHERE RideID = ? AND DriverID = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sssiii", $startLocation, $endLocation, $dateTime, $availableSeats, $rideID, $driverID);
        $stmt->execute();
    }
}

// Fetch rides
$rideQuery = "SELECT * FROM rides WHERE DriverID = ?";
$stmt = $conn->prepare($rideQuery);
$stmt->bind_param("i", $driverID);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include 'driver_header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Ride Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f6f9fc;
        }
        .ride-card {
            border-left: 6px solid #0d6efd;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        .ride-header {
            background-color: #e9f1ff;
            padding: 15px;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            font-weight: bold;
        }
        .form-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
        }
        .btn-group .btn {
            margin-right: 10px;
        }
    </style>
</head>
<body>
<div class="container my-5">
    <h2 class="text-center mb-4 text-primary">Manage Your Rides</h2>

    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <?php while ($ride = $result->fetch_assoc()) { ?>
        <div class="card ride-card mb-4">
            <div class="ride-header">
                Ride #<?php echo $ride['RideID']; ?>: <?php echo htmlspecialchars($ride['StartLocation']); ?> ➜ <?php echo htmlspecialchars($ride['EndLocation']); ?>
            </div>
            <div class="card-body">
                <p><strong>Date & Time:</strong> <?php echo date('d M Y, H:i', strtotime($ride['DateTime'])); ?></p>
                <p><strong>Available Seats:</strong> <?php echo $ride['AvailableSeats']; ?></p>

                <form method="POST" class="form-section mt-3" onsubmit="return validateDateTime(this)">
                    <input type="hidden" name="ride_id" value="<?php echo $ride['RideID']; ?>">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label">New Start Location</label>
                            <input type="text" class="form-control" name="start_location" placeholder="Start Location" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">New End Location</label>
                            <input type="text" class="form-control" name="end_location" placeholder="End Location" required>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label">Date & Time</label>
                            <input type="datetime-local" class="form-control" name="date_time" min="<?= date('Y-m-d\TH:i'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Available Seats</label>
                            <input type="number" class="form-control" name="available_seats" placeholder="Seats" min="1" max="30" required>
                        </div>
                    </div>
                    <div class="btn-group mt-3">
                        <button type="submit" class="btn btn-primary" name="update_ride">Update Ride</button>
                        <button type="submit" class="btn btn-danger" name="delete_ride" onclick="return confirm('Are you sure you want to delete this ride?')">Delete Ride</button>
                    </div>
                </form>
            </div>
        </div>
    <?php } ?>
</div>

<script>
    function validateDateTime(form) {
        const input = form.querySelector('input[name="date_time"]');
        const selected = new Date(input.value);
        const now = new Date();

        if (selected <= now) {
            alert("Please select a future date and time.");
            return false;
        }
        return true;
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include 'driver_footer.php'; ?>

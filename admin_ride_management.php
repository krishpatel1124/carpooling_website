<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

function validate_ride_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Delete ride
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM rides WHERE RideID = $delete_id");
    echo "<div class='alert alert-success text-center'>Ride deleted successfully.</div>";
}

// Create ride
if (isset($_POST['create_ride'])) {
    $driver_id = intval($_POST['driver_id']);
    $car_id = intval($_POST['car_id']);
    $start_location = validate_ride_input($_POST['start_location']);
    $end_location = validate_ride_input($_POST['end_location']);
    $date_time = $_POST['date_time'];
    $fare_per_seat = floatval($_POST['fare_per_seat']);
    $available_seats = intval($_POST['available_seats']);

    $check_driver = $conn->query("SELECT * FROM users WHERE UserID = $driver_id");


    if ($driver_id <= 0 || $car_id <= 0 || empty($start_location) || empty($end_location) ||
        empty($date_time) || $fare_per_seat < 10 || $available_seats < 1 || $available_seats > 10) {
        echo "<div class='alert alert-danger text-center'>Please enter valid ride details.</div>";
    } elseif ($check_driver->num_rows === 0) {
        echo "<div class='alert alert-danger text-center'>Driver ID $driver_id does not exist or is not a driver.</div>";
    } else {
        $conn->query("INSERT INTO rides (DriverID, CarID, StartLocation, EndLocation, DateTime, FarePerSeat, AvailableSeats) 
            VALUES ($driver_id, $car_id, '$start_location', '$end_location', '$date_time', $fare_per_seat, $available_seats)");
        echo "<div class='alert alert-success text-center'>Ride created successfully.</div>";
    }
}

// Update ride
if (isset($_POST['update_ride'])) {
    $ride_id = intval($_POST['ride_id']);
    $start_location = validate_ride_input($_POST['start_location']);
    $end_location = validate_ride_input($_POST['end_location']);
    $date_time = $_POST['date_time'];
    $fare_per_seat = floatval($_POST['fare_per_seat']);
    $available_seats = intval($_POST['available_seats']);

    if ($ride_id <= 0 || empty($start_location) || empty($end_location) || empty($date_time) ||
        $fare_per_seat < 10 || $available_seats < 1 || $available_seats > 10) {
        echo "<div class='alert alert-danger text-center'>Invalid update details.</div>";
    } else {
        $conn->query("UPDATE rides SET StartLocation = '$start_location', EndLocation = '$end_location', 
            DateTime = '$date_time', FarePerSeat = $fare_per_seat, AvailableSeats = $available_seats 
            WHERE RideID = $ride_id");
        echo "<div class='alert alert-success text-center'>Ride updated successfully.</div>";
    }
}

$result = $conn->query("SELECT * FROM rides");
include "admin_header.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ride Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; }
        .section-title { font-weight: bold; color: #333; margin-bottom: 20px; }
        .badge { font-size: 0.9em; }
        .table-hover tbody tr:hover { background-color: #f1f1f1; }
    </style>
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4 text-center text-primary">Admin Ride Management</h2>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Create New Ride</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-2">
                        <input type="number" name="driver_id" placeholder="Driver ID" min="1" required class="form-control">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="car_id" placeholder="Car ID" min="1" required class="form-control">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="start_location" placeholder="Start Location" pattern="[A-Za-z\s]{2,}" required class="form-control">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="end_location" placeholder="End Location" pattern="[A-Za-z\s]{2,}" required class="form-control">
                    </div>
                    <div class="col-md-2">
                        <input type="datetime-local" name="date_time" min="<?php echo date('Y-m-d\TH:i'); ?>" required class="form-control">
                    </div>
                    <div class="col-md-1">
                        <input type="number" name="fare_per_seat" placeholder="₹/Seat" min="10" required class="form-control">
                    </div>
                    <div class="col-md-1">
                        <input type="number" name="available_seats" placeholder="Seats" min="1" max="10" required class="form-control">
                    </div>
                </div>
                <div class="mt-3 text-end">
                    <button type="submit" name="create_ride" class="btn btn-success">Add Ride</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">All Rides</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Ride ID</th>
                        <th>Driver ID</th>
                        <th>Car ID</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Date & Time</th>
                        <th>Fare</th>
                        <th>Seats</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($ride = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><span class="badge bg-secondary"><?php echo $ride['RideID']; ?></span></td>
                        <td><?php echo $ride['DriverID']; ?></td>
                        <td><?php echo $ride['CarID']; ?></td>
                        <td><?php echo $ride['StartLocation']; ?></td>
                        <td><?php echo $ride['EndLocation']; ?></td>
                        <td><?php echo $ride['DateTime']; ?></td>
                        <td><span class="badge bg-success">₹<?php echo $ride['FarePerSeat']; ?></span></td>
                        <td><span class="badge bg-info text-dark"><?php echo $ride['AvailableSeats']; ?></span></td>
                        <td>
                            <a href="?delete_id=<?php echo $ride['RideID']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this ride?')">Delete</a>
                            <button class="btn btn-sm btn-warning" onclick="editRide(
                                '<?php echo $ride['RideID']; ?>',
                                '<?php echo $ride['StartLocation']; ?>',
                                '<?php echo $ride['EndLocation']; ?>',
                                '<?php echo $ride['DateTime']; ?>',
                                '<?php echo $ride['FarePerSeat']; ?>',
                                '<?php echo $ride['AvailableSeats']; ?>'
                            )">Update</button>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-warning">
            <h5 class="mb-0">Update Ride</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="ride_id" id="ride_id">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="start_location" id="start_location" pattern="[A-Za-z\s]{2,}" required class="form-control">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="end_location" id="end_location" pattern="[A-Za-z\s]{2,}" required class="form-control">
                    </div>
                    <div class="col-md-3">
                        <input type="datetime-local" name="date_time" id="date_time" min="<?php echo date('Y-m-d\TH:i'); ?>" required class="form-control">
                    </div>
                    <div class="col-md-1">
                        <input type="number" name="fare_per_seat" id="fare_per_seat" min="10" required class="form-control">
                    </div>
                    <div class="col-md-1">
                        <input type="number" name="available_seats" id="available_seats" min="1" max="10" required class="form-control">
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="submit" name="update_ride" class="btn btn-warning">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="admin_dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>
</div>

<script>
    function editRide(id, start, end, datetime, fare, seats) {
        document.getElementById('ride_id').value = id;
        document.getElementById('start_location').value = start;
        document.getElementById('end_location').value = end;
        document.getElementById('date_time').value = datetime;
        document.getElementById('fare_per_seat').value = fare;
        document.getElementById('available_seats').value = seats;
        window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
    }
</script>
</body>
</html>

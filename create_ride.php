<?php
session_start();
include 'db.php';

if (!isset($_SESSION['UserID']) || $_SESSION['Role'] != 'Driver') {
    header("Location: login.php");
    exit();
}

$driver_id = $_SESSION['UserID'];

// Fetch cars
$car_query = "SELECT CarID, CarModel, CarNumber FROM cars WHERE OwnerID = ?";
$stmt = mysqli_prepare($conn, $car_query);
mysqli_stmt_bind_param($stmt, "i", $driver_id);
mysqli_stmt_execute($stmt);
$car_result = mysqli_stmt_get_result($stmt);

// Ride creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_ride'])) {
    $start_location = trim($_POST['start_location']);
    $end_location = trim($_POST['end_location']);
    $date_time = $_POST['date_time'];
    $available_seats = (int)$_POST['available_seats'];
    $fare_per_seat = (float)$_POST['fare_per_seat'];
    $car_id = $_POST['car_id'];

    $current_time = date('Y-m-d\TH:i');

    if ($date_time <= $current_time) {
        $error = "Please select a future date and time.";
    } elseif ($available_seats < 1 || $available_seats > 30) {
        $error = "Available seats must be between 1 and 30.";
    } elseif ($fare_per_seat < 1) {
        $error = "Fare per seat must be at least ₹1.";
    } elseif (empty($car_id)) {
        $error = "Please select a car.";
    } else {
        $sql = "INSERT INTO rides (DriverID, CarID, StartLocation, EndLocation, DateTime, AvailableSeats, FarePerSeat, Status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'active')";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iisssdi", $driver_id, $car_id, $start_location, $end_location, $date_time, $available_seats, $fare_per_seat);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: driver_dashboard.php");
            exit();
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}

// Car addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_car'])) {
    $car_model = trim($_POST['car_model']);
    $car_number = trim($_POST['car_number']);
    $seats = (int)$_POST['seats'];
    $car_type = $_POST['car_type'];

    if (empty($car_model) || empty($car_number) || empty($seats) || empty($car_type)) {
        $error = "All car details must be filled.";
    } else {
        // Check if car number is unique
        $check_query = "SELECT * FROM cars WHERE CarNumber = ?";
        $stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($stmt, "s", $car_number);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $error = "Car number already exists.";
        } else {
            $car_sql = "INSERT INTO cars (OwnerID, CarModel, CarNumber, Capacity, CarType) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $car_sql);
            mysqli_stmt_bind_param($stmt, "issis", $driver_id, $car_model, $car_number, $seats, $car_type);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: create_ride.php");
                exit();
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}

include 'driver_header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Ride</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f8; }
        .page-title {
            background: linear-gradient(to right, #007bff, #00c6ff);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 40px;
            text-align: center;
        }
        .form-card {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 40px;
        }
        .form-title { margin-bottom: 20px; color: #007bff; }
        .btn-custom {
            padding: 10px 25px;
            border-radius: 30px;
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="page-title">
        <h2>Create a New Ride</h2>
        <p class="lead">Easily create rides and manage your vehicles</p>
    </div>

    <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>

    <!-- Ride Form -->
    <div class="form-card">
        <h4 class="form-title">Ride Details</h4>
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Start Location</label>
                    <input type="text" class="form-control" name="start_location" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">End Location</label>
                    <input type="text" class="form-control" name="end_location" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date & Time</label>
                    <input type="datetime-local" class="form-control" name="date_time" min="<?= date('Y-m-d\TH:i'); ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Available Seats</label>
                    <input type="number" class="form-control" name="available_seats" min="1" max="30" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Fare per Seat (₹)</label>
                    <input type="number" class="form-control" name="fare_per_seat" min="1" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Select Your Car</label>
                    <select class="form-control" name="car_id" required>
                        <option value="">-- Select Car --</option>
                        <?php mysqli_data_seek($car_result, 0); while ($car = mysqli_fetch_assoc($car_result)) { ?>
                            <option value="<?= $car['CarID']; ?>">
                                <?= htmlspecialchars($car['CarModel'] . " (" . $car['CarNumber'] . ")"); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <button type="submit" name="create_ride" class="btn btn-success btn-custom mt-3">Create Ride</button>
        </form>
    </div>

    <!-- Car Form -->
    <div class="form-card">
        <h4 class="form-title">Add a New Car</h4>
        <form method="POST">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Car Model</label>
                    <input type="text" class="form-control" name="car_model" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Car Number</label>
                    <input type="text" class="form-control" name="car_number" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Total Seats</label>
                    <input type="number" class="form-control" name="seats" min="1" max="30" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Car Type</label>
                    <select class="form-control" name="car_type" required>
                        <option value="">-- Select Type --</option>
                        <option value="AC">AC</option>
                        <option value="Non-AC">Non-AC</option>
                    </select>
                </div>
            </div>
            <button type="submit" name="add_car" class="btn btn-primary btn-custom mt-3">Add Car</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include 'driver_footer.php'; ?>

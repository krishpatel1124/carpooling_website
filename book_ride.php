<?php
session_start();

if (!isset($_SESSION['UserID']) || $_SESSION['Role'] != 'Passenger') {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Handle ride booking
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ride_id'])) {
    $user_id = $_SESSION['UserID'];
    $ride_id = intval($_POST['ride_id']);
    $status = 'pending';

    $checkTimeQuery = "SELECT DateTime FROM rides WHERE RideID = $ride_id AND DateTime > NOW()";
    $checkTimeResult = mysqli_query($conn, $checkTimeQuery);

    if (mysqli_num_rows($checkTimeResult) > 0) {
        $checkBooking = "SELECT * FROM bookings WHERE RideID = $ride_id AND PassengerID = $user_id";
        $bookingResult = mysqli_query($conn, $checkBooking);

        if (mysqli_num_rows($bookingResult) === 0) {
            $sql = "INSERT INTO bookings (RideID, PassengerID, Status) VALUES ($ride_id, $user_id, '$status')";
            if (mysqli_query($conn, $sql)) {
                $driverQuery = "SELECT DriverID FROM rides WHERE RideID = $ride_id";
                $driverResult = mysqli_query($conn, $driverQuery);
                $driverRow = mysqli_fetch_assoc($driverResult);
                $driver_id = $driverRow['DriverID'];

                $message = "New ride request from Passenger ID: $user_id";
                $notifSql = "INSERT INTO notifications (user_id, message, is_read, created_at) VALUES ($driver_id, '$message', 0, NOW())";
                mysqli_query($conn, $notifSql);

                $_SESSION['message'] = "Ride request sent successfully!";
                header("Location: book_ride.php");
                exit();
            } else {
                $_SESSION['error'] = "Error: " . mysqli_error($conn);
            }
        } else {
            $_SESSION['error'] = "You have already requested this ride.";
        }
    } else {
        $_SESSION['error'] = "You cannot book a past ride.";
    }
}

// Fetch available rides (future only)
$sql = "SELECT * FROM rides WHERE Status = 'active' AND DateTime > NOW() ORDER BY DateTime ASC";

$start_location = $end_location = $date = $seats_required = '';

if (isset($_GET['start_location'], $_GET['end_location'], $_GET['date'], $_GET['seats_required'])) {
    $start_location = mysqli_real_escape_string($conn, $_GET['start_location']);
    $end_location = mysqli_real_escape_string($conn, $_GET['end_location']);
    $date = mysqli_real_escape_string($conn, $_GET['date']);
    $seats_required = intval($_GET['seats_required']);

    if ($seats_required > 0) {
        $sql = "SELECT * FROM rides 
                WHERE Status = 'active' 
                AND DateTime > NOW()
                AND StartLocation LIKE '%$start_location%' 
                AND EndLocation LIKE '%$end_location%' 
                AND DateTime LIKE '%$date%' 
                AND AvailableSeats >= $seats_required
                ORDER BY DateTime ASC";
    } else {
        $_SESSION['error'] = "Please enter a valid number of seats.";
    }
}

$result = mysqli_query($conn, $sql);

include 'passenger_header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book a Ride</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .ride-card {
            border: 1px solid #dee2e6;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .ride-card .card-body {
            padding: 1.5rem;
        }
        .driver-info {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .btn-request {
            border-radius: 25px;
        }
        .section-title {
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 5px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <h2 class="mb-4 text-primary">Hello, <?php echo htmlspecialchars($_SESSION['Name']); ?> 👋</h2>

    <!-- Messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- Search Section -->
    <h4 class="section-title text-secondary">🔍 Search for Available Rides</h4>
    <form method="GET" class="mb-5">
        <div class="row g-3">
            <div class="col-md-3">
                <input type="text" class="form-control" name="start_location" placeholder="Start Location" value="<?php echo htmlspecialchars($start_location); ?>" required>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" name="end_location" placeholder="End Location" value="<?php echo htmlspecialchars($end_location); ?>" required>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="date" value="<?php echo htmlspecialchars($date); ?>" required>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control" name="seats_required" placeholder="Seats" min="1" value="<?php echo htmlspecialchars($seats_required); ?>" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Go</button>
            </div>
        </div>
    </form>

    <!-- Available Rides -->
    <h4 class="section-title text-secondary">🚌 Available Rides</h4>
    <?php
    if (mysqli_num_rows($result) > 0) {
        while ($ride = mysqli_fetch_assoc($result)) {
            $driver_id = $ride['DriverID'];

            $driver_query = mysqli_query($conn, "SELECT Name FROM users WHERE UserID = $driver_id");
            $driver = mysqli_fetch_assoc($driver_query);
            $driver_name = $driver ? $driver['Name'] : "Unknown";

            $rating_query = "SELECT AVG(Rating) AS avg_rating FROM review WHERE DriverID = $driver_id";
            $rating_result = mysqli_query($conn, $rating_query);
            $rating_row = mysqli_fetch_assoc($rating_result);
            $avg_rating = $rating_row['avg_rating'] ? number_format($rating_row['avg_rating'], 1) : "No rating";

            echo "<div class='card ride-card mb-4'>
                    <div class='card-body'>
                        <div class='d-flex justify-content-between'>
                            <div>
                                <h5 class='card-title mb-2'>{$ride['StartLocation']} → {$ride['EndLocation']}</h5>
                                <p class='card-text mb-1'><strong>Date & Time:</strong> " . date('d M Y, h:i A', strtotime($ride['DateTime'])) . "</p>
                                <p class='card-text mb-1'><strong>Available Seats:</strong> {$ride['AvailableSeats']}</p>
                                <p class='card-text'><strong>Fare per Seat:</strong> ₹{$ride['FarePerSeat']}</p>
                                <p class='driver-info'><strong>Driver:</strong> {$driver_name} | <strong>Rating:</strong> {$avg_rating} ⭐</p>
                            </div>
                            <div class='align-self-center'>
                                <form method='POST'>
                                    <input type='hidden' name='ride_id' value='{$ride['RideID']}'>
                                    <button type='submit' class='btn btn-success btn-request'>Request Ride</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>";
        }
    } else {
        echo "<p class='text-muted'>No available rides found.</p>";
    }
    ?>

    <!-- Booked Rides -->
    <h4 class="section-title text-secondary mt-5">📌 Your Booked Rides</h4>
    <?php
    $user_id = $_SESSION['UserID'];
    $booked_query = "SELECT rides.* FROM bookings 
                    JOIN rides ON bookings.RideID = rides.RideID 
                    WHERE bookings.PassengerID = $user_id 
                    AND bookings.Status = 'confirmed' 
                    AND rides.DateTime > NOW()
                    ORDER BY rides.DateTime ASC";
    $booked_rides = mysqli_query($conn, $booked_query);

    if (mysqli_num_rows($booked_rides) > 0) {
        while ($ride = mysqli_fetch_assoc($booked_rides)) {
            echo "<div class='card ride-card mb-3'>
                    <div class='card-body'>
                        <h5 class='card-title'>{$ride['StartLocation']} → {$ride['EndLocation']}</h5>
                        <p class='card-text mb-1'><strong>Date & Time:</strong> " . date('d M Y, h:i A', strtotime($ride['DateTime'])) . "</p>
                        <p class='card-text'><strong>Fare per Seat:</strong> ₹{$ride['FarePerSeat']}</p>
                        <span class='badge bg-success'>Booked</span>
                    </div>
                </div>";
        }
    } else {
        echo "<p class='text-muted'>You have not booked any upcoming rides yet.</p>";
    }
    ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include 'passenger_footer.php'; ?>

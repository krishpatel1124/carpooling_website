<?php
session_start();

if (!isset($_SESSION['UserID']) || $_SESSION['Role'] != 'Passenger') {
    header("Location: login.php");
    exit();
}

include 'db.php';

$user_id = $_SESSION['UserID'];
$success = $error = "";

// Handle manual actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = intval($_POST['booking_id']);

    if (isset($_POST['cancel_booking'])) {
        $cancel_sql = "UPDATE bookings SET Status = 'canceled' WHERE BookingID = $booking_id AND PassengerID = $user_id";
        $success = mysqli_query($conn, $cancel_sql) ? "Booking canceled." : "Error: " . mysqli_error($conn);
    }

    if (isset($_POST['complete_booking'])) {
        $complete_sql = "UPDATE bookings SET Status = 'completed' WHERE BookingID = $booking_id AND PassengerID = $user_id";
        $success = mysqli_query($conn, $complete_sql) ? "Ride marked as completed." : "Error: " . mysqli_error($conn);
    }
}

// Fetch bookings
$sql = "SELECT 
            b.BookingID, 
            r.RideID, 
            r.StartLocation, 
            r.EndLocation, 
            r.DateTime, 
            r.FarePerSeat, 
            r.DriverID,
            b.Status 
        FROM bookings b
        JOIN rides r ON b.RideID = r.RideID
        WHERE b.PassengerID = $user_id
        ORDER BY r.DateTime DESC";

$result = mysqli_query($conn, $sql);
if (!$result) die("Error fetching bookings: " . mysqli_error($conn));

$activeRides = [];
$pastRides = [];

$currentTime = date('Y-m-d H:i:s');

// Automatically update outdated ride statuses
while ($row = mysqli_fetch_assoc($result)) {
    $rideTime = $row['DateTime'];
    $status = strtolower($row['Status']);
    $bookingId = $row['BookingID'];

    if (strtotime($rideTime) < strtotime($currentTime)) {
        if ($status == 'pending') {
            $update = "UPDATE bookings SET Status = 'canceled' WHERE BookingID = $bookingId";
            mysqli_query($conn, $update);
            $row['Status'] = 'canceled';
            $pastRides[] = $row;
        } elseif ($status == 'confirmed') {
            $update = "UPDATE bookings SET Status = 'completed' WHERE BookingID = $bookingId";
            mysqli_query($conn, $update);
            $row['Status'] = 'completed';
            $pastRides[] = $row;
        } else {
            $pastRides[] = $row;
        }
    } else {
        if (in_array($status, ['confirmed', 'pending'])) {
            $activeRides[] = $row;
        } else {
            $pastRides[] = $row;
        }
    }
}

include 'passenger_header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Booked Rides</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .ride-card {
            border: 1px solid #dee2e6;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.2s ease;
        }
        .ride-card:hover { transform: scale(1.01); }
        .ride-header {
            font-weight: 600;
            font-size: 1.2rem;
            color: #0d6efd;
        }
        .badge-status {
            font-size: 0.9rem;
            padding: 6px 10px;
            border-radius: 20px;
        }
        .action-btn {
            border-radius: 25px;
            padding: 5px 15px;
        }
        .section-title {
            border-left: 5px solid #0d6efd;
            padding-left: 10px;
            margin-top: 40px;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <h2 class="section-title text-dark">🚗 Your Booked Rides</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (!empty($activeRides)): ?>
        <h4 class="text-success section-title">🟢 Current Bookings</h4>
        <div class="row g-4">
            <?php foreach ($activeRides as $row): ?>
                <?php
                    $status = strtolower($row['Status']);
                    $statusBadge = match ($status) {
                        'confirmed' => '<span class="badge bg-info badge-status">Confirmed</span>',
                        'pending'   => '<span class="badge bg-warning text-dark badge-status">Pending</span>',
                        default     => '<span class="badge bg-dark badge-status">Unknown</span>'
                    };
                ?>
                <div class="col-md-6">
                    <div class="card ride-card p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="ride-header"><?php echo htmlspecialchars($row['StartLocation']); ?> → <?php echo htmlspecialchars($row['EndLocation']); ?></div>
                            <?php echo $statusBadge; ?>
                        </div>
                        <p class="mb-1"><strong>Date & Time:</strong> <?php echo date('d M Y, h:i A', strtotime($row['DateTime'])); ?></p>
                        <p class="mb-2"><strong>Fare:</strong> ₹<?php echo htmlspecialchars($row['FarePerSeat']); ?></p>

                        <div class="d-flex gap-2 flex-wrap">
                            <a href="details.php?ride_id=<?php echo $row['RideID']; ?>&driver_id=<?php echo $row['DriverID']; ?>" class="btn btn-outline-primary btn-sm action-btn">View Details</a>
                            <form method="POST">
                                <input type="hidden" name="booking_id" value="<?php echo $row['BookingID']; ?>">
                                <button type="submit" name="cancel_booking" class="btn btn-outline-danger btn-sm action-btn">Cancel</button>
                            </form>
                            <form method="POST">
                                <input type="hidden" name="booking_id" value="<?php echo $row['BookingID']; ?>">
                                <button type="submit" name="complete_booking" class="btn btn-outline-success btn-sm action-btn">Complete</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($pastRides)): ?>
        <h4 class="text-muted section-title">📁 Past Bookings</h4>
        <div class="row g-4">
            <?php foreach ($pastRides as $row): ?>
                <?php
                    $status = strtolower($row['Status']);
                    $statusBadge = match ($status) {
                        'completed' => '<span class="badge bg-success badge-status">Completed</span>',
                        'canceled'  => '<span class="badge bg-secondary badge-status">Canceled</span>',
                        'rejected'  => '<span class="badge bg-danger badge-status">Rejected</span>',
                        default     => '<span class="badge bg-dark badge-status">Unknown</span>'
                    };
                ?>
                <div class="col-md-6">
                    <div class="card ride-card p-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="ride-header"><?php echo htmlspecialchars($row['StartLocation']); ?> → <?php echo htmlspecialchars($row['EndLocation']); ?></div>
                            <?php echo $statusBadge; ?>
                        </div>
                        <p class="mb-1"><strong>Date & Time:</strong> <?php echo date('d M Y, h:i A', strtotime($row['DateTime'])); ?></p>
                        <p class="mb-2"><strong>Fare:</strong> ₹<?php echo htmlspecialchars($row['FarePerSeat']); ?></p>

                        <div class="d-flex gap-2 flex-wrap">
                            <a href="details.php?ride_id=<?php echo $row['RideID']; ?>&driver_id=<?php echo $row['DriverID']; ?>" class="btn btn-outline-primary btn-sm action-btn">View Details</a>
                            <span class="text-muted">No actions</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($activeRides) && empty($pastRides)): ?>
        <div class="alert alert-secondary">No booked rides found.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include 'passenger_footer.php'; ?>

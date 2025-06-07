<?php
session_start();

if (!isset($_SESSION['UserID']) || $_SESSION['Role'] != 'Passenger') {
    header("Location: login.php");
    exit();
}

include 'db.php';
$passenger_id = $_SESSION['UserID'];

$sql = "SELECT 
            b.BookingID,
            b.RideID,
            r.DriverID,
            r.StartLocation,
            r.EndLocation,
            r.DateTime,
            r.FarePerSeat
        FROM bookings b
        JOIN rides r ON b.RideID = r.RideID
        WHERE b.PassengerID = $passenger_id AND b.Status = 'completed'
        ORDER BY r.DateTime DESC";

$result = mysqli_query($conn, $sql);

include 'passenger_header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Past Rides</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7fa;
        }
        .ride-card {
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            padding: 20px;
            transition: transform 0.2s ease;
        }
        .ride-card:hover {
            transform: scale(1.01);
        }
        .location-route {
            font-weight: 600;
            font-size: 1.1rem;
            color: #0d6efd;
        }
        .ride-info {
            font-size: 0.95rem;
            color: #555;
        }
        .fare-tag {
            font-size: 1rem;
            font-weight: bold;
            color: #28a745;
        }
        .feedback-btn {
            float: right;
        }
        .header-line {
            border-left: 5px solid #0d6efd;
            padding-left: 10px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <h2 class="header-line">🚗 Your Past Rides</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="row g-4">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-6">
                    <div class="ride-card">
                        <div class="location-route mb-2">
                            <?php echo htmlspecialchars($row['StartLocation']); ?> → <?php echo htmlspecialchars($row['EndLocation']); ?>
                        </div>
                        <div class="ride-info mb-2">
                            <strong>Date & Time:</strong> <?php echo date('d M Y, h:i A', strtotime($row['DateTime'])); ?>
                        </div>
                        <div class="ride-info mb-2">
                            <strong>Fare:</strong> <span class="fare-tag">₹<?php echo htmlspecialchars($row['FarePerSeat']); ?></span>
                        </div>
                        <a href="feedback.php?ride_id=<?php echo $row['RideID']; ?>&driver_id=<?php echo $row['DriverID']; ?>&booking_id=<?php echo $row['BookingID']; ?>" class="btn btn-outline-primary btn-sm feedback-btn">Give Feedback</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">No completed rides found.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include 'passenger_footer.php'; ?>

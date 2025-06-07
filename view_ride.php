<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['Role'] != 'Passenger') {
    header("Location: login.php");
    exit();
}

include 'db.php';

$passenger_id = $_SESSION['UserID'];

$sql = "SELECT rides.*, users.Name AS DriverName, users.Mobile AS DriverMobile 
        FROM bookings
        JOIN rides ON bookings.RideID = rides.RideID
        JOIN users ON rides.DriverID = users.UserID
        WHERE bookings.PassengerID = $passenger_id AND bookings.Status = 'confirmed' 
        ORDER BY rides.DateTime DESC LIMIT 1";

$result = mysqli_query($conn, $sql);
$ride = mysqli_fetch_assoc($result);

include 'passenger_header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ride Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Ride Details</h2>
    <?php if ($ride): ?>
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title"><?= $ride['StartLocation'] ?> to <?= $ride['EndLocation'] ?></h5>
                <p class="card-text">Date & Time: <?= date('d M Y, H:i', strtotime($ride['DateTime'])) ?></p>
                <p class="card-text">Fare: ₹<?= $ride['FarePerSeat'] ?></p>
                <h4>Driver Details</h4>
                <p>Name: <?= $ride['DriverName'] ?></p>
                <p>Mobile: <?= $ride['DriverMobile'] ?></p>
            </div>
        </div>
    <?php else: ?>
        <p>No ride details found.</p>
    <?php endif; ?>
</div>

</body>
</html>

<?php
include 'passenger_footer.php';
?>

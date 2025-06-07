<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['Role'] != 'Passenger') {
    header("Location: login.php");
    exit();
}

include 'db.php';

if (!isset($_GET['ride_id']) || !isset($_GET['driver_id'])) {
    die("Invalid request.");
}

$ride_id = intval($_GET['ride_id']);
$driver_id = intval($_GET['driver_id']);
$passenger_id = $_SESSION['UserID'];

// Fetch ride details
$ride_query = "SELECT * FROM rides WHERE RideID = $ride_id";
$ride_result = mysqli_query($conn, $ride_query);
$ride = mysqli_fetch_assoc($ride_result);

if (!$ride) {
    die("Ride not found.");
}

// Fetch driver details
$driver_query = "SELECT Name, Email, Phone, ProfilePicture FROM users WHERE UserID = $driver_id";
$driver_result = mysqli_query($conn, $driver_query);
$driver = mysqli_fetch_assoc($driver_result);

if (!$driver) {
    die("Driver not found.");
}

// Fetch car details
$car_details = null;
if (!empty($ride['CarID'])) {
    $car_id = intval($ride['CarID']);
    $car_query = "SELECT CarModel, CarType, Capacity, CarNumber FROM cars WHERE CarID = $car_id";
    $car_result = mysqli_query($conn, $car_query);
    $car_details = mysqli_fetch_assoc($car_result);
}

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
    <table class="table table-bordered">
        <tr><th>From</th><td><?php echo htmlspecialchars($ride['StartLocation']); ?></td></tr>
        <tr><th>To</th><td><?php echo htmlspecialchars($ride['EndLocation']); ?></td></tr>
        <tr><th>Date & Time</th><td><?php echo date('d M Y, H:i', strtotime($ride['DateTime'])); ?></td></tr>
        <tr><th>Fare Per Seat</th><td>₹<?php echo htmlspecialchars($ride['FarePerSeat']); ?></td></tr>
    </table>
    
    <h3>Driver Details</h3>
    <table class="table table-bordered">
        <tr><th>Name</th><td><?php echo htmlspecialchars($driver['Name']); ?></td></tr>
        <tr><th>Email</th><td><?php echo htmlspecialchars($driver['Email']); ?></td></tr>
        <tr><th>Phone</th><td><?php echo htmlspecialchars($driver['Phone']); ?></td></tr>
        <tr><th>Profile Picture</th>
            <td>
                <?php if (!empty($driver['ProfilePicture'])): ?>
                    <img src="images/<?php echo htmlspecialchars($driver['ProfilePicture']); ?>" class="img-thumbnail" width="80">
                <?php else: ?>
                    No Profile Picture
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <?php if ($car_details): ?>
        <h3>Car Details</h3>
        <table class="table table-bordered">
            <tr><th>Model</th><td><?php echo htmlspecialchars($car_details['CarModel']); ?></td></tr>
            <tr><th>Type</th><td><?php echo htmlspecialchars($car_details['CarType']); ?></td></tr>
            <tr><th>Capacity</th><td><?php echo htmlspecialchars($car_details['Capacity']); ?></td></tr>
            <tr><th>Car Number</th><td><?php echo htmlspecialchars($car_details['CarNumber']); ?></td></tr>
        </table>
    <?php endif; ?>

    <a href="passenger_ride_managment.php" class="btn btn-secondary">Back</a>
    <a href="chat.php?driver_id=<?php echo $driver_id; ?>&ride_id=<?php echo $ride_id; ?>" class="btn btn-primary">Chat with Driver</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include 'passenger_footer.php'; ?>

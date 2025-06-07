<?php
session_start();
if (!isset($_SESSION['UserID']) || $_SESSION['Role'] != 'Passenger') {
    header("Location: login.php");
    exit();
}

include 'db.php';

$passenger_id = $_SESSION['UserID'];

// Fetch notifications with correct joins
$query = "
    SELECT n.*, r.RideID, u.Name AS DriverName 
    FROM notifications n
    LEFT JOIN rides r ON n.ride_id = r.RideID
    LEFT JOIN users u ON r.DriverID = u.UserID
    WHERE n.user_id = '$passenger_id'
    ORDER BY n.created_at DESC
";

$result = mysqli_query($conn, $query);

$todays_notifications = [];
$older_notifications = [];

while ($row = mysqli_fetch_assoc($result)) {
    $createdDate = date('Y-m-d', strtotime($row['created_at']));
    $today = date('Y-m-d');
    if ($createdDate === $today) {
        $todays_notifications[] = $row;
    } else {
        $older_notifications[] = $row;
    }
}

include 'passenger_header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f9fafe;
        }
        .notification-card {
            border: 1px solid #dee2e6;
            border-radius: 16px;
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        .notification-card:hover {
            transform: scale(1.01);
        }
        .card-title {
            font-weight: 600;
            color: #0d6efd;
        }
        .badge-date {
            font-size: 0.8rem;
            background-color: #e2e6ea;
            color: #6c757d;
        }
        .section-header {
            margin-top: 40px;
            margin-bottom: 20px;
            border-left: 5px solid #0d6efd;
            padding-left: 10px;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <h2 class="section-header text-dark">🔔 Your Notifications</h2>

    <?php if (count($todays_notifications) > 0): ?>
        <h5 class="text-success mb-3">🟢 Today’s Notifications</h5>
        <div class="row g-4">
            <?php foreach ($todays_notifications as $n): ?>
                <div class="col-md-6">
                    <div class="p-3 notification-card">
                        <h6 class="card-title">🚘 <?php echo htmlspecialchars($n['message']); ?></h6>
                        <?php if (!empty($n['DriverName'])): ?>
                            <p class="mb-1"><strong>Driver:</strong> <?php echo htmlspecialchars($n['DriverName']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($n['RideID'])): ?>
                            <p class="mb-1"><strong>Ride ID:</strong> <?php echo $n['RideID']; ?></p>
                            <a href="passenger_ride_managment.php?id=<?php echo $n['RideID']; ?>" class="btn btn-sm btn-outline-primary mt-2">View Details</a>
                        <?php endif; ?>
                        <span class="badge badge-date mt-2">Today @ <?php echo date('h:i A', strtotime($n['created_at'])); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (count($older_notifications) > 0): ?>
        <h5 class="text-secondary mt-5 mb-3">📁 Older Notifications</h5>
        <div class="row g-4">
            <?php foreach ($older_notifications as $n): ?>
                <div class="col-md-6">
                    <div class="p-3 notification-card">
                        <h6 class="card-title">📬 <?php echo htmlspecialchars($n['message']); ?></h6>
                        <?php if (!empty($n['DriverName'])): ?>
                            <p class="mb-1"><strong>Driver:</strong> <?php echo htmlspecialchars($n['DriverName']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($n['RideID'])): ?>
                            <p class="mb-1"><strong>Ride ID:</strong> <?php echo $n['RideID']; ?></p>
                            <a href="passenger_ride_managment.php?id=<?php echo $n['RideID']; ?>" class="btn btn-sm btn-outline-secondary mt-2">View Details</a>
                        <?php endif; ?>
                        <span class="badge badge-date mt-2"><?php echo date('d M Y, h:i A', strtotime($n['created_at'])); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($todays_notifications) && empty($older_notifications)): ?>
        <div class="alert alert-secondary text-center mt-4">No notifications yet.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include 'passenger_footer.php'; ?>

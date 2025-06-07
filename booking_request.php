<?php
session_start();
include 'db.php';

// Only drivers allowed
if (!isset($_SESSION['UserID']) || $_SESSION['Role'] != 'Driver') {
    header("Location: login.php");
    exit();
}

$driver_id = $_SESSION['UserID'];

// Handle approve/reject actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['booking_id'], $_POST['action'])) {
    $booking_id = $_POST['booking_id'];
    $action = $_POST['action'];

    $status = ($action == 'approve') ? 'confirmed' : 'rejected';
    $message = ($action == 'approve') ? "Your ride request has been approved!" : "Your ride request has been rejected.";

    $updateQuery = $conn->prepare("UPDATE bookings SET Status = ? WHERE BookingID = ?");
    $updateQuery->bind_param("si", $status, $booking_id);

    if ($updateQuery->execute()) {
        $passengerQuery = $conn->prepare("SELECT PassengerID FROM bookings WHERE BookingID = ?");
        $passengerQuery->bind_param("i", $booking_id);
        $passengerQuery->execute();
        $result = $passengerQuery->get_result();

        if ($passengerRow = $result->fetch_assoc()) {
            $passenger_id = $passengerRow['PassengerID'];
            $notifSql = $conn->prepare("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (?, ?, 0, NOW())");
            $notifSql->bind_param("is", $passenger_id, $message);
            $notifSql->execute();
        }

        $_SESSION['message'] = "Ride request has been updated successfully!";
    } else {
        $_SESSION['error'] = "Error: " . $conn->error;
    }
    header("Location: booking_request.php");
    exit();
}

// Fetch pending requests
$sql = "SELECT b.BookingID, u.Name AS PassengerName, r.StartLocation, r.EndLocation, r.DateTime, b.Status
        FROM bookings b
        JOIN rides r ON b.RideID = r.RideID
        JOIN users u ON b.PassengerID = u.UserID
        WHERE r.DriverID = ? AND b.Status = 'pending' 
        ORDER BY r.DateTime ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$result = $stmt->get_result();

include 'driver_header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Requests</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f9fc;
        }
        .booking-card {
            border-left: 6px solid #0d6efd;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            background-color: #fff;
        }
        .booking-header {
            font-size: 1.25rem;
            font-weight: bold;
            color: #0d6efd;
        }
        .btn-action {
            width: 100px;
        }
        .badge-status {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
<div class="container py-5">
    <h2 class="text-center mb-4 text-primary">Ride Booking Requests</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($request = $result->fetch_assoc()): ?>
            <div class="card booking-card mb-4">
                <div class="card-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <p class="booking-header mb-1"><?= htmlspecialchars($request['PassengerName']) ?></p>
                            <p class="mb-0"><strong>From:</strong> <?= htmlspecialchars($request['StartLocation']) ?></p>
                            <p><strong>To:</strong> <?= htmlspecialchars($request['EndLocation']) ?></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Date & Time:</strong> <?= date('d M Y, H:i', strtotime($request['DateTime'])) ?></p>
                            <span class="badge bg-warning text-dark badge-status"><?= ucfirst($request['Status']) ?></span>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="booking_id" value="<?= $request['BookingID'] ?>">
                                <button type="submit" name="action" value="approve" class="btn btn-success btn-sm btn-action me-2">
                                    ✅ Approve
                                </button>
                            </form>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="booking_id" value="<?= $request['BookingID'] ?>">
                                <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm btn-action">
                                    ❌ Reject
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-info text-center">
            No ride requests available at the moment.
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include 'driver_footer.php'; ?>

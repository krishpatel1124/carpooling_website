<?php
session_start();

if (!isset($_SESSION['UserID']) || $_SESSION['Role'] != 'Passenger') {
    header("Location: login.php");
    exit();
}

include 'db.php';

$user_id = $_SESSION['UserID'];
$ride_id = $_GET['ride_id'];
$driver_id = $_GET['driver_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rating = $_POST['rating'];
    $comments = mysqli_real_escape_string($conn, $_POST['comments']);

    $sql = "INSERT INTO review (RideID, PassengerID, DriverID, Rating, Comments, ReviewDate) 
            VALUES ($ride_id, $user_id, $driver_id, $rating, '$comments', NOW())";

    if (mysqli_query($conn, $sql)) {
        header("Location: passenger_dashboard.php?success=Review submitted successfully");
        exit();
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>

<?php include 'passenger_header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Give Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Give Feedback</h2>

    <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="rating" class="form-label">Rating</label>
            <select class="form-control" id="rating" name="rating" required>
                <option value="5">⭐⭐⭐⭐⭐ (Excellent)</option>
                <option value="4">⭐⭐⭐⭐ (Very Good)</option>
                <option value="3">⭐⭐⭐ (Good)</option>
                <option value="2">⭐⭐ (Fair)</option>
                <option value="1">⭐ (Poor)</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="comments" class="form-label">Comments</label>
            <textarea class="form-control" id="comments" name="comments" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-success">Submit Feedback</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include 'passenger_footer.php'; ?>

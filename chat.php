<?php
session_start();
include 'db.php';

if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['ride_id']) || !isset($_GET['driver_id'])) {
    die("Invalid request. Missing ride_id or driver_id.");
}

$ride_id = intval($_GET['ride_id']);
$driver_id = intval($_GET['driver_id']);
$passenger_id = $_SESSION['UserID'];

// Fetch messages from chats table
$chat_query = "SELECT * FROM chats WHERE RideID = $ride_id AND (SenderID = $passenger_id OR ReceiverID = $passenger_id) ORDER BY Timestamp ASC";
$chat_result = mysqli_query($conn, $chat_query);

// Send message
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $insert_query = "INSERT INTO chats (RideID, SenderID, ReceiverID, Message) VALUES ($ride_id, $passenger_id, $driver_id, '$message')";
    mysqli_query($conn, $insert_query);
    header("Location: chat.php?driver_id=$driver_id&ride_id=$ride_id");
    exit();
}

include 'passenger_header.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat with Driver</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Chat with Driver</h2>
    <div class="chat-box border p-3" style="height: 300px; overflow-y: scroll;">
        <?php while ($row = mysqli_fetch_assoc($chat_result)): ?>
            <p><strong><?php echo ($row['SenderID'] == $passenger_id) ? 'You' : 'Driver'; ?>:</strong> 
            <?php echo htmlspecialchars($row['Message']); ?> 
            <small class="text-muted">(<?php echo $row['Timestamp']; ?>)</small></p>
        <?php endwhile; ?>
    </div>
    <form method="post" class="mt-3">
        <div class="input-group">
            <input type="text" name="message" class="form-control" placeholder="Type a message..." required>
            <button class="btn btn-primary" type="submit">Send</button>
        </div>
    </form>
</div>
</body>
</html>

<?php include 'passenger_footer.php'; ?>

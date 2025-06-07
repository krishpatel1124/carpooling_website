<?php
session_start();
include 'db.php';

$passenger_id = $_SESSION['UserID'];
$ride_id = intval($_GET['ride_id']);
$driver_id = intval($_GET['driver_id']);

$chat_query = "SELECT * FROM chats 
               WHERE RideID = $ride_id AND 
               ((SenderID = $passenger_id AND ReceiverID = $driver_id) 
                OR (SenderID = $driver_id AND ReceiverID = $passenger_id))
               ORDER BY Timestamp ASC";

$chat_result = mysqli_query($conn, $chat_query);

while ($chat = mysqli_fetch_assoc($chat_result)) {
    $is_passenger = ($chat['SenderID'] == $passenger_id);
    $class = $is_passenger ? "sent" : "received";
    echo "<div class='message $class'>{$chat['Message']}<br><small class='text-muted'>" . date('d M Y, H:i', strtotime($chat['Timestamp'])) . "</small></div>";
}
?>

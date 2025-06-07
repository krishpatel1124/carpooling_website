<?php
session_start();
include 'db.php';

$driver_id = $_SESSION['UserID'];
$ride_id = intval($_GET['ride_id']);
$passenger_id = intval($_GET['passenger_id']);

$chat_query = "SELECT * FROM chats 
               WHERE RideID = $ride_id AND 
               ((SenderID = $driver_id AND ReceiverID = $passenger_id) 
                OR (SenderID = $passenger_id AND ReceiverID = $driver_id))
               ORDER BY Timestamp ASC";

$chat_result = mysqli_query($conn, $chat_query);

while ($chat = mysqli_fetch_assoc($chat_result)) {
    $is_driver = ($chat['SenderID'] == $driver_id);
    $class = $is_driver ? "sent" : "received";
    echo "<div class='message $class'>{$chat['Message']}<br><small class='text-muted'>" . date('d M Y, H:i', strtotime($chat['Timestamp'])) . "</small></div>";
}
?>

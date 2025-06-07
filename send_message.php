<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    die('Not logged in');
}

if (isset($_POST['ride_id']) && isset($_POST['message'])) {
    $ride_id = $_POST['ride_id'];
    $message = $_POST['message'];
    $sender_id = $_SESSION['UserID'];

    // Insert the message into the chat table
    $sql = "INSERT INTO chat (RideID, SenderID, Message, Timestamp) VALUES ($ride_id, $sender_id, '$message', NOW())";

    if (mysqli_query($conn, $sql)) {
        echo 'Message sent';
    } else {
        echo 'Error: ' . mysqli_error($conn);
    }
}
?>

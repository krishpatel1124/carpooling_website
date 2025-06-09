<?php
// Database connection settings for db4free.net
$servername = "db4free.net"; // Host of the remote database
$username = "your_db_username"; // Replace with your db4free username
$password = "your_db_password"; // Replace with your db4free password
$dbname = "ride"; // Database name you created on db4free

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Uncomment below to test successful connection
// echo "Database connected successfully!";
?>

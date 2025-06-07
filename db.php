<?php
// Database connection settings
$servername = "localhost";
$username = "root"; // Default username for WAMP
$password = "";     // Default password for WAMP (empty)
$dbname = "ride"; // The name of the database you created

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

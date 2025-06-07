<?php
$servername = "localhost";
$username = "root"; 
$password = ""; // If your MySQL has a password, add it here
$database = "ride"; 

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

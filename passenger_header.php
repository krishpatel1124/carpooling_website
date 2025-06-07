<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Pooling Website</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; }
        .header {
            display: flex;
            align-items: center;
            background-color: #4B014B; /* Darker Purple */
            color: #fff;
            padding: 10px 20px;
        }
        .logo {
            margin-right: 30px;
        }
        .logo img {
            height: 40px;
        }
        .nav {
            display: flex;
            gap: 20px;
            flex-grow: 1;
        }
        .nav a {
            color: #fff;
            text-decoration: none;
            padding: 8px 15px;
        }
        .nav a:hover {
            background-color: #6a0572;
            border-radius: 5px;
        }
        .profile {
            cursor: pointer;
        }
        .profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
        .footer {
            background-color: #4B014B;
            color: #fff;
            text-align: center;
            padding: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="logo">
        <a href="passenger_dashboard.php">
            <img src="images/IMG_20250329_093158.jpg" alt="Logo">
        </a>
    </div>
    <div class="nav">
        <a href="book_ride.php">Book Ride</a>
        <a href="passenger_ride_managment.php">Manage Ride</a>
        <a href="passenger_notifications.php">Notification</a>
        <a href="passenger_past_rides.php">past rides</a>
        <a href="passenger_chat.php">chats</a>
    </div>
    <div class="profile">
        <a href="profile.php">
            <img src="images/WhatsApp Image 2025-03-29 at 00.38.45_d8201df7.jpg" alt="Profile">
        </a>
    </div>
</div>

</body>
</html>

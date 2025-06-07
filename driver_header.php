<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Panel</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
        }
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            background-color: rgb(166, 10, 161); 
            padding: 15px 20px; 
            color: white; 
        }
        .left-section {
            display: flex;
            align-items: center;
        }
        .logo img { 
            width: 100px; 
            height: 40px; 
            margin-right: 20px;
        }
        .nav-links {
            display: flex;
            align-items: center;
        }
        .nav-links a { 
            color: white; 
            margin: 0 10px; 
            text-decoration: none; 
            font-weight: bold; 
            transition: color 0.3s; 
        }
        .nav-links a:hover { 
            color: #000; 
            text-decoration: underline; 
        }
        .profile img { 
            width: 40px; 
            height: 40px; 
            border-radius: 50%; 
            cursor: pointer; 
        }
    </style>
</head>
<body>
    <div class="header">
        <!-- Left section: Logo + Nav links -->
        <div class="left-section">
            <div class="logo">
                
                    <img src="images/IMG_20250329_093158.jpg" alt="Logo">
                </a>
            </div>
            <div class="nav-links">
                <a href="driver_dashboard.php">Home</a>
                <a href="driver_chat.php">Chat</a>
                <a href="create_ride.php">Create Ride</a>
                <a href="manage_ride_requests.php">Manage Ride</a>
                <a href="booking_request.php">Booking Requests</a>
            </div>
        </div>

        <!-- Right section: Profile image -->
        <div class="profile">
            <a href="profile.php">
                <img src="images/WhatsApp Image 2025-03-29 at 00.38.45_d8201df7.jpg" alt="Profile">
            </a>
        </div>
    </div>
</body>
</html>

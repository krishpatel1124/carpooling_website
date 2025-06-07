<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Pooling Website</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .footer {
            background-color: #0078FF;
            color: #fff;
            padding: 40px 20px 20px;
            font-family: Arial, sans-serif;
            margin-top: 50px;
        }

        .footer-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-column {
            flex: 1 1 250px;
            margin: 20px;
        }

        .footer h4 {
            font-size: 18px;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .footer a {
            display: block;
            color: #fff;
            text-decoration: none;
            margin: 6px 0;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: #cce7ff;
        }

        .social-icons a {
            margin-right: 15px;
            font-size: 18px;
            color: #fff;
            transition: transform 0.3s ease;
        }

        .social-icons a:hover {
            transform: scale(1.2);
            color: #cce7ff;
        }

        .footer-bottom {
            text-align: center;
            border-top: 1px solid rgba(255,255,255,0.2);
            padding-top: 15px;
            margin-top: 20px;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .footer-container {
                flex-direction: column;
                align-items: center;
            }
            .footer-column {
                margin: 10px 0;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<div class="footer">
    <div class="footer-container">
        <div class="footer-column">
            <h4>About Us</h4>
            <p>Our carpooling platform helps passengers and drivers share rides safely and affordably.</p>
        </div>
        <div class="footer-column">
            <h4>Quick Links</h4>
            <a href="passenger_dashboard.php">Dashboard</a>
            <a href="book_ride.php">Book a Ride</a>
            <a href="passenger_ride_managment.php">Manage Rides</a>
            <a href="edit_profile.php">Edit Profile</a>
        </div>
        <div class="footer-column">
            <h4>Connect with Us</h4>
            <div class="social-icons">
                <a href="https://www.facebook.com/share/1FNF8qX14S/"><i class="fab fa-facebook-f"></i></a>
           
                <a href="https://www.instagram.com/myride111?igsh=a2l3ZDNnMXRmM2Jp"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; <?php echo date('Y'); ?> Car Pooling Website. All rights reserved.
    </div>
</div>

</body>
</html>

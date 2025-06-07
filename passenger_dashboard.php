<?php
include 'passenger_header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #e0f7fa, #ffffff);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .hero-section {
            background: #004d40;
            color: white;
            padding: 60px 20px;
            text-align: center;
            border-radius: 20px;
            margin-top: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        .hero-section h1 {
            font-size: 2.5rem;
            font-weight: 700;
        }
        .hero-section p {
            font-size: 1.2rem;
            margin-top: 10px;
        }

        .carousel {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-top: 40px;
        }
        .carousel img {
            max-height: 400px;
            object-fit: cover;
        }
        .carousel-caption-custom {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(0, 77, 64, 0.8);
            padding: 10px 25px;
            border-radius: 12px;
            font-size: 1.1rem;
        }
        .carousel-caption-custom a {
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
        }

        .dashboard-cards {
            margin-top: 50px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }

        .card-custom {
            background: white;
            border: none;
            border-radius: 15px;
            text-align: center;
            padding: 30px 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card-custom:hover {
            transform: translateY(-8px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        .card-custom i {
            font-size: 2rem;
            color: #00796b;
            margin-bottom: 10px;
        }

        .card-custom a {
            display: block;
            margin-top: 10px;
            font-weight: 600;
            color: #004d40;
            text-decoration: none;
        }

        @media (max-width: 576px) {
            .hero-section h1 {
                font-size: 2rem;
            }
        }
    </style>
    <script src="https://kit.fontawesome.com/a2e0e6f91a.js" crossorigin="anonymous"></script>
</head>
<body>

<div class="container">
    <!-- Hero Section -->
    <div class="hero-section">
        <h1><i class="fas fa-car-side me-2"></i>Welcome to Our Carpooling Service</h1>
        <p>Your smart and sustainable travel companion</p>
    </div>

    <!-- Carousel -->
    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">

            <div class="carousel-item active position-relative">
                <img src="images/images (111).jpg" class="d-block w-100" alt="Manage Rides">
                <div class="carousel-caption-custom">
                    <a href="passenger_ride_managment.php">Manage My Rides</a>
                </div>
            </div>

            <div class="carousel-item position-relative">
                <img src="images/pngtree-joint-travel-carpool-service-illustration-banner-image_1325943.jpg" class="d-block w-100" alt="Book Ride">
                <div class="carousel-caption-custom">
                    <a href="book_ride.php">Book a Ride</a>
                </div>
            </div>

        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>

        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- Dashboard Cards -->
    <div class="dashboard-cards mt-5">
        <div class="card-custom">
            <i class="fas fa-car"></i>
            <h5>My Rides</h5>
            <a href="passenger_ride_managment.php">View / Manage</a>
        </div>
        <div class="card-custom">
            <i class="fas fa-search-location"></i>
            <h5>Book a Ride</h5>
            <a href="book_ride.php">Start Booking</a>
        </div>
        <div class="card-custom">
            <i class="fas fa-history"></i>
            <h5>Past Rides</h5>
            <a href="past_rides.php">View History</a>
        </div>
        <div class="card-custom">
            <i class="fas fa-user-edit"></i>
            <h5>Edit Profile</h5>
            <a href="edit_profile.php">Update Info</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<?php
include 'passenger_footer.php';
?>
</body>
</html>

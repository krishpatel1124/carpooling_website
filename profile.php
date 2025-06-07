<?php
session_start();
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Fetch user profile details
$sql = "SELECT * FROM users WHERE UserID = " . $_SESSION['UserID'];
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Check user role
$profileTitle = ($user['Role'] === 'Driver') ? "Driver Profile" : "Passenger Profile";

// Determine back URL
$backUrl = ($user['Role'] === 'Driver') ? "driver_dashboard.php" : "passenger_dashboard.php";

// Profile Image Logic
$profileImage = !empty($user['ProfilePicture']) ? $user['ProfilePicture'] : 'default_profile.jpg';

?>

<?php include 'header.php'; ?>

<!-- Custom CSS for Profile Design -->
<style>
    .profile-card {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 15px;
        padding: 30px;
        max-width: 400px;
        margin: 50px auto;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .profile-header {
        background-color: #c3baf8;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
        padding: 40px 0;
    }

    .profile-image {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 3px solid #fff;
        margin-top: -50px;
    }

    .profile-title {
        font-size: 22px;
        font-weight: bold;
        margin-top: 10px;
    }

    .profile-info {
        padding: 20px 0;
    }

    .profile-info p {
        margin: 5px 0;
        font-size: 16px;
    }

    .profile-options {
        list-style: none;
        padding: 0;
        margin: 20px 0 0;
        text-align: left;
    }

    .profile-options li {
        padding: 10px 15px;
        border-top: 1px solid #ddd;
    }

    .profile-options a {
        text-decoration: none;
        color: #007bff;
        font-weight: 500;
    }

    .profile-options a:hover {
        text-decoration: underline;
    }
</style>

<div class="profile-card">
    <div class="profile-header"></div>
    <img src="images/<?php echo htmlspecialchars($profileImage); ?>" alt="Profile Image" class="profile-image">
    
    <h2 class="profile-title"><?php echo htmlspecialchars($user['Name']); ?></h2>

    <div class="profile-info">
        <p><strong>phone no:</strong> <?php echo htmlspecialchars($user['Phone']); ?></p>
        <p><strong>Mail:</strong> <?php echo htmlspecialchars($user['Email']); ?></p>
    </div>

    <ul class="profile-options">
        <li>✏️ <a href="edit_profile.php">Edit Profile</a></li>
        <li>🏠 <a href="<?php echo $backUrl; ?>">Back to Home</a></li>
        <li>↩️ <a href="logout.php" style="color: #dc3545;">Log out</a></li>
    </ul>
</div>

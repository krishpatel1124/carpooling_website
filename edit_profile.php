<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Edit</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f9f9f9; }
        .container { max-width: 600px; margin-top: 50px; background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        .form-label { font-weight: bold; }
        .btn-primary { background-color: rgb(166, 10, 161); border: none; }
        .btn-primary:hover { background-color: #9a078b; }
        .profile-image { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Edit Profile</h2>
    <?php
    session_start();
    if (!isset($_SESSION['UserID'])) {
        header("Location: login.php");
        exit();
    }

    include 'db.php';

    $stmt = $conn->prepare("SELECT * FROM users WHERE UserID = ?");
    $stmt->bind_param("i", $_SESSION['UserID']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        echo "<div class='alert alert-danger'>User not found.</div>";
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);

        $profileImage = $user['ProfilePicture'];
        if (!empty($_FILES['profile_image']['name'])) {
            $targetDir = "images/";
            $profileImage = basename($_FILES['profile_image']['name']);
            $targetFilePath = $targetDir . $profileImage;
            $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($imageFileType, $allowedTypes)) {
                echo "<div class='alert alert-danger'>Invalid image format. Please upload JPG, JPEG, PNG, or GIF files.</div>";
            } elseif (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFilePath)) {
                echo "<div class='alert alert-danger'>Image upload failed. Please try again.</div>";
            }
        }

        $stmt = $conn->prepare("UPDATE users SET Name = ?, Phone = ?, Email = ?, ProfilePicture = ? WHERE UserID = ?");
        $stmt->bind_param("ssssi", $name, $phone, $email, $profileImage, $_SESSION['UserID']);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Profile updated successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error updating profile. Please try again.</div>";
        }
    }
    ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="text-center">
            <img src="images/<?php echo $user['ProfilePicture']; ?>" alt="Profile Image" class="profile-image">
        </div>

        <div class="mb-3">
            <label class="form-label">Name:</label>
            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user['Name']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Phone:</label>
            <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['Phone']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email:</label>
            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Profile Image:</label>
            <input type="file" class="form-control" name="profile_image">
        </div>

        <button type="submit" class="btn btn-primary w-100">Update Profile</button>
        <a href="profile.php" class="btn btn-secondary w-100 mt-3">Back to Profile</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

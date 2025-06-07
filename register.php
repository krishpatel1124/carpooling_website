<?php
include 'db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Trim and sanitize inputs
    $name = trim(mysqli_real_escape_string($conn, $_POST['name']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $phone = trim(mysqli_real_escape_string($conn, $_POST['phone']));
    $password_raw = trim($_POST['password']);
    $role = trim(mysqli_real_escape_string($conn, $_POST['role']));

    // Server-side validations
    if (empty($name) || empty($email) || empty($phone) || empty($password_raw) || empty($role)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $error = "Phone number must be 10 digits.";
    } elseif (strlen($password_raw) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check for duplicate email
        $checkEmail = "SELECT * FROM users WHERE Email='$email'";
        $result = mysqli_query($conn, $checkEmail);

        if (mysqli_num_rows($result) > 0) {
            $error = "Email is already registered!";
        } else {
            $password = password_hash($password_raw, PASSWORD_BCRYPT);
            $sql = "INSERT INTO users (Name, Email, Phone, Password, Role) 
                    VALUES ('$name', '$email', '$phone', '$password', '$role')";

            if (mysqli_query($conn, $sql)) {
                header("Location: login.php");
                exit();
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Carpool System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #3F3D56, #6C63FF);
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-card {
            background: white;
            border-radius: 16px;
            padding: 40px 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
        }

        .register-card h2 {
            font-weight: 600;
            margin-bottom: 25px;
            text-align: center;
            color: #3F3D56;
        }

        .form-label {
            font-weight: 500;
        }

        .btn-primary {
            background-color: #6C63FF;
            border: none;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background-color: #554ddb;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .login-link a {
            color: #6C63FF;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .alert {
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="register-card">
    <h2>Create an Account</h2>

    <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <form method="POST" action="register.php" onsubmit="return validateForm();">
        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="name" name="name" required placeholder="Your full name">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" required placeholder="example@email.com">
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="text" class="form-control" id="phone" name="phone" required placeholder="9876543210" maxlength="10">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Create Password</label>
            <input type="password" class="form-control" id="password" name="password" required placeholder="Minimum 6 characters" minlength="6">
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Select Role</label>
            <select class="form-select" id="role" name="role" required>
                <option value="">-- Choose Role --</option>
                <option value="Passenger">Passenger</option>
                <option value="Driver">Driver</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary w-100">Register</button>
    </form>

    <div class="login-link">
        Already have an account? <a href="login.php">Login here</a>
    </div>
</div>

<script>
    function validateForm() {
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const password = document.getElementById('password').value.trim();
        const role = document.getElementById('role').value;

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const phoneRegex = /^[0-9]{10}$/;

        if (!emailRegex.test(email)) {
            alert("Please enter a valid email address.");
            return false;
        }

        if (!phoneRegex.test(phone)) {
            alert("Phone number must be exactly 10 digits.");
            return false;
        }

        if (password.length < 6) {
            alert("Password must be at least 6 characters long.");
            return false;
        }

        if (!role) {
            alert("Please select a role.");
            return false;
        }

        return true;
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

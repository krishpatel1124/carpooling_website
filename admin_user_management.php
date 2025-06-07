<?php
// admin_user_management.php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$message = "";

// Helper function to sanitize input
function clean_input($data) {
    return htmlspecialchars(trim($data));
}

// Add user functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $name = clean_input($_POST['name']);
    $email = clean_input($_POST['email']);
    $phone = clean_input($_POST['phone']);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='alert alert-danger text-center'>Invalid email format.</div>";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $message = "<div class='alert alert-danger text-center'>Phone number must be 10 digits.</div>";
    } elseif (strlen($password) < 6) {
        $message = "<div class='alert alert-danger text-center'>Password must be at least 6 characters.</div>";
    } else {
        $check = $conn->prepare("SELECT * FROM users WHERE Email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result_check = $check->get_result();
        if ($result_check->num_rows > 0) {
            $message = "<div class='alert alert-danger text-center'>Email already exists.</div>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (Name, Email, Phone, Password, Role) VALUES (?, ?, ?, ?, 'Passenger')");
            $stmt->bind_param("ssss", $name, $email, $phone, $hashed_password);
            $stmt->execute();
            $message = "<div class='alert alert-success text-center'>User added successfully.</div>";
        }
    }
}

// Update user functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    $user_id = intval($_POST['user_id']);
    $name = clean_input($_POST['name']);
    $email = clean_input($_POST['email']);
    $phone = clean_input($_POST['phone']);
    $role = clean_input($_POST['role']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='alert alert-danger text-center'>Invalid email format.</div>";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $message = "<div class='alert alert-danger text-center'>Phone number must be 10 digits.</div>";
    } else {
        $stmt = $conn->prepare("UPDATE users SET Name=?, Email=?, Phone=?, Role=? WHERE UserID=?");
        $stmt->bind_param("ssssi", $name, $email, $phone, $role, $user_id);
        $stmt->execute();
        $message = "<div class='alert alert-success text-center'>User updated successfully.</div>";
    }
}

// Delete user functionality
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM users WHERE UserID = $delete_id");
    $message = "<div class='alert alert-success text-center'>User deleted successfully.</div>";
}

// Fetch users
$result = $conn->query("SELECT * FROM users");
?>

<?php include "admin_header.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f4f8;
        }
        .card {
            border-radius: 1rem;
        }
        .table th {
            background-color: #0d6efd;
            color: white;
        }
    </style>
</head>
<body>
<div class="container my-5">
    <h2 class="text-center mb-4">👥 Admin User Management</h2>

    <?php if ($message) echo $message; ?>

    <!-- Add User Card -->
    <div class="card mb-4 shadow">
        <div class="card-header bg-success text-white">Add New User</div>
        <div class="card-body">
            <form method="POST" novalidate>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Name:</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Email:</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Phone:</label>
                        <input type="text" class="form-control" name="phone" required pattern="[0-9]{10}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Password:</label>
                        <input type="password" class="form-control" name="password" required minlength="6">
                    </div>
                </div>
                <div class="text-end mt-3">
                    <button type="submit" name="add_user" class="btn btn-success">Add User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- User Table -->
    <div class="card mb-4 shadow">
        <div class="card-header bg-primary text-white">User List</div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th style="width: 160px;">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($user = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $user['UserID']; ?></td>
                        <td><?php echo $user['Name']; ?></td>
                        <td><?php echo $user['Email']; ?></td>
                        <td><?php echo $user['Phone']; ?></td>
                        <td><?php echo $user['Role']; ?></td>
                        <td>
                            <a href="?delete_id=<?php echo $user['UserID']; ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure?')">Delete</a>
                            <button class="btn btn-warning btn-sm"
                                    onclick="editUser(<?php echo $user['UserID']; ?>, '<?php echo $user['Name']; ?>', '<?php echo $user['Email']; ?>', '<?php echo $user['Phone']; ?>', '<?php echo $user['Role']; ?>')">Edit
                            </button>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Update User Card -->
    <div class="card shadow">
        <div class="card-header bg-warning">Update User</div>
        <div class="card-body">
            <form method="POST" novalidate>
                <input type="hidden" name="user_id" id="user_id">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Name:</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Email:</label>
                        <input type="email" class="form-control" name="email" id="email" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Phone:</label>
                        <input type="text" class="form-control" name="phone" id="phone" required pattern="[0-9]{10}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Role:</label>
                        <select name="role" id="role" class="form-select" required>
                            <option value="Passenger">Passenger</option>
                            <option value="Driver">Driver</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="text-end mt-3">
                    <button type="submit" name="update_user" class="btn btn-warning">Update User</button>
                </div>
            </form>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="admin_dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>
</div>

<script>
    function editUser(userId, name, email, phone, role) {
        document.getElementById('user_id').value = userId;
        document.getElementById('name').value = name;
        document.getElementById('email').value = email;
        document.getElementById('phone').value = phone;
        document.getElementById('role').value = role;
        window.scrollTo({top: document.body.scrollHeight, behavior: 'smooth'});
    }
</script>
</body>
</html>

<!-- Admin Header/Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="admin_dashboard.php">🚗 Carpool Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? ' active' : ''; ?>" href="admin_dashboard.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'admin_ride_management.php' ? ' active' : ''; ?>" href="admin_ride_management.php">Ride Manage</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'admin_user_management.php' ? ' active' : ''; ?>" href="admin_user_management.php">User Manage</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link<?php echo basename($_SERVER['PHP_SELF']) == 'admin_report.php' ? ' active' : ''; ?>" href="admin_report.php">Reports</a>
                </li>
                
            </ul>
        </div>
    </div>
</nav>

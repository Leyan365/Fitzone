<?php
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/fitness_features.php';
include('db.php');

$current_user = require_login($conn);
$user_id = (int) $current_user['id'];
$user_role = $current_user['role'];
$user_name = $current_user['name'];

if (!is_allowed_role($user_role)) {
    http_response_code(403);
    exit('Access denied.');
}

ensure_fitness_feature_tables($conn);

// The main layout for all dashboards
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - FitZone</title>
    <?php include('includes/head_assets.php'); ?>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body class="dashboard-page d-flex flex-column min-vh-100">
    <?php include('includes/header.php'); ?>

    <main class="dashboard-shell flex-grow-1">
        <div class="container-fluid">
            <div class="row g-0">
                <nav class="dashboard-sidebar col-lg-2 col-md-3">
                    <div class="position-sticky">
                        <h4 class="sidebar-heading">
                            <span class="sidebar-mark"><i class="bi bi-grid-1x2-fill"></i></span>
                            <span>Dashboard</span>
                        </h4>
                        <ul class="nav flex-column dashboard-nav">
                            <li class="nav-item">
                                <a class="nav-link active" href="dashboard.php">
                                    <i class="bi bi-house-door-fill"></i> Overview
                                </a>
                            </li>
                            <?php if ($user_role === 'customer'): ?>
                                <li class="nav-item"><a class="nav-link" href="#membership"><i class="bi bi-card-checklist"></i> Membership</a></li>
                                <li class="nav-item"><a class="nav-link" href="#classes"><i class="bi bi-calendar-check"></i> Classes</a></li>
                                <li class="nav-item"><a class="nav-link" href="#queries"><i class="bi bi-chat-dots"></i> Queries</a></li>
                            <?php else: ?>
                                <li class="nav-item"><a class="nav-link" href="#memberships"><i class="bi bi-card-checklist"></i> Memberships</a></li>
                                <li class="nav-item"><a class="nav-link" href="#bookings"><i class="bi bi-calendar-check"></i> Bookings</a></li>
                                <li class="nav-item"><a class="nav-link" href="#queries"><i class="bi bi-chat-dots"></i> Queries</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </nav>

                <div class="dashboard-content col-lg-10 col-md-9">
                    <div class="dashboard-hero">
                        <div>
                            <p class="dashboard-eyebrow"><?php echo e(ucfirst($user_role)); ?> Portal</p>
                            <h1>Welcome, <?php echo e($user_name); ?></h1>
                            <p class="dashboard-subtitle">Manage memberships, bookings, and customer support from one workspace.</p>
                        </div>
                        <a href="logout.php" class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
                    </div>
                    
                    <?php
                    // Include the correct dashboard view based on user role
                    switch ($user_role) {
                        case 'admin':
                            include('dashboards/admin.php');
                            break;
                        case 'management':
                            include('dashboards/management.php');
                            break;
                        case 'customer':
                        default:
                            include('dashboards/customer.php');
                            break;
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>

    <?php include('includes/footer.php'); ?>

</body>
</html>

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
    <style>
        
        .dashboard-card {
            background-color: #212529; 
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .dashboard-card-header {
            background-color: rgba(0, 0, 0, 0.2);
            font-weight: bold;
        }
        .table-responsive {
            max-height: 400px; 
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <?php include('includes/header.php'); ?>

    <main class="flex-grow-1">
        <div class="container-fluid py-4">
            <div class="row">
          <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse" style="background-color: #000;">
                    <div class="position-sticky pt-3">
                        <h4 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-warning">
                            <span>Dashboard</span>
                        </h4>
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link text-white" href="#">
                                    <i class="bi bi-house-door-fill"></i> Overview
                                </a>
                            </li>
                            </ul>
                    </div>
                </nav>

                <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2 text-white">Welcome, <?php echo e($user_name); ?>!</h1>
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

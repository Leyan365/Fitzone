<?php
// --- Shared Logic for Admin & Management ---
// This handles all backend tasks for deleting and replying to queries.
include(dirname(__FILE__) . '/../includes/query_management_logic.php');

// --- Management-Specific Stat Logic ---
// Let's get a count of active customers for a stat card.
$customer_count = 0;
$stmt = $conn->prepare("SELECT COUNT(id) as count FROM users WHERE role = 'customer'");
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$customer_count = $result['count'];
$stmt->close();

$pending_memberships = pending_membership_count($conn);
$active_bookings = active_booking_count($conn);
?>

<?php if ($query_message) echo $query_message; // Display feedback messages from query logic ?>

<div class="row">
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card text-white dashboard-card h-100">
             <div class="card-header dashboard-card-header d-flex justify-content-between align-items-center">
                New Queries <i class="bi bi-question-circle-fill text-warning fs-3"></i>
            </div>
            <div class="card-body">
                <h2 class="card-title"><?php echo count($queries_for_staff); ?></h2>
                <p class="card-text">Queries assigned to you.</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card text-white dashboard-card h-100">
             <div class="card-header dashboard-card-header d-flex justify-content-between align-items-center">
                Active Members <i class="bi bi-person-check-fill text-success fs-3"></i>
            </div>
            <div class="card-body">
                <h2 class="card-title"><?php echo $customer_count; ?></h2>
                <p class="card-text">Total active customers.</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card text-white dashboard-card h-100">
             <div class="card-header dashboard-card-header d-flex justify-content-between align-items-center">
                Pending Memberships <i class="bi bi-card-checklist text-info fs-3"></i>
            </div>
            <div class="card-body">
                <h2 class="card-title"><?php echo e($pending_memberships); ?></h2>
                <p class="card-text">Plans waiting for staff review.</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card text-white dashboard-card h-100">
             <div class="card-header dashboard-card-header d-flex justify-content-between align-items-center">
                Active Bookings <i class="bi bi-calendar-check-fill text-success fs-3"></i>
            </div>
            <div class="card-body">
                <h2 class="card-title"><?php echo e($active_bookings); ?></h2>
                <p class="card-text">Classes currently booked.</p>
            </div>
        </div>
    </div>
</div>

<div class="card text-white dashboard-card mt-4">
    <div class="card-header dashboard-card-header"><h4>Class Timetable</h4></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-bordered text-center table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Time</th>
                        <th>Monday</th>
                        <th>Tuesday</th>
                        <th>Wednesday</th>
                        <th>Thursday</th>
                        <th>Friday</th>
                        <th>Saturday</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>6:00 AM</td>
                        <td><strong>David Laid</strong> - Strength Training</td>
                        <td><strong>Anna Kaiser</strong> - Yoga & Flexibility</td>
                        <td><strong>David Laid</strong> - Interval Workouts</td>
                        <td><strong>Anna Kaiser</strong> - Yoga & Flexibility</td>
                        <td><strong>David Laid</strong> - Strength Training</td>
                        <td><strong>Sam Sulek</strong> - High-Intensity Cardio</td>
                    </tr>
                    <tr>
                        <td>8:00 AM</td>
                        <td><strong>Sam Sulek</strong> - High-Intensity Cardio</td>
                        <td><strong>Sarah Lee</strong> - Personal Training & Nutrition</td>
                        <td><strong>Sam Sulek</strong> - HIIT</td>
                        <td><strong>Sarah Lee</strong> - Nutrition & Fitness</td>
                        <td><strong>Sam Sulek</strong> - High-Intensity Cardio</td>
                        <td><strong>Anna Kaiser</strong> - Yoga & Meditation</td>
                    </tr>
                    <tr>
                        <td>10:00 AM</td>
                        <td><strong>Alex Wilson</strong> - Strength & Conditioning</td>
                        <td><strong>Emily Chen</strong> - Pilates</td>
                        <td><strong>Alex Wilson</strong> - Endurance Training</td>
                        <td><strong>Emily Chen</strong> - Core & Posture</td>
                        <td><strong>Alex Wilson</strong> - Muscle Building</td>
                        <td><strong>Sarah Lee</strong> - Nutrition Coaching</td>
                    </tr>
                    <tr>
                        <td>12:00 PM</td>
                        <td><strong>Sarah Lee</strong> - Nutrition & Personal Training</td>
                        <td><strong>Alex Wilson</strong> - Strength & Conditioning</td>
                        <td><strong>Sarah Lee</strong> - Meal Planning & Fitness</td>
                        <td><strong>Alex Wilson</strong> - Strength & Conditioning</td>
                        <td><strong>Sarah Lee</strong> - Personal Training</td>
                        <td><strong>Alex Wilson</strong> - Sports Conditioning</td>
                    </tr>
                    <tr>
                        <td>2:00 PM</td>
                        <td><strong>Emily Chen</strong> - Pilates & Mobility</td>
                        <td><strong>David Laid</strong> - Interval Workouts</td>
                        <td><strong>Emily Chen</strong> - Flexibility & Core</td>
                        <td><strong>David Laid</strong> - Interval Workouts</td>
                        <td><strong>Emily Chen</strong> - Posture Improvement</td>
                        <td>Closed for Maintenance</td>
                    </tr>
                    <tr>
                        <td>4:00 PM</td>
                        <td><strong>Anna Kaiser</strong> - Advanced Yoga</td>
                        <td><strong>Sam Sulek</strong> - High-Intensity Cardio</td>
                        <td><strong>Anna Kaiser</strong> - Beginner Yoga</td>
                        <td><strong>Sam Sulek</strong> - Dynamic HIIT</td>
                        <td><strong>Anna Kaiser</strong> - Vinyasa Yoga</td>
                        <td><strong>Sam Sulek</strong> - Advanced HIIT</td>
                    </tr>
                    <tr>
                        <td>6:00 PM</td>
                        <td><strong>David Laid</strong> - Strength Training</td>
                        <td><strong>Emily Chen</strong> - Pilates & Mobility</td>
                        <td><strong>David Laid</strong> - Strength & Conditioning</td>
                        <td><strong>Emily Chen</strong> - Flexibility & Core</td>
                        <td><strong>David Laid</strong> - Interval Workouts</td>
                        <td><strong>Anna Kaiser</strong> - Meditation & Yoga</td>
                    </tr>
                    <tr>
                        <td>8:00 PM</td>
                        <td><strong>Sarah Lee</strong> - Personal Training & Nutrition</td>
                        <td><strong>Alex Wilson</strong> - Strength & Conditioning</td>
                        <td><strong>Sarah Lee</strong> - Wellness Coaching</td>
                        <td><strong>Alex Wilson</strong> - Sports Conditioning</td>
                        <td><strong>Sarah Lee</strong> - Nutrition & Meal Planning</td>
                        <td>Closed for Maintenance</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include(dirname(__FILE__) . '/../includes/staff_membership_bookings.php'); ?>

<?php include(dirname(__FILE__) . '/../includes/query_management_view.php'); ?>

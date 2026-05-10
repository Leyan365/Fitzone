<?php
$staff_feature_message = $staff_feature_message ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_membership_status'], $_POST['membership_request_id'])) {
    require_csrf();

    $request_id = (int) $_POST['membership_request_id'];
    $new_status = $_POST['membership_status'] ?? '';
    $allowed_statuses = ['approved', 'rejected', 'pending'];

    if (in_array($new_status, $allowed_statuses, true)) {
        $stmt = $conn->prepare("UPDATE membership_requests SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $request_id);

        if ($stmt->execute()) {
            $staff_feature_message = "<div class='alert alert-success'>Membership request updated.</div>";
        } else {
            error_log('Membership status update failed: ' . $stmt->error);
            $staff_feature_message = "<div class='alert alert-danger'>Failed to update membership request.</div>";
        }

        $stmt->close();
    } else {
        $staff_feature_message = "<div class='alert alert-warning'>Invalid membership status.</div>";
    }
}

$membership_requests_for_staff = [];
$stmt = $conn->prepare("
    SELECT mr.id, mr.plan_name, mr.plan_price, mr.status, mr.created_at, u.name AS customer_name, u.email
    FROM membership_requests mr
    JOIN users u ON u.id = mr.user_id
    ORDER BY FIELD(mr.status, 'pending', 'approved', 'rejected'), mr.created_at DESC
");
$stmt->execute();
$membership_requests_for_staff = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$bookings_for_staff = [];
$stmt = $conn->prepare("
    SELECT cb.id, cb.status, cb.created_at, u.name AS customer_name, s.title, s.trainer, s.session_day, s.session_time
    FROM class_bookings cb
    JOIN users u ON u.id = cb.user_id
    JOIN class_sessions s ON s.id = cb.session_id
    ORDER BY cb.created_at DESC
    LIMIT 30
");
$stmt->execute();
$bookings_for_staff = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<?php if ($staff_feature_message) echo $staff_feature_message; ?>

<div class="card text-white dashboard-card mt-4" id="memberships">
    <div class="card-header dashboard-card-header"><h4>Membership Requests</h4></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Plan</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Requested</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($membership_requests_for_staff)): ?>
                        <tr><td colspan="6" class="text-center text-white-50">No membership requests yet.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($membership_requests_for_staff as $request): ?>
                        <tr>
                            <td>
                                <?php echo e($request['customer_name']); ?><br>
                                <span class="text-white-50 small"><?php echo e($request['email']); ?></span>
                            </td>
                            <td><?php echo e($request['plan_name']); ?></td>
                            <td><?php echo e($request['plan_price']); ?></td>
                            <td><span class="badge bg-<?php echo $request['status'] === 'approved' ? 'success' : ($request['status'] === 'rejected' ? 'danger' : 'warning'); ?>"><?php echo e(ucfirst($request['status'])); ?></span></td>
                            <td><?php echo e(date("Y-m-d", strtotime($request['created_at']))); ?></td>
                            <td>
                                <form action="dashboard.php" method="post" class="d-flex gap-2">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="membership_request_id" value="<?php echo e($request['id']); ?>">
                                    <select name="membership_status" class="form-select form-select-sm" aria-label="Membership status">
                                        <option value="pending" <?php echo $request['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="approved" <?php echo $request['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                        <option value="rejected" <?php echo $request['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                    </select>
                                    <button type="submit" name="update_membership_status" class="btn btn-sm btn-primary">Save</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card text-white dashboard-card mt-4" id="bookings">
    <div class="card-header dashboard-card-header"><h4>Recent Class Bookings</h4></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-striped table-hover">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Class</th>
                        <th>Trainer</th>
                        <th>Schedule</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings_for_staff)): ?>
                        <tr><td colspan="5" class="text-center text-white-50">No class bookings yet.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($bookings_for_staff as $booking): ?>
                        <tr>
                            <td><?php echo e($booking['customer_name']); ?></td>
                            <td><?php echo e($booking['title']); ?></td>
                            <td><?php echo e($booking['trainer']); ?></td>
                            <td><?php echo e($booking['session_day'] . ' ' . $booking['session_time']); ?></td>
                            <td><span class="badge bg-<?php echo $booking['status'] === 'booked' ? 'success' : 'secondary'; ?>"><?php echo e(ucfirst($booking['status'])); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

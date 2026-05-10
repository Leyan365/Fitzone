<?php
// --- Customer-Specific Logic ---
require_role($current_user, ['customer']);

$query_message = '';
$membership_message = '';
$booking_message = '';
$plans = membership_plans();

// Retrieve Admin and Management users for the dropdown
$staff_members = [];
$staff_stmt = $conn->prepare("SELECT id, name, role FROM users WHERE role IN ('admin', 'management')");
$staff_stmt->execute();
$staff_result = $staff_stmt->get_result();
while ($row = $staff_result->fetch_assoc()) {
    $staff_members[] = $row;
}
$staff_stmt->close();

// Handle membership request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_membership'])) {
    require_csrf();

    $plan_name = trim($_POST['plan_name'] ?? '');

    if (!isset($plans[$plan_name])) {
        $membership_message = "<div class='alert alert-warning mt-3'>Please select a valid membership plan.</div>";
    } else {
        $status_stmt = $conn->prepare("SELECT id FROM membership_requests WHERE user_id = ? AND status IN ('pending', 'approved') LIMIT 1");
        $status_stmt->bind_param("i", $user_id);
        $status_stmt->execute();
        $has_active_request = $status_stmt->get_result()->num_rows > 0;
        $status_stmt->close();

        if ($has_active_request) {
            $membership_message = "<div class='alert alert-info mt-3'>You already have a pending or approved membership request.</div>";
        } else {
            $plan_price = $plans[$plan_name]['price'];
            $stmt = $conn->prepare("INSERT INTO membership_requests (user_id, plan_name, plan_price) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user_id, $plan_name, $plan_price);

            if ($stmt->execute()) {
                $membership_message = "<div class='alert alert-success mt-3'>Membership request submitted successfully.</div>";
            } else {
                error_log('Membership request failed: ' . $stmt->error);
                $membership_message = "<div class='alert alert-danger mt-3'>Failed to submit membership request.</div>";
            }

            $stmt->close();
        }
    }
}

// Handle class booking
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_class']) && is_numeric($_POST['session_id'])) {
    require_csrf();

    $session_id = (int) $_POST['session_id'];
    $stmt = $conn->prepare("
        SELECT
            s.capacity,
            COALESCE(SUM(CASE WHEN b.status = 'booked' THEN 1 ELSE 0 END), 0) AS booked_count
        FROM class_sessions s
        LEFT JOIN class_bookings b ON b.session_id = s.id
        WHERE s.id = ? AND s.is_active = 1
        GROUP BY s.id, s.capacity
    ");
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
    $session = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $existing_stmt = $conn->prepare("SELECT id FROM class_bookings WHERE user_id = ? AND session_id = ? AND status = 'booked' LIMIT 1");
    $existing_stmt->bind_param("ii", $user_id, $session_id);
    $existing_stmt->execute();
    $already_booked = $existing_stmt->get_result()->num_rows > 0;
    $existing_stmt->close();

    if (!$session) {
        $booking_message = "<div class='alert alert-warning mt-3'>Class session was not found.</div>";
    } elseif ($already_booked) {
        $booking_message = "<div class='alert alert-info mt-3'>You already booked this class.</div>";
    } elseif ((int) $session['booked_count'] >= (int) $session['capacity']) {
        $booking_message = "<div class='alert alert-warning mt-3'>This class is already full.</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO class_bookings (user_id, session_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $session_id);

        if ($stmt->execute()) {
            $booking_message = "<div class='alert alert-success mt-3'>Class booked successfully.</div>";
        } else {
            error_log('Class booking failed: ' . $stmt->error);
            $booking_message = "<div class='alert alert-danger mt-3'>Failed to book class.</div>";
        }

        $stmt->close();
    }
}

// Handle class cancellation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_booking']) && is_numeric($_POST['session_id'])) {
    require_csrf();

    $session_id = (int) $_POST['session_id'];
    $stmt = $conn->prepare("UPDATE class_bookings SET status = 'cancelled' WHERE user_id = ? AND session_id = ? AND status = 'booked'");
    $stmt->bind_param("ii", $user_id, $session_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $booking_message = "<div class='alert alert-success mt-3'>Booking cancelled.</div>";
    } else {
        $booking_message = "<div class='alert alert-warning mt-3'>No active booking was found for that class.</div>";
    }

    $stmt->close();
}

// Handle query submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_query'])) {
    require_csrf();

    $query_text = trim($_POST['query_text']);
    $recipient_id = (int)$_POST['recipient_id'];

    if (!empty($query_text) && !empty($recipient_id)) {
        $recipient_stmt = $conn->prepare("SELECT id FROM users WHERE id = ? AND role IN ('admin', 'management')");
        $recipient_stmt->bind_param("i", $recipient_id);
        $recipient_stmt->execute();
        $recipient_exists = $recipient_stmt->get_result()->num_rows > 0;
        $recipient_stmt->close();

        if (!$recipient_exists) {
            $query_message = "<div class='alert alert-warning mt-3'>Please select a valid staff member.</div>";
        } else {
            $stmt = $conn->prepare("INSERT INTO queries (customer_id, recipient_id, query_text) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $user_id, $recipient_id, $query_text);

            if ($stmt->execute()) {
                $query_message = "<div class='alert alert-success mt-3'>Your query has been submitted successfully!</div>";
            } else {
                error_log('Customer query submission failed: ' . $stmt->error);
                $query_message = "<div class='alert alert-danger mt-3'>Failed to submit your query. Please try again.</div>";
            }
            $stmt->close();
        }
    } else {
        $query_message = "<div class='alert alert-warning mt-3'>Please select a recipient and enter a query.</div>";
    }
}

// Retrieve this customer's past queries and replies
$my_queries = [];
$query_stmt = $conn->prepare("
    SELECT q.query_text, q.status, q.created_at, q.reply_text, u.name as recipient_name
    FROM queries q
    JOIN users u ON q.recipient_id = u.id
    WHERE q.customer_id = ? ORDER BY q.created_at DESC
");
$query_stmt->bind_param("i", $user_id);
$query_stmt->execute();
$my_queries = $query_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$query_stmt->close();

$membership_requests = customer_membership_requests($conn, $user_id);
$class_sessions = class_sessions_for_customer($conn, $user_id);
?>

<p class="text-white-50">This is your personal space. Track your progress, book classes, and manage your membership.</p>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card text-white dashboard-card h-100">
            <div class="card-header dashboard-card-header">Membership</div>
            <div class="card-body">
                <h2 class="card-title"><?php echo empty($membership_requests) ? 'None' : e(ucfirst($membership_requests[0]['status'])); ?></h2>
                <p class="card-text text-white-50"><?php echo empty($membership_requests) ? 'Choose a plan below.' : e($membership_requests[0]['plan_name']); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card text-white dashboard-card h-100">
            <div class="card-header dashboard-card-header">Available Classes</div>
            <div class="card-body">
                <h2 class="card-title"><?php echo count($class_sessions); ?></h2>
                <p class="card-text text-white-50">Book your next training session.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card text-white dashboard-card h-100">
            <div class="card-header dashboard-card-header">Support</div>
            <div class="card-body">
                <h2 class="card-title"><?php echo count($my_queries); ?></h2>
                <p class="card-text text-white-50">Queries and replies in your account.</p>
            </div>
        </div>
    </div>
</div>

<div class="card text-white dashboard-card mt-4">
    <div class="card-header dashboard-card-header"><h4>Membership Plans</h4></div>
    <div class="card-body">
        <?php if ($membership_message) echo $membership_message; ?>
        <div class="row g-3">
            <?php foreach ($plans as $plan_name => $plan): ?>
                <div class="col-md-6 col-xl-4">
                    <div class="border rounded p-3 h-100">
                        <h5 class="text-warning"><?php echo e($plan_name); ?></h5>
                        <p class="mb-1 fw-bold"><?php echo e($plan['price']); ?> / month</p>
                        <p class="text-white-50 small"><?php echo e($plan['summary']); ?></p>
                        <form action="dashboard.php" method="post">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="plan_name" value="<?php echo e($plan_name); ?>">
                            <button type="submit" name="request_membership" class="btn btn-sm btn-success">Request Plan</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($membership_requests)): ?>
            <hr>
            <h5>My Membership Requests</h5>
            <div class="table-responsive">
                <table class="table table-dark table-striped">
                    <thead>
                        <tr>
                            <th>Plan</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Requested</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($membership_requests as $request): ?>
                            <tr>
                                <td><?php echo e($request['plan_name']); ?></td>
                                <td><?php echo e($request['plan_price']); ?></td>
                                <td><span class="badge bg-<?php echo $request['status'] === 'approved' ? 'success' : ($request['status'] === 'rejected' ? 'danger' : 'warning'); ?>"><?php echo e(ucfirst($request['status'])); ?></span></td>
                                <td><?php echo e(date("Y-m-d", strtotime($request['created_at']))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card text-white dashboard-card mt-4">
    <div class="card-header dashboard-card-header"><h4>Book a Class</h4></div>
    <div class="card-body">
        <?php if ($booking_message) echo $booking_message; ?>
        <div class="table-responsive">
            <table class="table table-dark table-striped align-middle">
                <thead>
                    <tr>
                        <th>Class</th>
                        <th>Trainer</th>
                        <th>Day</th>
                        <th>Time</th>
                        <th>Seats</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($class_sessions as $session): ?>
                        <?php
                        $remaining = (int) $session['capacity'] - (int) $session['booked_count'];
                        $is_booked = $session['user_status'] === 'booked';
                        ?>
                        <tr>
                            <td><?php echo e($session['title']); ?></td>
                            <td><?php echo e($session['trainer']); ?></td>
                            <td><?php echo e($session['session_day']); ?></td>
                            <td><?php echo e($session['session_time']); ?></td>
                            <td><?php echo e(max(0, $remaining)); ?> / <?php echo e($session['capacity']); ?></td>
                            <td>
                                <form action="dashboard.php" method="post" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="session_id" value="<?php echo e($session['id']); ?>">
                                    <?php if ($is_booked): ?>
                                        <button type="submit" name="cancel_booking" class="btn btn-sm btn-outline-warning">Cancel</button>
                                    <?php else: ?>
                                        <button type="submit" name="book_class" class="btn btn-sm btn-success" <?php echo $remaining <= 0 ? 'disabled' : ''; ?>>Book</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card text-white dashboard-card mt-4">
    <div class="card-header dashboard-card-header"><h4>My Queries & Replies</h4></div>
    <div class="card-body">
        <?php if (empty($my_queries)): ?>
            <p class="text-white-50">You have not submitted any queries yet.</p>
        <?php else: ?>
            <div class="accordion" id="queriesAccordion">
                <?php foreach ($my_queries as $index => $query): ?>
                    <div class="accordion-item bg-dark">
                        <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                            <button class="accordion-button bg-dark text-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="false" aria-controls="collapse<?php echo $index; ?>">
                                Query to <?php echo e($query['recipient_name']); ?> on <?php echo e(date("F j, Y", strtotime($query['created_at']))); ?>
                                <span class="ms-auto badge <?php echo ($query['status'] == 'replied') ? 'bg-success' : 'bg-warning'; ?>"><?php echo e(ucfirst($query['status'])); ?></span>
                            </button>
                        </h2>
                        <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#queriesAccordion">
                            <div class="accordion-body">
                                <strong>Your Query:</strong>
                                <p class="text-white-50 fst-italic">"<?php echo e($query['query_text']); ?>"</p>
                                <hr>
                                <strong>Reply:</strong>
                                <?php if (!empty($query['reply_text'])): ?>
                                    <p class="text-white">"<?php echo e($query['reply_text']); ?>"</p>
                                <?php else: ?>
                                    <p class="text-white-50">No reply yet. A staff member will get back to you soon.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>


<div class="card text-white dashboard-card mt-4">
    <div class="card-header dashboard-card-header"><h4>Have a Question?</h4></div>
    <div class="card-body">
        <form action="dashboard.php" method="post">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
                <label for="recipient_id" class="form-label">Send query to:</label>
                <select class="form-select" name="recipient_id" id="recipient_id" required>
                    <option value="" disabled selected>-- Select a Staff Member --</option>
                    <?php foreach ($staff_members as $staff): ?>
                        <option value="<?php echo e($staff['id']); ?>">
                            <?php echo e($staff['name']) . ' (' . e($staff['role']) . ')'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="query_text" class="form-label">Your message:</label>
                <textarea class="form-control" name="query_text" rows="5" required></textarea>
            </div>
            <button type="submit" name="submit_query" class="btn btn-success">Submit Query</button>
        </form>
        <?php if ($query_message) echo $query_message; ?>
    </div>
</div>

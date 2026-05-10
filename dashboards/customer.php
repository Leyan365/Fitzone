<?php
// --- Customer-Specific Logic ---
require_role($current_user, ['customer']);

$query_message = '';

// Retrieve Admin and Management users for the dropdown
$staff_members = [];
$staff_stmt = $conn->prepare("SELECT id, name, role FROM users WHERE role IN ('admin', 'management')");
$staff_stmt->execute();
$staff_result = $staff_stmt->get_result();
while ($row = $staff_result->fetch_assoc()) {
    $staff_members[] = $row;
}
$staff_stmt->close();

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
?>

<p class="text-white-50">This is your personal space. Track your progress, book classes, and manage your membership.</p>

<div class="row">
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

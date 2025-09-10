<?php
// --- Admin-Specific Logic ---
$message = '';

// Handle new user addition from the modal
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role = $_POST['role'];

    if ($email && !empty($name) && !empty($_POST['password'])) {
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            $message = "<div class='alert alert-warning'>Email already registered.</div>";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $password, $role);
            if ($stmt->execute()) {
                $message = "<div class='alert alert-success'>User added successfully.</div>";
            } else {
                $message = "<div class='alert alert-danger'>Error adding user.</div>";
            }
            $stmt->close();
        }
        $checkStmt->close();
    } else {
        $message = "<div class='alert alert-danger'>Please fill all fields with valid data.</div>";
    }
}

// --- Shared Logic for Admin & Management ---
include(dirname(__FILE__) . '/../includes/query_management_logic.php');

// Retrieve all users
$stmt = $conn->prepare("SELECT id, name, email, role FROM users");
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<?php if ($message) echo $message; ?>
<?php if ($query_message) echo $query_message; ?>

<div class="card text-white dashboard-card mt-4">
    <div class="card-header dashboard-card-header d-flex justify-content-between align-items-center">
        <h4>User Management</h4>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal"><i class="bi bi-plus-circle-fill"></i> Add New User</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($user['role']); ?></span></td>
                        <td>
                            <a href="dashboard.php?delete_id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure? This action cannot be undone.');"><i class="bi bi-trash-fill"></i> Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include(dirname(__FILE__) . '/../includes/query_management_view.php'); ?>
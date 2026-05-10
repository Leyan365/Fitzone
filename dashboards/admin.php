<?php
require_role($current_user, ['admin']);

// --- Admin-Specific Logic ---
$message = '';
$allowed_roles = FITZONE_ROLES;

// Handle new user addition from the modal
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    require_csrf();

    $name = trim($_POST['name']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password_input = trim($_POST['password']);
    $role = $_POST['role'] ?? '';

    if ($email && !empty($name) && !empty($password_input) && is_allowed_role($role)) {
        $password = password_hash($password_input, PASSWORD_DEFAULT);
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
                error_log('Admin user add failed: ' . $stmt->error);
                $message = "<div class='alert alert-danger'>Error adding user.</div>";
            }
            $stmt->close();
        }
        $checkStmt->close();
    } else {
        $message = "<div class='alert alert-danger'>Please fill all fields with valid data.</div>";
    }
}

// Handle user deletion from a protected POST form.
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user_id']) && is_numeric($_POST['delete_user_id'])) {
    require_csrf();

    $delete_user_id = (int) $_POST['delete_user_id'];

    if ($delete_user_id === $user_id) {
        $message = "<div class='alert alert-warning'>You cannot delete your own account while logged in.</div>";
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $delete_user_id);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>User deleted successfully.</div>";
        } else {
            error_log('Admin user delete failed: ' . $stmt->error);
            $message = "<div class='alert alert-danger'>Error deleting user.</div>";
        }

        $stmt->close();
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
                        <td><?php echo e($user['id']); ?></td>
                        <td><?php echo e($user['name']); ?></td>
                        <td><?php echo e($user['email']); ?></td>
                        <td><span class="badge bg-info text-dark"><?php echo e($user['role']); ?></span></td>
                        <td>
                            <form action="dashboard.php" method="post" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="delete_user_id" value="<?php echo e($user['id']); ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure? This action cannot be undone.');"><i class="bi bi-trash-fill"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true" data-bs-theme="dark">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="dashboard.php" method="post">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" id="password" minlength="6" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" name="role" id="role" required>
                            <?php foreach ($allowed_roles as $role): ?>
                                <option value="<?php echo e($role); ?>"><?php echo e(ucfirst($role)); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_user" class="btn btn-success">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include(dirname(__FILE__) . '/../includes/query_management_view.php'); ?>

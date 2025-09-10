<?php
session_start();
include('db.php'); // Your database connection file

$login_message = ''; // To store success/error messages

if (isset($_GET['registered']) && $_GET['registered'] == 'true') {
    $login_message = "<div class='alert alert-success'>Registration successful! Please log in.</div>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $login_message = "<div class='alert alert-danger'>Both email and password are required.</div>";
    } else {
        $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name']; // Store name
                $_SESSION['user_role'] = $user['role']; // Store role in session
                session_regenerate_id(true);
                header("Location: dashboard.php");
                exit();
            } else {
                $login_message = "<div class='alert alert-danger'>Invalid password.</div>";
            }
        } else {
            $login_message = "<div class='alert alert-danger'>No user found with this email.</div>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FitZone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="custom.css">
    <style>
        /* Specific styles for the login page background and form */
        body {
            background-color: #111; /* Fallback for custom.css */
            color: white; /* Fallback for custom.css */
            min-height: 100vh; /* Ensure body takes full viewport height */
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex-grow: 1; /* Allows main content to expand */
            display: flex;
            align-items: center; /* Center form vertically */
            justify-content: center; /* Center form horizontally */
            position: relative;
            padding: 50px 0; /* Add some vertical padding */
        }
        .login-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('images/aboutuswall.webp');
            background-size: cover;
            background-position: center;
            z-index: 0; /* Send background behind content */
        }
        .login-form-card {
            background-color: rgba(255, 255, 255, 0.1); /* Slightly transparent white for glass effect */
            backdrop-filter: blur(10px); /* Glassmorphism blur */
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.18);
            max-width: 450px;
            width: 90%;
            text-align: center;
        }
        .login-form-card .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
        }
        .login-form-card .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        .login-form-card .form-control:focus {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: #ffc107; 
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        }
        .login-form-card input.form-control,
        .login-form-card textarea.form-control {
            color: black !important;
        }
        .login-form-card .btn-primary {
            background-color: #28a745; /* Green button */
            border-color: #28a745;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        .login-form-card .btn-primary:hover {
            background-color: #218838;
            border-color: #218838;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <?php include('includes/header.php'); ?>

    <main class="main-content">
        <div class="login-background"></div>
        <div class="login-form-card">
            <h2 class="mb-4" style="color: #ffc107;">Login</h2>
            <?php echo $login_message; ?>
            <form action="login.php" method="post" novalidate>
                <div class="mb-3 text-start">
                    <label for="email" class="form-label text-white-50">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email">
                    <div class="invalid-feedback">
                        Please enter a valid email address.
                    </div>
                </div>
                <div class="mb-3 text-start">
                    <label for="password" class="form-label text-white-50">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
                    <div class="invalid-feedback">
                        Please enter your password.
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-3">Login</button>
            </form>
            <p class="mt-3 text-white-50">Don't have an account? <a href="register.php" class="text-warning text-decoration-none fw-bold">Register here</a></p>
        </div>
    </main>

    <?php include('includes/footer.php');  ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Client-side form validation for login
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.login-form-card form');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);

            // Sticky navbar script from previous files
            const nav = document.querySelector('.navbar');
            if (nav) {
                window.addEventListener('scroll', () => {
                    if (window.scrollY > 50) {
                        nav.classList.add('scrolled');
                    } else {
                        nav.classList.remove('scrolled');
                    }
                });
            }
        });
    </script>
</body>
</html>
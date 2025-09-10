<?php
include('db.php'); // Your database connection file

$registration_message = ''; // To store success/error messages

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password_input = trim($_POST['password']); // Store plain password for hashing

    // Basic server-side validation
    if (empty($name) || empty($email) || empty($password_input)) {
        $registration_message = "<div class='alert alert-danger'>All fields are required.</div>";
    } elseif (!$email) {
        $registration_message = "<div class='alert alert-danger'>Invalid email format.</div>";
    } elseif (strlen($password_input) < 6) {
        $registration_message = "<div class='alert alert-danger'>Password must be at least 6 characters long.</div>";
    } else {
        $password_hashed = password_hash($password_input, PASSWORD_DEFAULT);

        // Check if the email already exists
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $registration_message = "<div class='alert alert-warning'>Email already registered. Please use a different email.</div>";
        } else {
            // Insert if email does not exist
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $password_hashed);

            if ($stmt->execute()) {
                // Redirect on success
                header("Location: login.php?registered=true");
                exit();
            } else {
                $registration_message = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
            }
            $stmt->close();
        }
        $checkStmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FitZone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="custom.css">
    <style>
        /* Specific styles for the registration page background and form */
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
        .registration-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('images/backreg.jpg');
            background-size: cover;
            background-position: center;
            z-index: 0; /* Send background behind content */
        }
        .registration-form-card {
            background-color: rgba(255, 255, 255, 0.1); /* Slightly transparent white for glass effect */
            backdrop-filter: blur(10px); /* Glassmorphism blur */
            border-radius: 15px;
            padding: 2.5rem;
            margin-top: 80px; 
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.18);
            max-width: 450px;
            width: 90%;
            text-align: center;
        }
        .registration-form-card .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #ffc107;
        }
        .registration-form-card .form-control::placeholder {
           color: black !important;
        }
        .registration-form-card .form-control:focus {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: #ffc107 !important ; 
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
        }
        .registration-form-card input.form-control,
        .registration-form-card textarea.form-control {
            color: black !important;
        }
        .registration-form-card .btn-primary {
            background-color: #28a745; /* Green button */
            border-color: #28a745;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        .registration-form-card .btn-primary:hover {
            background-color: #218838;
            border-color: #218838;
        }
        .password-strength-indicator {
            font-size: 0.85rem;
            margin-top: 5px;
            text-align: left;
            padding-left: 5px;
        }

    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <?php include('includes/header.php'); ?>

    <main class="main-content">
        <div class="registration-background"></div>
        <div class="registration-form-card">
          <h2 class="mb-4" style="color: #ffc107;">Register</h2>
            <?php echo $registration_message;?>
            <form action="register.php" method="post" id="registerForm" novalidate>
                <div class="mb-3 text-start">
                    <label for="name" class="form-label text-white-50">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required placeholder="Enter your name">
                    <div class="invalid-feedback">
                        Please enter your name.
                    </div>
                </div>
                <div class="mb-3 text-start">
                    <label for="email" class="form-label text-white-50">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email">
                    <div class="invalid-feedback">
                        Please enter a valid email address.
                    </div>
                </div>
                <div class="mb-3 text-start">
                    <label for="password" class="form-label text-white-50">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required oninput="checkPasswordStrength(this.value)" placeholder="Enter your password">
                    <div class="invalid-feedback">
                        Password must be at least 6 characters long.
                    </div>
                    <div id="password-strength" class="password-strength-indicator text-danger"></div>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-3">Register</button>
            </form>
            <p class="mt-3 text-white-50">Already have an account? <a href="login.php" class="text-warning text-decoration-none fw-bold">Login here</a></p>
        </div>
    </main>

    

    <?php include('includes/footer.php');?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Client-side form validation and password strength
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const passwordStrengthText = document.getElementById('password-strength');

            form.addEventListener('submit', function(event) {
                // Remove existing custom invalid feedback
                Array.from(form.querySelectorAll('.is-invalid')).forEach(el => el.classList.remove('is-invalid'));
                Array.from(form.querySelectorAll('.invalid-feedback-custom')).forEach(el => el.remove());

                let isValid = true;

                if (nameInput.value.trim() === '') {
                    nameInput.classList.add('is-invalid');
                    isValid = false;
                }

                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(emailInput.value.trim())) {
                    emailInput.classList.add('is-invalid');
                    isValid = false;
                }

                if (passwordInput.value.length < 6) {
                    passwordInput.classList.add('is-invalid');
                    // Add a custom message for password length
                    let customFeedback = document.createElement('div');
                    customFeedback.classList.add('invalid-feedback', 'invalid-feedback-custom', 'd-block');
                    customFeedback.textContent = 'Password must be at least 6 characters long.';
                    passwordInput.parentNode.insertBefore(customFeedback, passwordInput.nextSibling);
                    isValid = false;
                }

                if (!isValid) {
                    event.preventDefault(); // Stop form submission
                    event.stopPropagation();
                }

                form.classList.add('was-validated');
            }, false);

            window.checkPasswordStrength = function(password) {
                let strength = '';
                let color = 'text-danger';

                if (password.length === 0) {
                    strength = '';
                } else if (password.length < 6) {
                    strength = 'Weak';
                    color = 'text-danger';
                } else if (password.length >= 8 && /[A-Z]/.test(password) && /[0-9]/.test(password) && /[^A-Za-z0-9]/.test(password)) {
                    strength = 'Strong';
                    color = 'text-success';
                } else if (password.length >= 6 && (/[A-Z]/.test(password) || /[0-9]/.test(password))) {
                    strength = 'Medium';
                    color = 'text-warning';
                } else {
                    strength = 'Weak';
                    color = 'text-danger';
                }

                passwordStrengthText.textContent = strength ? 'Strength: ' + strength : '';
                passwordStrengthText.className = 'password-strength-indicator ' + color;
            };

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
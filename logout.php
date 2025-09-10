<?php
session_start();

try {
    if (!session_unset()) {
        throw new Exception("Failed to unset session variables.");
    }

    if (!session_destroy()) {
        throw new Exception("Failed to destroy the session.");
    }

    header("Location: login.php");
    exit;
} catch (Exception $e) {
    error_log($e->getMessage());
    echo "An error occurred while logging out. Please try again.";
}

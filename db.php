<?php
$servername = getenv('FITZONE_DB_HOST') ?: 'localhost';
$username = getenv('FITZONE_DB_USER') ?: 'root';
$password = getenv('FITZONE_DB_PASS') ?: '';
$dbname = getenv('FITZONE_DB_NAME') ?: 'fitzone';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    error_log('Database connection failed: ' . $conn->connect_error);
    http_response_code(500);
    exit('Unable to connect to the database.');
}

if (!$conn->set_charset('utf8mb4')) {
    error_log('Failed to set database charset: ' . $conn->error);
    http_response_code(500);
    exit('Unable to initialize the database connection.');
}

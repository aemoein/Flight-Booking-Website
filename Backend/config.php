<?php

// Enable enhanced error reporting
error_reporting(E_ALL);

define('DB_HOST', 'localhost');
define('DB_USER', 'aemeoin');
define('DB_PASSWORD', 'password123');
define('DB_NAME', 'flyease_db');

// Establish a database connection with careful error handling
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, 3306);

    // Check the connection
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    } else {
        error_log("Database connection successful", 3, 'error.log');
    }
} catch (Exception $e) {
    // Log the error message with a timestamp
    error_log(date('Y-m-d H:i:s') . ' - ' . $e->getMessage(), 3, 'error.log');
    // Redirect to the signup page for a user-friendly error message
    header("Location: signup.php?error=database");
    exit();
}

// Rest of your application code using $conn for database operations

?>
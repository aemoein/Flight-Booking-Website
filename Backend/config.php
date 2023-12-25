<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "flyease_db";
$socketPath = "/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock";

$conn = new mysqli($servername, $username, $password, $dbname, null, $socketPath);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully";
?>
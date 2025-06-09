<?php
// Database connection settings
$host =   'localhost';
$dbUser = 'root';         // Default user for XAMPP is usually 'root'
$dbPass = '';             // Default password for 'root' in XAMPP is usually empty
$dbName = 'vendora';   // Replace with your actual database name

// Create a connection
$conn = new mysqli($host, $dbUser, $dbPass, $dbName);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set charset (good practice)
$conn->set_charset("utf8mb4");
?>

<?php
// Prevent direct access
if (!defined('SECURE_ACCESS')) {
    die('Direct access not permitted');
}

// Create a connection with proper error handling
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset and collation
    if (!$conn->set_charset("utf8mb4")) {
        error_log("Error setting charset: " . $conn->error);
        throw new Exception("Error setting charset: " . $conn->error);
    }
    
    // Set strict mode
    $conn->query("SET SESSION sql_mode = 'STRICT_ALL_TABLES'");
    
    // Set timezone
    $conn->query("SET time_zone = '+00:00'");
    
} catch (Exception $e) {
    // Log the error
    error_log("Database connection error: " . $e->getMessage());
    
    // Show generic error to user
    die("A database error occurred. Please try again later.");
}

// Function to safely prepare and execute queries
function safeQuery($conn, $sql, $types = "", $params = []) {
    try {
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Query preparation failed: " . $conn->error);
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Query execution failed: " . $stmt->error);
        }
        
        return $stmt;
        
    } catch (Exception $e) {
        error_log("Query error: " . $e->getMessage());
        throw new Exception("A database error occurred. Please try again later.");
    }
}

// Function to safely fetch results
function safeFetch($stmt) {
    try {
        $result = $stmt->get_result();
        $data = [];
        
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        return $data;
        
    } catch (Exception $e) {
        error_log("Fetch error: " . $e->getMessage());
        throw new Exception("A database error occurred. Please try again later.");
    }
}
?>

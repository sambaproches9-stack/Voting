<?php
// Database connection details
$host = 'localhost';    // Your database host
$db = 'voting';         // Your database name (voting)
$user = 'root';         // Your MySQL username
$pass = '';             // Your MySQL password (set it accordingly)

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

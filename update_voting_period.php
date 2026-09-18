<?php
session_start();
if (!isset($_SESSION['admission_number']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include('connect.php');

// Get and validate inputs
$voting_start = $_POST['voting_start'] ?? '';
$voting_end = $_POST['voting_end'] ?? '';

if (!$voting_start || !$voting_end) {
    die("Start and end times are required.");
}

if (strtotime($voting_start) === false || strtotime($voting_end) === false) {
    die("Invalid date format.");
}

if ($voting_start >= $voting_end) {
    die("Voting end time must be after start time.");
}

// Check if the settings row exists
$check_sql = "SELECT id FROM settings WHERE id = 1";
$result = mysqli_query($conn, $check_sql);

if ($result && mysqli_num_rows($result) > 0) {
    // Update existing
    $sql = "UPDATE settings SET voting_start = ?, voting_end = ? WHERE id = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $voting_start, $voting_end);
} else {
    // Insert new
    $sql = "INSERT INTO settings (id, voting_start, voting_end) VALUES (1, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $voting_start, $voting_end);
}

if ($stmt->execute()) {
    header("Location: admin_dashboard.php?success=1");
    exit;
} else {
    echo "Error updating voting period: " . $stmt->error;
}
?>

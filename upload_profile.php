<?php
session_start();
include('connect.php');

if (!isset($_SESSION['admission_number'])) {
    header("Location: login.php");
    exit;
}

$admission_number = $_SESSION['admission_number'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];
    $targetDir = "uploads/";
    $fileName = basename($file['name']);
    $targetFile = $targetDir . uniqid() . "_" . $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($imageFileType, $allowed)) {
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            // Update user's profile picture in the database
            $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE admission_number = ?");
            $stmt->bind_param("ss", $targetFile, $admission_number);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Profile picture updated successfully.";
            } else {
                $_SESSION['error'] = "Database update failed.";
            }
        } else {
            $_SESSION['error'] = "Failed to upload image.";
        }
    } else {
        $_SESSION['error'] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
    }
}

header("Location: user_dashboard.php");
exit;

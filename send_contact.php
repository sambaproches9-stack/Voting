<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $subject === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Please complete all fields with a valid email address.'); window.location.href='contact.php';</script>";
    exit;
}

$safe_email = str_replace(["\r", "\n"], '', $email);
$safe_subject = str_replace(["\r", "\n"], '', $subject);
$headers = "From: {$safe_email}\r\nReply-To: {$safe_email}";
$full_message = "Name: {$name}\nEmail: {$safe_email}\nSubject: {$safe_subject}\n\nMessage:\n{$message}";

if (mail('support@iaa.ac.tz', $safe_subject, $full_message, $headers)) {
    echo "<script>alert('Message sent successfully.'); window.location.href='contact.php';</script>";
} else {
    echo "<script>alert('Failed to send message. Please try again later.'); window.location.href='contact.php';</script>";
}
?>

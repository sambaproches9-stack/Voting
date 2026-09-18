<?php
session_start();
if (!isset($_SESSION['admission_number']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank You</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #a9a9a9;
        }
        .thank-you-container {
            max-width: 500px;
            margin: auto;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .thank-you-container h2 {
            font-weight: bold;
            color:#003366;
        }
        .thank-you-container p {
            font-size: 1.1rem;
            color: #444;
        }
        .btn-submit {
            background-color: #003366;
            color: white;
            width: 100%;
            margin-top: 10px;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">

    <div class="thank-you-container text-center">
        <img src="images/logo.png" alt="Thank You" class="mb-4" style="width: 80px;">
        <h2>Thank You for Voting!</h2>
        <p>Your votes have been successfully recorded.</p>
        <p>Stay tuned for the results.</p>
        <a href="results.php" class="btn btn-submit mt-3">View Results</a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

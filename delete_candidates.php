<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['admission_number']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include('connect.php');

$message = "";
$message_type = "danger";

if (isset($_GET['id'])) {
    $candidate_id = (int) $_GET['id'];

    $delete_stmt = $conn->prepare("DELETE FROM candidates WHERE id = ?");
    $delete_stmt->bind_param("i", $candidate_id);

    if ($delete_stmt->execute()) {
        $message = "Candidate deleted successfully!";
        $message_type = "success";
    } else {
        $message = "Error: " . $delete_stmt->error;
    }
    $delete_stmt->close();
} else {
    $message = "No candidate selected!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Candidate - IAA ONLINE VOTING SYSTEM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #071c35;
            --blue: #0d4677;
            --red: #b52e38;
            --ink: #142235;
            --muted: #6c7b8c;
            --line: #e2e8ef;
            --canvas: #f4f7fa;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 24px;
            background: var(--canvas);
            color: var(--ink);
            font-family: 'DM Sans', sans-serif;
        }

        .result-card {
            width: min(100%, 520px);
            padding: clamp(24px, 5vw, 40px);
            border: 1px solid var(--line);
            border-radius: 12px;
            background: white;
            box-shadow: 0 12px 28px rgba(23, 44, 68, .07);
            text-align: center;
        }

        .result-icon {
            display: grid;
            width: 58px;
            height: 58px;
            margin: 0 auto 18px;
            place-items: center;
            border-radius: 50%;
            color: <?= $message_type === 'success' ? '#27804b' : '#b52e38' ?>;
            background: <?= $message_type === 'success' ? '#edf8f0' : '#fff1f2' ?>;
            font-size: 1.7rem;
        }

        .result-card h1 {
            margin: 0 0 10px;
            color: var(--navy);
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.5rem;
        }

        .result-card p {
            margin: 0 0 24px;
            color: var(--muted);
            font-size: .92rem;
        }

        .btn-submit {
            min-height: 46px;
            border: 0;
            border-radius: 7px;
            background: var(--blue);
            color: white;
            font-weight: 700;
        }

        .btn-submit:hover, .btn-submit:focus {
            background: #08385f;
            color: white;
        }
    </style>
</head>
<body>

<main class="result-card">
    <div class="result-icon"><i class="bi <?= $message_type === 'success' ? 'bi-check-lg' : 'bi-exclamation-lg' ?>"></i></div>
    <h1><?= $message_type === 'success' ? 'Candidate removed' : 'Unable to remove candidate' ?></h1>
    <p><?= htmlspecialchars($message) ?></p>
    <a href="admin_dashboard.php?page=view_candidates" class="btn btn-submit px-4"><i class="bi bi-arrow-left me-2"></i>Back to candidates</a>
</main>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>

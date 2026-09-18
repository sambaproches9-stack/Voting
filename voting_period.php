<?php
if (!isset($_SESSION['admission_number']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}
include('connect.php');

// Set timezone
date_default_timezone_set("Africa/Dar_es_Salaam");

// Update voting period
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $start = $_POST['voting_start'];
    $end = $_POST['voting_end'];

    $stmt = $conn->prepare("UPDATE settings SET voting_start = ?, voting_end = ? WHERE id = 1");
    $stmt->bind_param("ss", $start, $end);

    if ($stmt->execute()) {
        $message = "Voting period updated successfully!";
    } else {
        $message = "Failed to update voting period.";
    }
}

// Fetch current voting period
$voting_start = "";
$voting_end = "";
$result = $conn->query("SELECT voting_start, voting_end FROM settings WHERE id = 1");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $voting_start = $row['voting_start'];
    $voting_end = $row['voting_end'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Voting Period | IAA Digital Voting System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        body {
            background: var(--canvas);
            color: var(--ink);
            font-family: 'DM Sans', sans-serif;
        }

        .period-page {
            width: min(100%, 980px);
            margin: 0 auto;
            padding: 12px 0 35px;
        }

        .page-heading {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 24px;
        }

        .eyebrow {
            margin: 0 0 8px;
            color: var(--red);
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .page-heading h1 {
            margin: 0 0 6px;
            color: var(--navy);
            font-family: 'Space Grotesk', sans-serif;
            font-size: 2rem;
        }

        .page-heading p {
            margin: 0;
            color: var(--muted);
            font-size: .9rem;
        }

        .page-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 12px;
            border: 1px solid var(--line);
            border-radius: 7px;
            color: var(--blue);
            background: white;
            font-size: .78rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .form-container {
            background: white;
            padding: clamp(22px, 4vw, 38px);
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: 0 12px 28px rgba(23, 44, 68, .05);
            max-width: 720px;
            margin: 0 auto;
        }

        .section-label {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 22px;
            color: var(--navy);
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.08rem;
        }

        .section-label i {
            color: var(--red);
        }

        .form-label {
            margin-bottom: 8px;
            color: #344255;
            font-size: .82rem;
            font-weight: 700;
        }

        .form-control {
            min-height: 49px;
            border: 1px solid var(--line);
            border-radius: 7px;
            color: var(--ink);
            font-size: .9rem;
        }

        .form-control:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(13, 70, 119, .12);
        }

        .alert {
            border-radius: 8px;
            font-size: .86rem;
        }

        .btn-submit {
            min-height: 50px;
            width: 100%;
            border: 0;
            border-radius: 7px;
            background: var(--red);
            color: white;
            font-weight: 700;
            transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
        }

        .btn-submit:hover, .btn-submit:focus {
            background: #982630;
            color: white;
            box-shadow: 0 8px 18px rgba(181, 46, 56, .2);
            transform: translateY(-1px);
        }

        @media (max-width: 700px) {
            .period-page {
                padding-top: 0;
            }

            .page-heading {
                align-items: flex-start;
                flex-direction: column;
            }

            .page-heading h1 {
                font-size: 1.7rem;
            }

            .page-badge {
                display: none;
            }
        }
    </style>
</head>
<body>
    <main class="period-page">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Election settings</p>
                <h1>Voting period</h1>
                <p>Set the start and end date for the election cycle.</p>
            </div>
            <span class="page-badge"><i class="bi bi-shield-check"></i> Admin only</span>
        </div>

        <div class="form-container">
            <h2 class="section-label"><i class="bi bi-calendar-range"></i> Voting schedule</h2>

            <?php if ($message): ?>
                <div class="alert alert-info" role="alert"><?= $message ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-4">
                    <label class="form-label">Voting start time</label>
                    <input type="datetime-local" name="voting_start" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($voting_start)) ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Voting end time</label>
                    <input type="datetime-local" name="voting_end" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($voting_end)) ?>" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-submit"><i class="bi bi-calendar-check me-2"></i>Save voting period</button>
                </div>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include('footer.php'); ?>
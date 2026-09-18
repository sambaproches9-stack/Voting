<?php
include('connect.php');

if (!isset($_SESSION['admission_number']) || $_SESSION['role'] != 'admin') {      
    header("Location: login.php");
    exit;
}

// Fetch all voters
$voters = $conn->query("SELECT * FROM users"); // or adjust role as needed
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Voter List | IAA Digital Voting System</title>
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

        .voters-page {
            width: min(100%, 1180px);
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

        .voter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 22px;
        }

        .voter-card {
            border: 1px solid var(--line);
            border-radius: 14px;
            background: white;
            box-shadow: 0 12px 28px rgba(23, 44, 68, .05);
            padding: 22px 20px 18px;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .voter-avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(13, 70, 119, .12), rgba(181, 46, 56, .12));
            color: var(--navy);
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 14px;
            font-family: 'Space Grotesk', sans-serif;
            overflow: hidden;
            border: 2px solid var(--line);
        }

        .voter-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .voter-card h5 {
            margin: 0 0 16px;
            color: var(--navy);
            font-size: 1.2rem;
            font-weight: 700;
            font-family: 'Space Grotesk', sans-serif;
            width: 100%;
        }

        .voter-meta {
            display: grid;
            gap: 8px;
            margin-bottom: 18px;
            color: var(--ink);
            font-size: .9rem;
            width: 100%;
            text-align: left;
        }

        .voter-meta p {
            margin: 0;
            color: #47576d;
        }

        .meta-label {
            font-weight: 700;
            color: var(--navy);
        }

        .card-actions {
            margin-top: auto;
            display: flex;
            justify-content: center;
            gap: 10px;
            width: 100%;
        }

        .btn-sm {
            font-size: .82rem;
            min-height: 38px;
            padding: .58rem .9rem;
            border-radius: 7px;
            font-weight: 600;
        }

        .btn-warning {
            background: #f3b73c;
            border-color: #f3b73c;
            color: #1f1f1f;
        }

        .btn-warning:hover {
            background: #e0a72d;
            border-color: #e0a72d;
            color: #1f1f1f;
        }

        .btn-danger {
            background: var(--red);
            border-color: var(--red);
        }

        .btn-danger:hover {
            background: #982630;
            border-color: #982630;
        }

        @media (max-width: 700px) {
            .voters-page {
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
    <main class="voters-page">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Voter management</p>
                <h1>Registered voters</h1>
                <p>Review and manage all currently registered participants.</p>
            </div>
            <span class="page-badge"><i class="bi bi-shield-check"></i> Admin view</span>
        </div>

        <div class="voter-grid">
            <?php while ($row = $voters->fetch_assoc()): ?>
                <?php
                    $profileImage = !empty($row['profile_picture']) ? htmlspecialchars($row['profile_picture']) : 'images/default.jpg';
                    $initial = strtoupper(substr(trim($row['name']), 0, 1));
                ?>
                <div class="voter-card">
                    <div class="voter-avatar">
                        <?php if (!empty($row['profile_picture'])): ?>
                            <img src="<?= $profileImage ?>" alt="<?= htmlspecialchars($row['name']) ?> profile picture">
                        <?php else: ?>
                            <?= $initial ?>
                        <?php endif; ?>
                    </div>
                    <h5><?= htmlspecialchars($row['name']) ?></h5>
                    <div class="voter-meta">
                        <p><span class="meta-label">Admission Number:</span> <?= htmlspecialchars($row['admission_number']) ?></p>
                        <p><span class="meta-label">Role:</span> <?= htmlspecialchars($row['role']) ?></p>
                    </div>
                    <div class="card-actions">
                        <a href="admin_dashboard.php?page=update_user&id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete_user.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this voter?')" class="btn btn-sm btn-danger">Delete</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

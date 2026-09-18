<?php
include('connect.php');

// Ensure only admin can access
if (!isset($_SESSION['admission_number']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Fetch messages
$sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Messages | IAA Digital Voting System</title>
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

        .messages-page {
            width: min(100%, 1200px);
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

        .table-wrapper {
            background: #fff;
            padding: 24px;
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: 0 12px 28px rgba(23, 44, 68, .05);
        }

        .table thead th {
            background: var(--navy);
            color: white;
            border: none;
            font-size: .8rem;
            letter-spacing: .04em;
            text-transform: uppercase;
            padding: 14px 16px;
        }

        .table tbody td {
            padding: 14px 16px;
            vertical-align: top;
            border-color: var(--line);
            color: #2b3a4b;
        }

        .table tbody tr:nth-child(even) {
            background: #fafcff;
        }

        .alert {
            border-radius: 8px;
            font-size: .9rem;
        }

        @media (max-width: 700px) {
            .messages-page {
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

            .table-wrapper {
                padding: 12px;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <main class="messages-page">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Support inbox</p>
                <h1>Contact messages</h1>
                <p>Review all user messages sent through the contact form.</p>
            </div>
            <span class="page-badge"><i class="bi bi-shield-check"></i> Admin inbox</span>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <div class="table-wrapper">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Message</th>
                                <th>Sent At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $count = 1; while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $count++ ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                                    <td><?= $row['created_at'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">No messages found.</div>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include('footer.php'); ?>

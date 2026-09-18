<?php

include('connect.php');

// Allow only logged-in admins or students
if (!isset($_SESSION['admission_number']) || !in_array($_SESSION['role'], ['admin', 'student'])) {
    echo "<p class='text-danger'>Access denied.</p>";
    exit;
}

// Fetch presidential candidates
$presidents = $conn->query("SELECT * FROM candidates WHERE election_type = 'president'");

// Count votes for each presidential candidate
$president_votes = [];
while ($row = $presidents->fetch_assoc()) {
    $candidate_id = $row['id'];
    $vote_count = $conn->query("SELECT COUNT(*) AS total_votes FROM votes WHERE candidate_id = $candidate_id AND election_type = 'president'")
                      ->fetch_assoc()['total_votes'];
    $president_votes[$candidate_id] = $vote_count;
}

// Reset pointer to loop again
$presidents->data_seek(0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Election Results | IAA Digital Voting System</title>
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

        .results-page {
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

        .section-title {
            margin: 28px 0 18px;
            color: var(--navy);
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.2rem;
        }

        .candidate-card {
            border: 1px solid var(--line);
            border-radius: 14px;
            background: white;
            box-shadow: 0 12px 28px rgba(23, 44, 68, .05);
            padding: 22px 18px 18px;
            height: 100%;
            text-align: center;
        }

        .candidate-pair {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .candidate-slot {
            flex: 1 1 160px;
            max-width: 180px;
        }

        .candidate-card img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 14px;
            margin-bottom: 10px;
            border: 1px solid var(--line);
            background: #f8fafc;
        }

        .candidate-card h5 {
            margin: 0;
            color: var(--navy);
            font-size: 1.05rem;
            font-weight: 700;
            font-family: 'Space Grotesk', sans-serif;
        }

        .candidate-card p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: .85rem;
        }

        .votes {
            margin-top: 18px;
            padding-top: 14px;
            border-top: 1px solid var(--line);
            font-size: 1.6rem;
            color: var(--red);
            font-weight: 700;
        }

        @media (max-width: 700px) {
            .results-page {
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
    <main class="results-page">
        <div class="page-heading">
            <div>
                <p class="eyebrow">Election overview</p>
                <h1>Election results</h1>
                <p>Live vote totals for the presidential election.</p>
            </div>
            <span class="page-badge"><i class="bi bi-shield-check"></i> Admin view</span>
        </div>

        <div class="mb-5">
            <h3 class="section-title">Presidential election results</h3>
            <div class="row g-4">
                <?php while ($row = $presidents->fetch_assoc()): 
                    $candidate_id = $row['id'];
                    $vote_count = $president_votes[$candidate_id] ?? 0;
                ?>
                <div class="col-md-6">
                    <div class="candidate-card">
                        <div class="candidate-pair">
                            <div class="candidate-slot">
                                <img src="<?= htmlspecialchars($row['president_image']) ?>" alt="President">
                                <h5><?= htmlspecialchars($row['president_name']) ?></h5>
                                <p><strong>President</strong></p>
                            </div>
                            <div class="candidate-slot">
                                <img src="<?= htmlspecialchars($row['vice_president_image']) ?>" alt="Vice President">
                                <h5><?= htmlspecialchars($row['vice_president_name']) ?></h5>
                                <p><strong>Vice President</strong></p>
                            </div>
                        </div>
                        <p class="votes"><?= $vote_count ?> Votes</p>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

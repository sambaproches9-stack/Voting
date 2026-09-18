<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include('connect.php');

if (!isset($_SESSION['admission_number']) || !in_array($_SESSION['role'], ['admin', 'student'], true)) {
    header('Location: login.php');
    exit;
}

$presidents = $conn->query("SELECT * FROM candidates WHERE election_type = 'president'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Candidates | IAA Digital Voting System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
        body { margin: 0; background: var(--canvas); color: var(--ink); font-family: 'DM Sans', sans-serif; }
        .candidates-page { width: min(100%, 1080px); margin: 0 auto; padding: 34px 18px 48px; }
        .page-heading { display: flex; align-items: end; justify-content: space-between; gap: 20px; margin-bottom: 32px; }
        .eyebrow { margin: 0 0 8px; color: var(--red); font-size: .72rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; }
        .page-heading h1 { margin: 0 0 7px; color: var(--navy); font-family: 'Space Grotesk', sans-serif; font-size: clamp(1.75rem, 4vw, 2.25rem); }
        .page-heading p { margin: 0; color: var(--muted); font-size: .92rem; }
        .candidate-count { color: var(--muted); font-size: .8rem; white-space: nowrap; }
        .section-heading { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
        .section-heading h2 { margin: 0; color: var(--navy); font-family: 'Space Grotesk', sans-serif; font-size: 1.15rem; }
        .section-heading i { color: var(--red); font-size: 1.15rem; }
        .candidate-card { margin-bottom: 22px; overflow: hidden; border: 1px solid var(--line); border-radius: 11px; background: #fff; box-shadow: 0 10px 24px rgba(23, 44, 68, .05); }
        .team-header { display: flex; align-items: center; justify-content: space-between; padding: 18px 22px; border-bottom: 1px solid var(--line); }
        .team-label { margin: 0; color: var(--muted); font-size: .72rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
        .team-number { color: var(--blue); font-size: .8rem; font-weight: 700; }
        .candidate-pair { display: grid; grid-template-columns: 1fr 1fr; }
        .candidate-person { display: flex; align-items: center; gap: 18px; padding: 24px 22px; }
        .candidate-person + .candidate-person { border-left: 1px solid var(--line); }
        .candidate-person img { width: 92px; height: 92px; flex: 0 0 auto; object-fit: cover; border: 3px solid #edf3f8; border-radius: 50%; }
        .candidate-person h3 { margin: 0 0 7px; color: var(--navy); font-family: 'Space Grotesk', sans-serif; font-size: 1.05rem; }
        .candidate-person p { margin: 0; color: var(--muted); font-size: .8rem; font-weight: 600; }
        .candidate-actions { display: flex; justify-content: flex-end; gap: 8px; padding: 14px 22px; border-top: 1px solid var(--line); background: #fbfcfd; }
        .candidate-actions .btn { border-radius: 6px; font-size: .78rem; font-weight: 700; }
        .btn-view { border-color: #bfd1df; color: var(--blue); background: white; }
        .btn-view:hover { border-color: var(--blue); color: white; background: var(--blue); }
        .btn-edit { border-color: #e6c980; color: #876000; background: #fffaf0; }
        .btn-delete { border-color: #edb8bc; color: #a62a34; background: #fff5f5; }
        .empty-state { padding: 52px 24px; border: 1px dashed #b9c8d6; border-radius: 10px; color: var(--muted); background: white; text-align: center; }
        .empty-state i { display: block; margin-bottom: 12px; color: #9bb0c2; font-size: 2rem; }
        .empty-state p { margin: 0; }
        .modal-content { border: 0; border-radius: 12px; overflow: hidden; }
        .modal-header { border-bottom-color: var(--line); }
        .modal-title { color: var(--navy); font-family: 'Space Grotesk', sans-serif; }
        .modal-body { padding: 24px; }
        .modal-person { display: grid; grid-template-columns: 110px 1fr; align-items: center; gap: 18px; padding: 16px 0; }
        .modal-person + .modal-person { border-top: 1px solid var(--line); }
        .modal-person img { width: 110px; height: 110px; object-fit: cover; border-radius: 8px; }
        .modal-person p { margin: 0 0 5px; color: var(--muted); font-size: .76rem; font-weight: 700; text-transform: uppercase; }
        .modal-person h3 { margin: 0; color: var(--navy); font-family: 'Space Grotesk', sans-serif; font-size: 1.05rem; }

        @media (max-width: 700px) {
            .candidates-page { padding-top: 24px; }
            .page-heading { align-items: flex-start; flex-direction: column; margin-bottom: 24px; }
            .candidate-count { display: none; }
            .candidate-pair { display: block; }
            .candidate-person + .candidate-person { border-top: 1px solid var(--line); border-left: 0; }
        }

        @media (max-width: 430px) {
            .candidate-person { align-items: flex-start; flex-direction: column; gap: 12px; }
            .candidate-actions { justify-content: stretch; flex-wrap: wrap; }
            .candidate-actions .btn { flex: 1 1 auto; }
            .modal-person { grid-template-columns: 80px 1fr; }
            .modal-person img { width: 80px; height: 80px; }
        }
    </style>
</head>
<body>
<main class="candidates-page">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Candidate management</p>
            <h1>Candidate overview</h1>
            <p>Review the presidential teams registered for this election.</p>
        </div>
        <span class="candidate-count"><i class="bi bi-people me-1"></i><?= $presidents->num_rows ?> team<?= $presidents->num_rows === 1 ? '' : 's' ?> registered</span>
    </div>

    <section>
        <div class="section-heading">
            <i class="bi bi-award-fill"></i>
            <h2>Presidential candidates</h2>
        </div>
        <div class="row g-4">
            <?php if ($presidents->num_rows === 0): ?>
                <div class="col-12">
                    <div class="empty-state"><i class="bi bi-person-x"></i><p>No presidential candidates have been registered yet.</p></div>
                </div>
            <?php endif; ?>

            <?php $team_number = 1; while ($row = $presidents->fetch_assoc()): ?>
                <div class="col-12">
                    <div class="candidate-card">
                        <div class="team-header"><p class="team-label">Presidential team</p><span class="team-number">Team <?= $team_number++ ?></span></div>
                        <div class="candidate-pair">
                            <div class="candidate-person">
                                <img src="<?= htmlspecialchars($row['president_image']) ?>" alt="Portrait of <?= htmlspecialchars($row['president_name']) ?>">
                                <div><h3><?= htmlspecialchars($row['president_name']) ?></h3><p>President</p></div>
                            </div>
                            <div class="candidate-person">
                                <img src="<?= htmlspecialchars($row['vice_president_image']) ?>" alt="Portrait of <?= htmlspecialchars($row['vice_president_name']) ?>">
                                <div><h3><?= htmlspecialchars($row['vice_president_name']) ?></h3><p>Vice President</p></div>
                            </div>
                        </div>
                        <div class="candidate-actions">
                            <button class="btn btn-sm btn-view" data-bs-toggle="modal" data-bs-target="#viewModal<?= (int) $row['id'] ?>"><i class="bi bi-eye me-1"></i>View</button>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <a href="admin_dashboard.php?page=update_candidates&id=<?= (int) $row['id'] ?>" class="btn btn-sm btn-edit"><i class="bi bi-pencil me-1"></i>Edit</a>
                                <a href="delete_candidates.php?id=<?= (int) $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this candidate?')" class="btn btn-sm btn-delete"><i class="bi bi-trash3 me-1"></i>Delete</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="viewModal<?= (int) $row['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="bi bi-award me-2"></i>Candidate details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="modal-person">
                                    <img src="<?= htmlspecialchars($row['president_image']) ?>" alt="Portrait of <?= htmlspecialchars($row['president_name']) ?>">
                                    <div><p>President</p><h3><?= htmlspecialchars($row['president_name']) ?></h3></div>
                                </div>
                                <div class="modal-person">
                                    <img src="<?= htmlspecialchars($row['vice_president_image']) ?>" alt="Portrait of <?= htmlspecialchars($row['vice_president_name']) ?>">
                                    <div><p>Vice President</p><h3><?= htmlspecialchars($row['vice_president_name']) ?></h3></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

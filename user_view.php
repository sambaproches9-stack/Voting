<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include('connect.php');

date_default_timezone_set('Africa/Dar_es_Salaam');

if (!isset($_SESSION['admission_number']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$voting_start = $voting_end = '';
$settings_result = $conn->query("SELECT voting_start, voting_end FROM settings WHERE id = 1");
if ($settings_row = $settings_result->fetch_assoc()) {
    $voting_start = $settings_row['voting_start'];
    $voting_end = $settings_row['voting_end'];

    $now = date("Y-m-d H:i:s");

    if ($now < $voting_start) {
        exit("<div class='alert alert-warning text-center'>Voting has not started yet.</div>");
    } elseif ($now > $voting_end) {
        exit("<div class='alert alert-danger text-center'>Voting has ended.</div>");
    }
} else {
    exit("<div class='alert alert-danger text-center'>Voting settings are not configured.</div>");
}

$presidents = $conn->query("SELECT * FROM candidates WHERE election_type = 'president'");
$president_voted = $conn->query("SELECT * FROM votes WHERE user_id = '$user_id' AND election_type = 'president'")->num_rows > 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['candidate_id']) && !$president_voted) {
        $candidate_id = $_POST['candidate_id'];
        $election_type = 'president';

        $conn->query("INSERT INTO votes (user_id, candidate_id, election_type) VALUES ('$user_id', $candidate_id, '$election_type')");
        $_SESSION['voted'] = true;
        $president_voted = true;
    }
}

?>

<style>
    .voting-page {
        width: min(100%, 1080px);
        margin: 0 auto;
    }

    .voting-heading {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 28px;
    }

    .eyebrow {
        margin: 0 0 8px;
        color: var(--red);
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    .voting-heading h1 {
        margin: 0 0 6px;
        color: var(--navy);
        font-family: 'Space Grotesk', sans-serif;
        font-size: clamp(1.65rem, 3vw, 2.15rem);
    }

    .voting-heading p {
        margin: 0;
        color: var(--muted);
        font-size: .92rem;
    }

    .voting-badge {
        padding: 9px 12px;
        border: 1px solid var(--line);
        border-radius: 7px;
        color: var(--blue);
        background: white;
        font-size: .78rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .notice-card {
        padding: 18px 20px;
        border: 1px solid #b9d8c2;
        border-left: 4px solid #27804b;
        border-radius: 9px;
        color: #215c35;
        background: #f5fbf6;
    }

    .notice-card i { margin-right: 8px; }

    .section-heading {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0 0 16px;
        color: var(--navy);
        font-family: 'Space Grotesk', sans-serif;
        font-size: 1.1rem;
    }

    .section-heading i { color: var(--red); }

    .candidate-card {
        overflow: hidden;
        border: 1px solid var(--line);
        border-radius: 11px;
        background: white;
        box-shadow: 0 10px 24px rgba(23, 44, 68, .05);
        transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
    }

    .candidate-card:has(input[type="radio"]:checked) {
        border-color: var(--red);
        box-shadow: 0 12px 28px rgba(181, 46, 56, .14);
        transform: translateY(-2px);
    }

    .team-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid var(--line);
    }

    .team-label {
        margin: 0;
        color: var(--muted);
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .candidate-pair {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    .candidate-person {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 22px 20px;
    }

    .candidate-person + .candidate-person { border-left: 1px solid var(--line); }

    .candidate-person img {
        width: 88px;
        height: 88px;
        flex: 0 0 auto;
        object-fit: cover;
        border: 3px solid #edf3f8;
        border-radius: 50%;
    }

    .candidate-person h3 {
        margin: 0 0 6px;
        color: var(--navy);
        font-family: 'Space Grotesk', sans-serif;
        font-size: 1rem;
    }

    .candidate-person p {
        margin: 0;
        color: var(--muted);
        font-size: .78rem;
        font-weight: 600;
    }

    .selection-area {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 14px 20px;
        border-top: 1px solid var(--line);
        background: #fbfcfd;
        color: var(--muted);
        font-size: .8rem;
        font-weight: 700;
    }

    input[type="radio"] {
        position: absolute;
        opacity: 0;
    }

    .custom-radio {
        display: inline-grid;
        width: 22px;
        height: 22px;
        place-items: center;
        position: relative;
        border: 2px solid #9db0c0;
        border-radius: 50%;
        cursor: pointer;
    }

    input[type="radio"]:checked + .custom-radio {
        border-color: var(--red);
        background: var(--red);
        box-shadow: 0 0 0 3px rgba(181, 46, 56, .14);
    }

    input[type="radio"]:checked + .custom-radio::after {
        content: '\2713';
        color: #27804b;
        font-size: 16px;
        font-weight: 900;
        line-height: 1;
    }

    .submit-btn {
        min-height: 48px;
        margin-top: 24px;
        padding: 0 26px;
        border: 0;
        border-radius: 7px;
        background: var(--red);
        color: white;
        font-weight: 700;
        transition: background .2s ease, box-shadow .2s ease, transform .2s ease;
    }

    .submit-btn:hover, .submit-btn:focus {
        background: #982630;
        color: white;
        box-shadow: 0 8px 18px rgba(181, 46, 56, .2);
        transform: translateY(-1px);
    }

    .empty-state {
        padding: 44px 20px;
        border: 1px dashed #b9c8d6;
        border-radius: 10px;
        color: var(--muted);
        background: white;
        text-align: center;
    }

    @media (max-width: 700px) {
        .voting-heading { align-items: flex-start; flex-direction: column; }
        .voting-badge { display: none; }
        .candidate-pair { display: block; }
        .candidate-person + .candidate-person { border-top: 1px solid var(--line); border-left: 0; }
    }

    @media (max-width: 430px) {
        .candidate-person { align-items: flex-start; flex-direction: column; }
    }
</style>

<main class="voting-page">
    <div class="voting-heading">
        <div>
            <p class="eyebrow">Election voting</p>
            <h1>Cast your vote</h1>
            <p>Select one presidential team carefully. Your vote can only be submitted once.</p>
        </div>
        <span class="voting-badge"><i class="bi bi-shield-check me-1"></i>Secure ballot</span>
    </div>

    <?php if ($president_voted): ?>
        <div class="notice-card">
            <i class="bi bi-check-circle-fill"></i>Your vote has been successfully submitted. Thank you for participating.
        </div>
    <?php else: ?>
        <div class="section-heading"><i class="bi bi-award-fill"></i>Presidential candidates</div>

        <form method="POST">
            <div class="row g-4">
                <?php while ($row = $presidents->fetch_assoc()): ?>
                    <div class="col-md-6">
                        <div class="candidate-card">
                            <div class="team-header"><p class="team-label">Presidential team</p><span class="text-muted small">Select one</span></div>
                            <div class="candidate-pair">
                                <div>
                                    <div class="candidate-person">
                                        <img src="<?= htmlspecialchars($row['president_image']) ?>" alt="Portrait of <?= htmlspecialchars($row['president_name']) ?>">
                                        <div><h3><?= htmlspecialchars($row['president_name']) ?></h3><p>President</p></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="candidate-person">
                                        <img src="<?= htmlspecialchars($row['vice_president_image']) ?>" alt="Portrait of <?= htmlspecialchars($row['vice_president_name']) ?>">
                                        <div><h3><?= htmlspecialchars($row['vice_president_name']) ?></h3><p>Vice President</p></div>
                                    </div>
                                </div>
                            </div>

                            <div class="selection-area">
                                <label>
                                    <input type="radio" name="candidate_id" value="<?= $row['id'] ?>" required>
                                    <span class="custom-radio"></span>
                                </label>
                                <span>Select this team</span>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="text-center">
                <input type="hidden" name="election_type" value="president">
                <button type="submit" class="submit-btn"><i class="bi bi-check2-circle me-2"></i>Submit vote</button>
            </div>
        </form>
    <?php endif; ?>
</main>

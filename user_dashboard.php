<?php
session_start();

if (!isset($_SESSION['admission_number']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit;
}

include('connect.php');

$admission_number = $_SESSION['admission_number'];
$query = "SELECT id, name, profile_picture FROM users WHERE admission_number = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $admission_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_id = $row['id'];
    $user_name = $row['name'];
    $profile_picture = !empty($row['profile_picture']) ? $row['profile_picture'] : 'images/default.jpg';
} else {
    $user_id = 0;
    $user_name = 'Guest';
    $profile_picture = 'images/default.jpg';
}
$stmt->close();

$page = $_GET['page'] ?? 'home';
$allowed_pages = [
    'home' => 'Dashboard',
    'user_view' => 'user_view.php',
    'results' => 'results.php',
    'change_password' => 'change_password.php',
    'change_profile_picture' => 'change_profile_picture.php',
];

// Check if user has voted
$voted_stmt = $conn->prepare("SELECT id FROM votes WHERE user_id = ?");
$voted_stmt->bind_param("i", $user_id);
$voted_stmt->execute();
$voted_result = $voted_stmt->get_result();
$has_voted = $voted_result->num_rows > 0;
$voted_stmt->close();

// Voting period
$voting_start = $voting_end = "";
$start_str = $end_str = "Not configured";
$status = "Voting status unknown";

$settings_result = $conn->query("SELECT voting_start, voting_end FROM settings WHERE id = 1");
if ($settings_row = $settings_result->fetch_assoc()) {
    $voting_start = $settings_row['voting_start'];
    $voting_end = $settings_row['voting_end'];
    $start_str = date("d M Y, H:i A", strtotime($voting_start));
    $end_str = date("d M Y, H:i A", strtotime($voting_end));

    $now = date("Y-m-d H:i:s");
    if ($now < $voting_start) {
        $status = "Voting not started yet";
    } elseif ($now >= $voting_start && $now <= $voting_end) {
        $status = "Voting is currently open";
    } else {
        $status = "Voting has ended";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
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
            margin: 0;
            color: var(--ink);
            font-family: 'DM Sans', sans-serif;
            display: flex;
            background: var(--canvas);
        }

        .sidebar {
            width: 264px;
            background: var(--navy);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            transition: transform .3s ease;
            z-index: 1000;
            box-shadow: 10px 0 30px rgba(7, 28, 53, .08);
        }

        .sidebar.hidden { transform: translateX(-100%); }

        .profile-section {
            text-align: center;
            padding: 30px 20px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, .1);
        }

        .profile-section img {
            width: 76px;
            height: 76px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 14px;
            border: 3px solid rgba(255, 255, 255, .8);
        }

        .profile-section p { margin: 0; font-size: .88rem; font-weight: 600; }
        .profile-role { display: block; margin-top: 5px; color: #9db3c8; font-size: .72rem; letter-spacing: .08em; text-transform: uppercase; }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 13px;
            margin: 3px 12px;
            padding: 12px 14px;
            border-radius: 7px;
            color: #b8c9d9;
            font-size: .88rem;
            text-decoration: none;
            transition: background .2s ease, color .2s ease;
        }

        .sidebar a i { width: 18px; color: #8fa7bd; font-size: 1rem; text-align: center; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255, 255, 255, .1); color: white; }
        .sidebar a.active { box-shadow: inset 3px 0 0 #ed7474; }
        .sidebar a:hover i, .sidebar a.active i { color: #ed7474; }
        .sidebar .logout-link { margin-top: 18px; color: #f0a4a4; }
        .sidebar-divider { margin: 20px 24px 12px; color: #718aa2; font-size: .68rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; }

        .main-content {
            margin-left: 264px;
            flex-grow: 1;
            background: var(--canvas);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left .3s ease;
        }

        .main-content.full { margin-left: 0; }

        .header {
            display: flex;
            align-items: center;
            min-height: 78px;
            background: white;
            padding: 12px 30px;
            border-bottom: 1px solid var(--line);
            color: var(--navy);
            justify-content: space-between;
        }

        .brand-lockup { display: flex; align-items: center; gap: 12px; }
        .header img { width: 42px; height: 42px; object-fit: contain; }
        .header .title { font-family: 'Space Grotesk', sans-serif; font-size: 1.15rem; font-weight: 700; }
        .header .context { display: block; margin-top: 2px; color: var(--muted); font-size: .72rem; font-weight: 400; }
        .hamburger { display: grid; width: 40px; height: 40px; place-items: center; border: 1px solid var(--line); border-radius: 7px; color: var(--blue); background: white; cursor: pointer; font-size: 1.2rem; }

        #content-area { width: min(100%, 1320px); padding: 34px 30px 48px; }
        .countdown { color: var(--red); font-weight: 700; }
        .dashboard-intro { display: flex; align-items: end; justify-content: space-between; gap: 20px; margin-bottom: 28px; }
        .dashboard-intro h1 { margin: 0 0 6px; color: var(--navy); font-family: 'Space Grotesk', sans-serif; font-size: clamp(1.65rem, 3vw, 2.15rem); }
        .dashboard-intro p { margin: 0; color: var(--muted); font-size: .92rem; }
        .date-stamp { color: var(--muted); font-size: .78rem; white-space: nowrap; }

        .period-card { padding: 20px 22px; border: 1px solid var(--line); border-left: 4px solid var(--blue); border-radius: 10px; background: white; box-shadow: 0 8px 20px rgba(23, 44, 68, .04); }
        .period-card strong { color: var(--navy); }
        .period-card .status { color: var(--blue); font-weight: 700; }
        .stat-card { position: relative; min-height: 148px; overflow: hidden; border: 1px solid var(--line); border-radius: 10px; background: white; box-shadow: 0 8px 20px rgba(23, 44, 68, .04); }
        .stat-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--accent); }
        .stat-card-body { display: flex; align-items: flex-start; justify-content: space-between; padding: 23px 22px; }
        .stat-label { margin: 0 0 12px; color: var(--muted); font-size: .78rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
        .stat-value { margin: 0; color: var(--navy); font-family: 'Space Grotesk', sans-serif; font-size: 2.15rem; line-height: 1; }
        .stat-icon { display: grid; width: 42px; height: 42px; place-items: center; border-radius: 8px; color: var(--accent); background: #edf3f8; font-size: 1.2rem; }
        .btn-results { border: 0; border-radius: 7px; background: var(--red); color: white; font-weight: 700; }
        .btn-results:hover, .btn-results:focus { background: #982630; color: white; }

        @media (max-width: 768px) {
            .sidebar { position: absolute; }
            .main-content { margin-left: 0; }
            .header { padding: 12px 18px; }
            #content-area { padding: 28px 18px 40px; }
            .dashboard-intro { align-items: flex-start; flex-direction: column; margin-bottom: 22px; }
            .date-stamp { display: none; }
        }
    </style>
</head>
<body>

<div id="sidebar" class="sidebar">
    <div class="profile-section">
        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
        <p>Welcome, <?php echo htmlspecialchars($user_name); ?></p>
        <span class="profile-role">Registered voter</span>
    </div>
    <div class="sidebar-divider">Voting</div>
    <a href="?page=home" class="<?= $page === 'home' ? 'active' : '' ?>"><i class="bi bi-grid"></i> Dashboard</a>
    <a href="?page=user_view" class="<?= $page === 'user_view' ? 'active' : '' ?>"><i class="bi bi-people-fill"></i> View Candidates</a>
    <a href="?page=results" class="<?= $page === 'results' ? 'active' : '' ?>"><i class="bi bi-bar-chart-fill"></i> View Results</a>
    <div class="sidebar-divider">Account</div>
    <a href="?page=change_password" class="<?= $page === 'change_password' ? 'active' : '' ?>"><i class="bi bi-key-fill"></i> Change Password</a>
    <a href="?page=change_profile_picture" class="<?= $page === 'change_profile_picture' ? 'active' : '' ?>"><i class="bi bi-image-fill"></i> Edit Photo</a>
    <a href="logout.php" class="logout-link"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<div id="main-content" class="main-content">
    <div class="header">
        <div class="brand-lockup">
            <img src="images/logo.png" alt="Logo">
            <div>
                <span class="title">Online Voting</span>
                <span class="context">Voter portal</span>
            </div>
        </div>
        <button type="button" class="hamburger" onclick="toggleSidebar()" aria-label="Toggle navigation menu"><i class="bi bi-list"></i></button>
    </div>

    <div id="content-area">
        <?php
        if ($page === 'home') {
            if (isset($_SESSION['show_thank_you']) && $_SESSION['show_thank_you'] === true) {
                include 'thank_you.php';
                unset($_SESSION['show_thank_you']);
            }

            $totalVoters = $conn->query("SELECT COUNT(*) AS cnt FROM users WHERE role = 'student'")->fetch_assoc()['cnt'];
            $totalCandidates = $conn->query("SELECT COUNT(*) AS cnt FROM candidates")->fetch_assoc()['cnt'];
            $totalVotes = $conn->query("SELECT COUNT(*) AS cnt FROM votes")->fetch_assoc()['cnt'];
            ?>
            <div class="dashboard-intro">
                <div>
                    <h1>Good day, <?= htmlspecialchars(explode(' ', trim($user_name))[0]) ?>.</h1>
                    <p>Stay informed and cast your vote when the election opens.</p>
                </div>
                <span class="date-stamp"><i class="bi bi-calendar3 me-1"></i><?= date('F j, Y') ?></span>
            </div>

            <div class="period-card mb-4">
                <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                    <div>
                        <strong><i class="bi bi-calendar-check me-2"></i>Voting period</strong>
                        <div class="text-muted small mt-2">Starts: <?= $start_str ?> &nbsp;|&nbsp; Ends: <?= $end_str ?></div>
                    </div>
                    <span class="status"><?= htmlspecialchars($status) ?></span>
                </div>
                <?php if ($status === "Voting is currently open"): ?>
                    <div class="small text-muted mt-3">Time left: <span id="countdown" class="countdown"></span></div>
                <?php endif; ?>
            </div>

            <?php if ($has_voted): ?>
                <a href="?page=results" class="btn btn-results mb-4 px-4"><i class="bi bi-bar-chart-fill me-2"></i>View results</a>
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-sm-6 col-xl-4">
                    <div class="stat-card" style="--accent: #0d4677;">
                        <div class="stat-card-body"><div><p class="stat-label">Total voters</p><p class="stat-value"><?= $totalVoters ?></p></div><span class="stat-icon"><i class="bi bi-person-check-fill"></i></span></div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="stat-card" style="--accent: #27804b;">
                        <div class="stat-card-body"><div><p class="stat-label">Candidates</p><p class="stat-value"><?= $totalCandidates ?></p></div><span class="stat-icon"><i class="bi bi-people-fill"></i></span></div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="stat-card" style="--accent: #b52e38;">
                        <div class="stat-card-body"><div><p class="stat-label">Votes cast</p><p class="stat-value"><?= $totalVotes ?></p></div><span class="stat-icon"><i class="bi bi-bar-chart-fill"></i></span></div>
                    </div>
                </div>
            </div>
            <?php
        } elseif (array_key_exists($page, $allowed_pages)) {
            include $allowed_pages[$page];
        } else {
            echo "<p class='text-danger'>Invalid page requested.</p>";
        }
        ?>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const main = document.getElementById('main-content');
        sidebar.classList.toggle('hidden');
        main.classList.toggle('full');
    }

    <?php if ($status === "Voting is currently open"): ?>
    const countdown = document.getElementById('countdown');
    const votingEnd = new Date("<?= $voting_end ?>").getTime();

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = votingEnd - now;

        if (distance <= 0) {
            countdown.innerText = "Voting has ended";
            clearInterval(interval);
            return;
        }

        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        countdown.innerText = `${hours}h ${minutes}m ${seconds}s`;
    }

    const interval = setInterval(updateCountdown, 1000);
    updateCountdown();
    <?php endif; ?>
</script>
</body>
</html>

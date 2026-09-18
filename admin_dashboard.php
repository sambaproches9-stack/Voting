<?php
session_start();
if (!isset($_SESSION['admission_number']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include('connect.php');

$admission_number = $_SESSION['admission_number'];
$stmt = $conn->prepare("SELECT name, profile_picture FROM users WHERE admission_number = ?");
$stmt->bind_param("s", $admission_number);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $username = $row['name'];
    $profile_picture = !empty($row['profile_picture']) ? $row['profile_picture'] : 'images/default.jpg';
} else {
    $username = 'Admin';
    $profile_picture = 'images/default.jpg';
}
$stmt->close();

$page = $_GET['page'] ?? 'home';
$allowed_pages = [
    'home' => 'Dashboard',
    'add_candidate' => 'add_candidate.php',
    'view_candidates' => 'view_candidates.php',
    'add_voters' => 'add_voters.php',
    'view_voters' => 'view_voters.php',
    'update_user' => 'update_user.php',
    'update_candidates' => 'update_candidates.php',
    'results' => 'results.php',
    'view_messages' => 'view_messages.php',
    'voting_period' => 'voting_period.php',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
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

        body {
            margin: 0;
            color: var(--ink);
            font-family: 'DM Sans', sans-serif;
            display: flex;
            background: var(--canvas);
            overflow-x: hidden;
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
            transition: transform 0.3s ease;
            z-index: 1000;
            box-shadow: 10px 0 30px rgba(7, 28, 53, .08);
        }

        .sidebar.hidden {
            transform: translateX(-100%);
        }

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

        .sidebar-divider {
            margin: 20px 24px 12px;
            color: #718aa2;
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .main-content {
            margin-left: 264px;
            flex-grow: 1;
            background: var(--canvas);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
        }

        .main-content.full {
            margin-left: 0;
        }

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
        .hamburger {
            display: grid;
            width: 40px;
            height: 40px;
            place-items: center;
            border: 1px solid var(--line);
            border-radius: 7px;
            color: var(--blue);
            background: white;
            cursor: pointer;
            font-size: 1.2rem;
        }

        #content-area {
            width: min(100%, 1320px);
            padding: 34px 30px 48px;
            overflow-x: hidden;
        }

        .dashboard-intro { display: flex; align-items: end; justify-content: space-between; gap: 20px; margin-bottom: 28px; }
        .dashboard-intro h1 { margin: 0 0 6px; color: var(--navy); font-family: 'Space Grotesk', sans-serif; font-size: clamp(1.65rem, 3vw, 2.15rem); }
        .dashboard-intro p { margin: 0; color: var(--muted); font-size: .92rem; }
        .date-stamp { color: var(--muted); font-size: .78rem; white-space: nowrap; }

        .stat-card {
            position: relative;
            min-height: 148px;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: white;
            box-shadow: 0 8px 20px rgba(23, 44, 68, .04);
        }

        .stat-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--accent); }
        .stat-card-body { display: flex; align-items: flex-start; justify-content: space-between; padding: 23px 22px; }
        .stat-label { margin: 0 0 12px; color: var(--muted); font-size: .78rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
        .stat-value { margin: 0; color: var(--navy); font-family: 'Space Grotesk', sans-serif; font-size: 2.15rem; line-height: 1; }
        .stat-icon { display: grid; width: 42px; height: 42px; place-items: center; border-radius: 8px; color: var(--accent); background: color-mix(in srgb, var(--accent) 12%, white); font-size: 1.2rem; }

        .section-heading { display: flex; align-items: center; justify-content: space-between; margin: 38px 0 16px; }
        .section-heading h2 { margin: 0; color: var(--navy); font-family: 'Space Grotesk', sans-serif; font-size: 1.1rem; }
        .section-heading span { color: var(--muted); font-size: .78rem; }
        .quick-action { display: flex; align-items: center; gap: 14px; height: 100%; padding: 18px; border: 1px solid var(--line); border-radius: 9px; background: white; color: var(--ink); text-decoration: none; transition: border-color .2s ease, transform .2s ease, box-shadow .2s ease; }
        .quick-action:hover { border-color: #aac2d7; color: var(--ink); box-shadow: 0 10px 24px rgba(23, 44, 68, .08); transform: translateY(-2px); }
        .quick-action i { display: grid; width: 38px; height: 38px; place-items: center; border-radius: 8px; color: var(--blue); background: #edf3f8; font-size: 1.05rem; }
        .quick-action strong { display: block; font-size: .88rem; }
        .quick-action small { display: block; margin-top: 3px; color: var(--muted); font-size: .76rem; }

        hr { border-color: rgba(255, 255, 255, .1); margin: 0; }

        @supports not (background: color-mix(in srgb, red 10%, white)) {
            .stat-icon { background: #edf3f8; }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 224px;
                position: absolute;
                transform: translateX(-100%);
                transition: transform .3s ease;
                box-shadow: 12px 0 28px rgba(7, 28, 53, .2);
            }

            .sidebar:not(.hidden) {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .header {
                min-height: 68px;
                padding: 10px 16px;
            }

            .brand-lockup { min-width: 0; gap: 9px; }
            .header img { width: 36px; height: 36px; }
            .header .title { overflow: hidden; font-size: 1rem; text-overflow: ellipsis; white-space: nowrap; }
            .header .context { font-size: .66rem; }
            .hamburger { width: 38px; height: 38px; flex: 0 0 auto; }

            .profile-section { padding: 22px 14px 18px; }
            .profile-section img { width: 58px; height: 58px; margin-bottom: 10px; }
            .profile-section p { font-size: .8rem; }
            .profile-role { font-size: .64rem; }
            .sidebar-divider { margin: 14px 18px 7px; font-size: .62rem; }
            .sidebar a { gap: 10px; margin: 2px 8px; min-height: 38px; padding: 8px 11px; font-size: .8rem; }
            .sidebar a i { width: 17px; font-size: .95rem; }

            #content-area { width: 100%; padding: 24px 16px 36px; }
            .dashboard-intro { align-items: flex-start; flex-direction: column; margin-bottom: 22px; }
            .date-stamp { display: none; }

            .dashboard-intro h1 { font-size: 1.65rem; }
            .dashboard-intro p { font-size: .86rem; }
            .section-heading { align-items: flex-start; flex-direction: column; gap: 5px; margin-top: 30px; }
            .stat-card { min-height: 124px; }
            .stat-card-body { padding: 18px 17px; }
            .stat-value { font-size: 1.85rem; }
            .quick-action { min-height: 74px; padding: 14px; }
        }

        @media (max-width: 420px) {
            .header .context { display: none; }
            .header .title { max-width: 180px; }
            .stat-card-body { align-items: center; }
            .stat-label { margin-bottom: 8px; font-size: .7rem; }
            .stat-icon { width: 36px; height: 36px; font-size: 1rem; }
        }
    </style>
</head>
<body>
    <div id="sidebar" class="sidebar">
        <div class="profile-section">
            <img src="<?= htmlspecialchars($profile_picture) ?>" alt="Profile Picture">
            <p>Welcome, <?= htmlspecialchars($username) ?></p>
            <span class="profile-role">System administrator</span>
        </div>
        <div class="sidebar-divider">Management</div>
        <a href="?page=home" class="<?= $page === 'home' ? 'active' : '' ?>"><i class="bi bi-grid"></i> Dashboard</a>
        <a href="?page=add_candidate" class="<?= $page === 'add_candidate' ? 'active' : '' ?>"><i class="bi bi-person-plus-fill"></i> Add Candidates</a>
        <a href="?page=view_candidates" class="<?= $page === 'view_candidates' ? 'active' : '' ?>"><i class="bi bi-people-fill"></i> View Candidates</a>
        <a href="?page=add_voters" class="<?= $page === 'add_voters' ? 'active' : '' ?>"><i class="bi bi-person-add"></i> Add Voters</a>
        <a href="?page=view_voters" class="<?= $page === 'view_voters' ? 'active' : '' ?>"><i class="bi bi-person-lines-fill"></i> View Voters</a>
        <div class="sidebar-divider">Monitoring</div>
        <a href="?page=results" class="<?= $page === 'results' ? 'active' : '' ?>"><i class="bi bi-bar-chart-fill"></i> View Results</a>
        <a href="?page=view_messages" class="<?= $page === 'view_messages' ? 'active' : '' ?>"><i class="bi bi-envelope-fill"></i> View Messages</a>
        <a href="?page=voting_period" class="<?= $page === 'voting_period' ? 'active' : '' ?>"><i class="bi bi-calendar-check-fill"></i> Voting Period</a>
        <a href="logout.php" class="logout-link"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>

    <div id="main-content" class="main-content">
        <div class="header">
            <div class="brand-lockup">
                <img src="images/logo.png" alt="Logo">
                <div>
                    <span class="title">Online Voting</span>
                    <span class="context">Administration portal</span>
                </div>
            </div>
            <button type="button" class="hamburger" onclick="toggleSidebar()" aria-label="Toggle navigation menu"><i class="bi bi-list"></i></button>
        </div>
        <div id="content-area">
            <?php
            if ($page === 'home') {
                $totalVoters = $conn->query("SELECT COUNT(*) AS cnt FROM users WHERE role = 'student'")->fetch_assoc()['cnt'];
                $totalCandidates = $conn->query("SELECT COUNT(*) AS cnt FROM candidates")->fetch_assoc()['cnt'];
                $totalMessages = $conn->query("SELECT COUNT(*) AS cnt FROM contact_messages")->fetch_assoc()['cnt'];
                $totalVotes = $conn->query("SELECT COUNT(*) AS cnt FROM votes")->fetch_assoc()['cnt'];
                ?>
                <div class="dashboard-intro">
                    <div>
                        <h1>Good day, <?= htmlspecialchars(explode(' ', trim($username))[0]) ?>.</h1>
                        <p>Here is the latest overview of your voting system.</p>
                    </div>
                    <span class="date-stamp"><i class="bi bi-calendar3 me-1"></i><?= date('F j, Y') ?></span>
                </div>

                <div class="row g-3">
                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-card" style="--accent: #0d4677;">
                            <div class="stat-card-body"><div><p class="stat-label">Total voters</p><p class="stat-value"><?= $totalVoters ?></p></div><span class="stat-icon"><i class="bi bi-person-check-fill"></i></span></div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-card" style="--accent: #27804b;">
                            <div class="stat-card-body"><div><p class="stat-label">Candidates</p><p class="stat-value"><?= $totalCandidates ?></p></div><span class="stat-icon"><i class="bi bi-people-fill"></i></span></div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-card" style="--accent: #a66a00;">
                            <div class="stat-card-body"><div><p class="stat-label">Messages</p><p class="stat-value"><?= $totalMessages ?></p></div><span class="stat-icon"><i class="bi bi-chat-left-text-fill"></i></span></div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="stat-card" style="--accent: #b52e38;">
                            <div class="stat-card-body"><div><p class="stat-label">Votes cast</p><p class="stat-value"><?= $totalVotes ?></p></div><span class="stat-icon"><i class="bi bi-bar-chart-fill"></i></span></div>
                        </div>
                    </div>
                </div>

                <div class="section-heading">
                    <h2>Quick actions</h2>
                    <span>Common administration tasks</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="?page=add_voters" class="quick-action"><i class="bi bi-person-plus-fill"></i><span><strong>Add a voter</strong><small>Create a new voter account</small></span></a>
                    </div>
                    <div class="col-md-4">
                        <a href="?page=add_candidate" class="quick-action"><i class="bi bi-person-badge-fill"></i><span><strong>Add a candidate</strong><small>Register election candidates</small></span></a>
                    </div>
                    <div class="col-md-4">
                        <a href="?page=view_messages" class="quick-action"><i class="bi bi-envelope-open-fill"></i><span><strong>Review messages</strong><small>Read voter communication</small></span></a>
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
    </script>
</body>
</html>

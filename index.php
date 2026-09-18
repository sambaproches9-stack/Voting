<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$logged_in = isset($_SESSION['admission_number']);
$dashboard_url = ($_SESSION['role'] ?? '') === 'admin' ? 'admin_dashboard.php' : 'user_dashboard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IAA Digital Voting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --navy: #071c35; --blue: #0d4677; --red: #b52e38; --ink: #142235; --muted: #6c7b8c; --line: #e2e8ef; --canvas: #f4f7fa; }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--canvas); color: var(--ink); font-family: 'DM Sans', sans-serif; }
        .site-header { display: flex; align-items: center; justify-content: space-between; min-height: 88px; padding: 14px clamp(20px, 5vw, 70px); border-bottom: 1px solid rgba(255,255,255,.12); background: var(--navy); }
        .brand-lockup { display: flex; align-items: center; gap: 12px; color: white; }
        .brand-lockup img { width: 58px; height: 58px; padding: 5px; object-fit: contain; border: 1px solid rgba(255,255,255,.35); border-radius: 8px; background: white; }
        .brand-lockup strong { display: block; font-family: 'Space Grotesk', sans-serif; font-size: 1.05rem; }
        .brand-lockup span { display: block; margin-top: 2px; color: #9db3c8; font-size: .7rem; }
        .site-nav { display: flex; align-items: center; gap: 8px; }
        .site-nav a { padding: 9px 13px; border-radius: 7px; color: #c8d5e0; font-size: .82rem; font-weight: 600; text-decoration: none; }
        .site-nav a:hover { color: white; background: rgba(255,255,255,.1); }
        .site-nav .nav-action { color: white; background: var(--red); }
        .site-nav .nav-action:hover { background: #982630; }
        .hero { position: relative; overflow: hidden; padding: clamp(58px, 9vw, 104px) clamp(20px, 5vw, 70px) 74px; background: var(--navy); color: white; }
        .hero::after { content: ''; position: absolute; right: -120px; bottom: -180px; width: 480px; height: 480px; border: 1px solid rgba(255,255,255,.1); border-radius: 50%; box-shadow: 0 0 0 35px rgba(255,255,255,.025), 0 0 0 70px rgba(255,255,255,.02); }
        .hero-content { position: relative; z-index: 1; width: min(100%, 1180px); margin: 0 auto; }
        .hero-grid { display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(280px, .9fr); align-items: center; gap: clamp(36px, 7vw, 90px); }
        .eyebrow { margin: 0 0 14px; color: #ed7474; font-size: .74rem; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; }
        .hero h1 { max-width: 700px; margin: 0; font-family: 'Space Grotesk', sans-serif; font-size: clamp(2.35rem, 6vw, 4.7rem); line-height: 1.02; letter-spacing: -.03em; }
        .hero-copy { max-width: 600px; margin: 20px 0 28px; color: #c8d5e0; font-size: 1.05rem; line-height: 1.7; }
        .hero-actions { display: flex; flex-wrap: wrap; gap: 10px; }
        .hero-actions a { display: inline-flex; align-items: center; min-height: 46px; padding: 0 18px; border-radius: 7px; font-size: .86rem; font-weight: 700; text-decoration: none; }
        .primary-action { color: white; background: var(--red); }
        .primary-action:hover { color: white; background: #982630; }
        .secondary-action { border: 1px solid rgba(255,255,255,.25); color: white; background: transparent; }
        .secondary-action:hover { color: white; background: rgba(255,255,255,.1); }
        .hero-art { display: flex; justify-content: center; }
        .hero-art img { width: min(100%, 360px); aspect-ratio: 1 / 1; object-fit: contain; filter: drop-shadow(0 20px 30px rgba(0,0,0,.25)); }
        .content { width: min(100%, 1180px); margin: 0 auto; padding: 42px 20px 58px; }
        .trust-strip { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-top: -8px; }
        .trust-item { display: flex; align-items: center; gap: 12px; min-height: 88px; padding: 18px; border: 1px solid var(--line); border-radius: 9px; background: white; box-shadow: 0 8px 20px rgba(23,44,68,.04); }
        .trust-item i { display: grid; width: 38px; height: 38px; flex: 0 0 auto; place-items: center; border-radius: 8px; color: var(--blue); background: #edf3f8; font-size: 1.05rem; }
        .trust-item strong { display: block; color: var(--navy); font-size: .86rem; }
        .trust-item span { display: block; margin-top: 3px; color: var(--muted); font-size: .75rem; }
        .section-heading { display: flex; align-items: end; justify-content: space-between; gap: 20px; margin: 50px 0 16px; }
        .section-heading h2 { margin: 0; color: var(--navy); font-family: 'Space Grotesk', sans-serif; font-size: 1.35rem; }
        .section-heading p { margin: 0; color: var(--muted); font-size: .8rem; }
        .info-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
        .info-card { padding: 22px; border: 1px solid var(--line); border-radius: 9px; background: white; }
        .info-card i { color: var(--red); font-size: 1.35rem; }
        .info-card h3 { margin: 15px 0 7px; color: var(--navy); font-family: 'Space Grotesk', sans-serif; font-size: 1rem; }
        .info-card p { margin: 0; color: var(--muted); font-size: .82rem; line-height: 1.6; }
        .site-footer { padding: 26px 20px; background: var(--navy); color: #b8c9d9; text-align: center; }
        .site-footer p { margin: 0; font-size: .78rem; }
        .site-footer p + p { margin-top: 8px; }
        .site-footer a { color: #f0a4a4; font-weight: 600; text-decoration: none; }
        .site-footer a:hover { color: white; text-decoration: underline; }
        @media (max-width: 760px) {
            .site-header { align-items: flex-start; flex-direction: column; gap: 14px; }
            .site-nav { width: 100%; flex-wrap: wrap; }
            .hero-grid, .info-grid { grid-template-columns: 1fr; }
            .hero-art { order: -1; justify-content: flex-start; }
            .hero-art img { width: min(100%, 220px); }
            .trust-strip { grid-template-columns: 1fr; }
            .section-heading { align-items: flex-start; flex-direction: column; }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <a href="index.php" class="brand-lockup text-decoration-none">
            <img src="images/IAA.png" alt="IAA Logo">
            <div><strong>IAA Online Voting</strong><span>Institute of Accountancy Arusha</span></div>
        </a>
        <nav class="site-nav" aria-label="Main navigation">
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
            <a href="<?= $logged_in ? $dashboard_url : 'login.php' ?>" class="nav-action"><i class="bi <?= $logged_in ? 'bi-grid' : 'bi-box-arrow-in-right' ?> me-1"></i><?= $logged_in ? 'Dashboard' : 'Sign in' ?></a>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="hero-content hero-grid">
                <div>
                    <p class="eyebrow">Student leadership starts here</p>
                    <h1>Make your voice count.</h1>
                    <p class="hero-copy">A secure digital voting experience for fair, transparent, and accessible elections at the Institute of Accountancy Arusha.</p>
                    <div class="hero-actions">
                        <a href="<?= $logged_in ? $dashboard_url . '?page=user_view' : 'login.php' ?>" class="primary-action"><i class="bi bi-check2-circle me-2"></i>Vote now</a>
                        <a href="about.php" class="secondary-action"><i class="bi bi-arrow-right me-2"></i>Learn about the system</a>
                    </div>
                </div>
                <div class="hero-art"><img src="images/voting logo.png" alt="Digital voting illustration"></div>
            </div>
        </section>

        <section class="content">
            <div class="trust-strip">
                <div class="trust-item"><i class="bi bi-shield-lock-fill"></i><div><strong>Secure access</strong><span>Protected voter accounts</span></div></div>
                <div class="trust-item"><i class="bi bi-eye-fill"></i><div><strong>Transparent process</strong><span>Clear election information</span></div></div>
                <div class="trust-item"><i class="bi bi-people-fill"></i><div><strong>Student voice</strong><span>Every eligible voter matters</span></div></div>
            </div>

            <div class="section-heading"><h2>Everything you need to participate</h2><p>Simple by design, dependable by purpose.</p></div>
            <div class="info-grid">
                <article class="info-card"><i class="bi bi-person-check-fill"></i><h3>Vote with confidence</h3><p>Review candidate information and submit your choice through a guided, easy-to-use ballot.</p></article>
                <article class="info-card"><i class="bi bi-clock-history"></i><h3>Know the schedule</h3><p>Stay informed about voting periods and access election results from your dashboard.</p></article>
                <article class="info-card"><i class="bi bi-headset"></i><h3>Get support</h3><p>Have a question or account issue? Contact the support team for assistance.</p></article>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <p>&copy; <?= date('Y') ?> IAA Online Voting System. All rights reserved.</p>
        <p><a href="contact.php">Contact us</a><span aria-hidden="true"> &nbsp;|&nbsp; </span><a href="about.php">About the system</a></p>
    </footer>
</body>
</html>

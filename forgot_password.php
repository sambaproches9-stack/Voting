<?php
include('connect.php');

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admission_number = trim($_POST['admission_number'] ?? '');

    if ($admission_number === '') {
        $error_message = 'Please enter your admission number.';
    } else {
        // Keep the response generic so account existence is not disclosed.
        $stmt = $conn->prepare('SELECT id FROM users WHERE admission_number = ? LIMIT 1');
        $stmt->bind_param('s', $admission_number);
        $stmt->execute();
        $stmt->get_result();
        $stmt->close();

        $success_message = 'Your request has been received. Please contact the system administrator for a password reset.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password recovery | IAA Digital Voting System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #071c35;
            --blue: #0d4677;
            --red: #b52e38;
            --ink: #142235;
            --muted: #667589;
            --line: #dce4ec;
            --surface: #ffffff;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 24px;
            color: var(--ink);
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(135deg, rgba(7, 28, 53, .98), rgba(13, 70, 119, .92)), #071c35;
        }

        .recovery-container {
            width: min(100%, 940px);
            display: grid;
            grid-template-columns: minmax(260px, .9fr) minmax(360px, 1.1fr);
            overflow: hidden;
            background: var(--surface);
            border: 1px solid rgba(255, 255, 255, .22);
            border-radius: 18px;
            box-shadow: 0 26px 70px rgba(0, 0, 0, .28);
        }

        .brand-panel {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 560px;
            padding: 44px 38px;
            overflow: hidden;
            color: white;
            background: var(--navy);
        }

        .brand-panel::after {
            content: '';
            position: absolute;
            right: -100px;
            bottom: -130px;
            width: 300px;
            height: 300px;
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 50%;
            box-shadow: 0 0 0 28px rgba(255, 255, 255, .035), 0 0 0 56px rgba(255, 255, 255, .025);
        }

        .brand-mark, .eyebrow, .brand-panel h1, .brand-copy, .brand-footer { position: relative; z-index: 1; }
        .brand-mark { width: 104px; height: 104px; object-fit: contain; margin-bottom: 30px; }
        .eyebrow { margin: 0 0 14px; color: #ed7474; font-size: .72rem; font-weight: 700; letter-spacing: .16em; text-transform: uppercase; }
        .brand-panel h1 { max-width: 280px; margin: 0; font-family: 'Space Grotesk', sans-serif; font-size: clamp(2rem, 4vw, 3rem); line-height: 1.04; }
        .brand-copy { max-width: 290px; margin: 18px 0 0; color: #b5c6d7; font-size: .94rem; line-height: 1.65; }
        .brand-footer { margin: 0; color: #8fa5ba; font-size: .78rem; }

        .form-panel { padding: 60px clamp(28px, 6vw, 76px); align-self: center; }
        .back-link { display: inline-block; margin-bottom: 30px; color: var(--blue); font-size: .84rem; font-weight: 700; text-decoration: none; }
        .back-link:hover { color: var(--red); text-decoration: underline; }
        .form-heading { margin-bottom: 28px; }
        .form-heading h2 { margin: 0 0 8px; color: var(--navy); font-family: 'Space Grotesk', sans-serif; font-size: 1.85rem; }
        .form-heading p { margin: 0; color: var(--muted); font-size: .92rem; line-height: 1.6; }
        .form-label { margin-bottom: 8px; color: #344255; font-size: .82rem; font-weight: 700; }
        .form-control { width: 100%; min-height: 50px; border: 1px solid var(--line); border-radius: 8px; padding: 0 14px; color: var(--ink); font: inherit; font-size: .94rem; }
        .form-control:focus { border-color: var(--blue); outline: none; box-shadow: 0 0 0 3px rgba(13, 70, 119, .12); }
        .btn-submit { width: 100%; min-height: 50px; border: 0; border-radius: 8px; background: var(--red); color: white; font: inherit; font-weight: 700; cursor: pointer; transition: transform .2s ease, background .2s ease, box-shadow .2s ease; }
        .btn-submit:hover, .btn-submit:focus { background: #982630; color: white; box-shadow: 0 8px 18px rgba(181, 46, 56, .2); transform: translateY(-1px); }
        .alert { border-radius: 8px; margin-bottom: 20px; padding: 12px 14px; font-size: .86rem; line-height: 1.5; }
        .alert-danger { border: 1px solid #f1b8b8; background: #fff2f2; color: #8d2028; }
        .alert-success { border: 1px solid #b8dfc5; background: #f0fbf3; color: #216b36; }
        .help-note { margin: 22px 0 0; color: var(--muted); font-size: .82rem; line-height: 1.6; }

        @media (max-width: 700px) {
            body { padding: 14px; }
            .recovery-container { display: block; border-radius: 14px; }
            .brand-panel { min-height: auto; padding: 30px 28px; }
            .brand-mark { width: 76px; height: 76px; margin-bottom: 22px; }
            .brand-panel h1 { font-size: 2.2rem; }
            .brand-copy { margin-top: 12px; }
            .brand-footer { margin-top: 32px; }
            .form-panel { padding: 34px 28px 40px; }
        }
    </style>
</head>
<body>
<main class="recovery-container">
    <section class="brand-panel">
        <div>
            <img src="images/logo.png" alt="Institute of Accountancy Arusha logo" class="brand-mark">
            <p class="eyebrow">IAA Digital Voting System</p>
            <h1>Get back to your account.</h1>
            <p class="brand-copy">We will help you reconnect with your secure voting account.</p>
        </div>
        <p class="brand-footer">Institute of Accountancy Arusha</p>
    </section>

    <section class="form-panel">
        <a href="login.php" class="back-link">&larr; Back to sign in</a>

        <div class="form-heading">
            <h2>Forgot your password?</h2>
            <p>Enter your admission number so the administrator can verify your account and reset your password.</p>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success" role="status"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label for="admission_number" class="form-label">Admission number</label>
                <input type="text" class="form-control" id="admission_number" name="admission_number" autocomplete="username" placeholder="Enter your admission number" required>
            </div>
            <button type="submit" class="btn-submit">Request password reset</button>
        </form>

        <p class="help-note">For account security, password resets are completed by the system administrator.</p>
    </section>
</main>
</body>
</html>

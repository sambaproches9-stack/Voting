<?php
include('connect.php');
session_start();

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admission_number = trim($_POST['admission_number']);
    $password = trim($_POST['password']);

    if (!empty($admission_number) && !empty($password)) {
        $query = "SELECT * FROM users WHERE admission_number = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $admission_number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                // Save all necessary session data
                $_SESSION['user_id'] = $row['id']; // Unique ID for vote tracking
                $_SESSION['admission_number'] = $admission_number;
                $_SESSION['role'] = $row['role'];

                // Redirect based on role
                if ($row['role'] == 'admin') {
                    header("Location: admin_dashboard.php");
                    exit;
                } elseif ($row['role'] == 'student') {
                    header("Location: user_dashboard.php");
                    exit;
                }
            } else {
                $error_message = "Wrong admission number or password.";
            }
        } else {
            $error_message = "Wrong admission number or password.";
        }
    } else {
        $error_message = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign in | IAA Digital Voting System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
            background:
                linear-gradient(135deg, rgba(7, 28, 53, .98), rgba(13, 70, 119, .92)),
                #071c35;
        }

        .login-container {
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

        .brand-mark {
            position: relative;
            z-index: 1;
            width: 104px;
            height: 104px;
            object-fit: contain;
            margin-bottom: 30px;
        }

        .eyebrow {
            position: relative;
            z-index: 1;
            margin: 0 0 14px;
            color: #ed7474;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .brand-panel h1 {
            position: relative;
            z-index: 1;
            max-width: 280px;
            margin: 0;
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(2rem, 4vw, 3rem);
            line-height: 1.04;
            letter-spacing: 0;
        }

        .brand-copy {
            position: relative;
            z-index: 1;
            max-width: 290px;
            margin: 18px 0 0;
            color: #b5c6d7;
            font-size: .94rem;
            line-height: 1.65;
        }

        .brand-footer {
            position: relative;
            z-index: 1;
            margin: 0;
            color: #8fa5ba;
            font-size: .78rem;
        }

        .form-panel {
            padding: 60px clamp(28px, 6vw, 76px);
            align-self: center;
        }

        .form-heading { margin-bottom: 32px; }

        .form-heading h2 {
            margin: 0 0 8px;
            color: var(--navy);
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.85rem;
            letter-spacing: 0;
        }

        .form-heading p { margin: 0; color: var(--muted); font-size: .92rem; }

        .form-label {
            margin-bottom: 8px;
            color: #344255;
            font-size: .82rem;
            font-weight: 700;
        }

        .form-control {
            min-height: 50px;
            border: 1px solid var(--line);
            border-radius: 8px;
            color: var(--ink);
            font-size: .94rem;
            box-shadow: none;
        }

        .form-control:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(13, 70, 119, .12);
        }

        .password-wrap { position: relative; }
        .password-wrap .form-control { padding-right: 48px; }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 7px;
            width: 36px;
            height: 36px;
            transform: translateY(-50%);
            border: 0;
            border-radius: 6px;
            color: var(--muted);
            background: transparent;
            font-size: .76rem;
            font-weight: 700;
        }

        .password-toggle:hover, .password-toggle:focus { color: var(--blue); background: #edf3f8; }

        .btn-login {
            min-height: 50px;
            margin-top: 8px;
            border: 0;
            border-radius: 8px;
            background: var(--red);
            color: white;
            font-weight: 700;
            letter-spacing: .01em;
            transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
        }

        .btn-login:hover, .btn-login:focus {
            background: #982630;
            color: white;
            box-shadow: 0 8px 18px rgba(181, 46, 56, .2);
            transform: translateY(-1px);
        }

        .alert { border-radius: 8px; font-size: .86rem; }
        .forgot-link { color: var(--blue); font-size: .84rem; font-weight: 700; text-decoration: none; }
        .forgot-link:hover { color: var(--red); text-decoration: underline; }

        @media (max-width: 700px) {
            body { padding: 14px; }
            .login-container { display: block; border-radius: 14px; }
            .brand-panel { min-height: auto; padding: 30px 28px; }
            .brand-mark { width: 76px; height: 76px; margin-bottom: 22px; }
            .brand-panel h1 { font-size: 2.2rem; }
            .brand-copy { margin-top: 12px; }
            .brand-footer { margin-top: 32px; }
            .form-panel { padding: 36px 28px 40px; }
        }
    </style>
</head>
<body>

<main class="login-container">
    <section class="brand-panel">
        <div>
            <img src="images/logo.png" alt="Institute of Accountancy Arusha logo" class="brand-mark">
            <p class="eyebrow">IAA Digital Voting System</p>
            <h1>Make your voice count.</h1>
            <p class="brand-copy">A secure, simple way for the IAA community to participate in elections.</p>
        </div>
        <p class="brand-footer">Institute of Accountancy Arusha</p>
    </section>

    <section class="form-panel">
        <div class="form-heading">
            <h2>Welcome back</h2>
            <p>Sign in to access your voting account.</p>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label for="admission_number" class="form-label">Admission number</label>
                <input type="text" class="form-control" id="admission_number" name="admission_number" autocomplete="username" placeholder="Enter your admission number" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="password-wrap">
                    <input type="password" class="form-control" id="password" name="password" autocomplete="current-password" placeholder="Enter your password" required>
                    <button type="button" class="password-toggle" id="togglePassword" aria-label="Show password">SHOW</button>
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100">Sign in to your account</button>
        </form>

        <div class="mt-4 text-center">
            <a href="forgot_password.php" class="forgot-link">Forgot your password?</a>
        </div>
    </section>
</main>

<script>
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');

    togglePassword.addEventListener('click', function () {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        this.textContent = isPassword ? 'HIDE' : 'SHOW';
        this.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
    });
</script>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

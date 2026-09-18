<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include('connect.php');

$error = '';
$success = '';

if (!isset($_SESSION['admission_number'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    $admission_number = $_SESSION['admission_number'];

    $query = "SELECT password FROM users WHERE admission_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $admission_number);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($current_password, $hashed_password)) {
        $error = "Current password is incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error = "New password must be at least 6 characters.";
    } else {
        $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password = ? WHERE admission_number = ?");
        $update->bind_param("ss", $new_hashed, $admission_number);
        $update->execute();
        $success = "Password changed successfully.";
    }
}
?>

<style>
    .account-page {
        width: min(100%, 980px);
        margin: 0 auto;
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
        font-size: clamp(1.65rem, 3vw, 2.15rem);
    }

    .page-heading p {
        margin: 0;
        color: var(--muted);
        font-size: .92rem;
    }

    .page-badge {
        padding: 9px 12px;
        border: 1px solid var(--line);
        border-radius: 7px;
        color: var(--blue);
        background: white;
        font-size: .78rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .account-card {
        max-width: 720px;
        margin: 0 auto;
        padding: clamp(22px, 4vw, 38px);
        border: 1px solid var(--line);
        border-radius: 12px;
        background: white;
        box-shadow: 0 12px 28px rgba(23, 44, 68, .05);
    }

    .section-label {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0 0 22px;
        color: var(--navy);
        font-family: 'Space Grotesk', sans-serif;
        font-size: 1.08rem;
    }

    .section-label i { color: var(--red); }

    .form-label {
        margin-bottom: 8px;
        color: #344255;
        font-size: .82rem;
        font-weight: 700;
    }

    .form-control {
        min-height: 49px;
        border: 1px solid var(--line);
        border-radius: 7px;
        color: var(--ink);
        font-size: .9rem;
    }

    .form-control:focus {
        border-color: var(--blue);
        box-shadow: 0 0 0 3px rgba(13, 70, 119, .12);
    }

    .field-help {
        margin: 7px 0 0;
        color: var(--muted);
        font-size: .75rem;
    }

    .btn-submit {
        min-height: 48px;
        border: 0;
        border-radius: 7px;
        background: var(--red);
        color: white;
        font-weight: 700;
        transition: background .2s ease, box-shadow .2s ease, transform .2s ease;
    }

    .btn-submit:hover, .btn-submit:focus {
        background: #982630;
        color: white;
        box-shadow: 0 8px 18px rgba(181, 46, 56, .2);
        transform: translateY(-1px);
    }

    .alert { border-radius: 8px; font-size: .86rem; }

    @media (max-width: 700px) {
        .page-heading { align-items: flex-start; flex-direction: column; }
        .page-badge { display: none; }
    }
</style>

<main class="account-page">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Account security</p>
            <h1>Change password</h1>
            <p>Update your account password to keep your voting profile secure.</p>
        </div>
        <span class="page-badge"><i class="bi bi-shield-lock me-1"></i>Private settings</span>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger" role="alert"><i class="bi bi-exclamation-circle me-2"></i><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success" role="alert"><i class="bi bi-check-circle me-2"></i><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" class="account-card">
        <h2 class="section-label"><i class="bi bi-key-fill"></i>Password details</h2>

        <div class="mb-4">
            <label for="current_password" class="form-label">Current password</label>
            <input type="password" name="current_password" id="current_password" class="form-control" required>
        </div>

        <div class="mb-4">
            <label for="new_password" class="form-label">New password</label>
            <input type="password" name="new_password" id="new_password" class="form-control" required>
            <p class="field-help">Use at least 6 characters.</p>
        </div>

        <div class="mb-4">
            <label for="confirm_password" class="form-label">Confirm new password</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
        </div>

        <div class="d-flex justify-content-end pt-3 border-top">
            <button type="submit" class="btn btn-submit px-4"><i class="bi bi-check2-circle me-2"></i>Update password</button>
        </div>
    </form>
</main>

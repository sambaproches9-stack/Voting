<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include('connect.php');

if (!isset($_SESSION['admission_number']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['page']) || $_GET['page'] !== 'update_user') {
    $redirect_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    header("Location: admin_dashboard.php?page=update_user&id=" . urlencode($redirect_id));
    exit;
}

$success = "";
$error = "";
$edit_id = $_GET['id'] ?? $_SESSION['admission_number'];

// Fetch existing user info
$stmt = $conn->prepare(
    "SELECT id, name, email, phone, role, profile_picture FROM users WHERE id = ? OR admission_number = ?"
);
$stmt->bind_param("is", $edit_id, $edit_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $profile_picture = $user['profile_picture'];

    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "images/";
        $profile_picture = $target_dir . basename($_FILES["profile_picture"]["name"]);
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $profile_picture);
    }

    $stmt2 = $conn->prepare(
        "UPDATE users SET name=?, email=?, phone=?, role=?, profile_picture=? WHERE id=?"
    );
    $stmt2->bind_param("sssssi", $name, $email, $phone, $role, $profile_picture, $edit_id);

    if ($stmt2->execute()) {
        $success = "Profile updated successfully.";
    } else {
        $error = "Error: " . $stmt2->error;
    }
    $stmt2->close();
}
?>

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

    .voter-page {
        width: min(100%, 980px);
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

    .voter-card {
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

    .section-label i {
        color: var(--red);
    }

    .profile-hero {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    .profile-preview {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #edf3f8;
        background: #f8fafc;
        box-shadow: 0 8px 24px rgba(23, 44, 68, .08);
    }

    .form-label {
        margin-bottom: 8px;
        color: #344255;
        font-size: .82rem;
        font-weight: 700;
    }

    .form-control, .form-select {
        min-height: 49px;
        border: 1px solid var(--line);
        border-radius: 7px;
        color: var(--ink);
        font-size: .9rem;
        background: white;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--blue);
        box-shadow: 0 0 0 3px rgba(13, 70, 119, .12);
    }

    .upload-field {
        padding: 16px;
        border: 1px dashed #b9c8d6;
        border-radius: 8px;
        background: #f8fafc;
    }

    .upload-field .form-control {
        background: white;
    }

    .field-help {
        margin-top: 7px;
        color: var(--muted);
        font-size: .75rem;
    }

    .btn-submit {
        min-height: 50px;
        border: 0;
        border-radius: 7px;
        background-color: #b52e38 !important;
        border-color: #b52e38 !important;
        color: #fff !important;
        font-weight: 700;
        transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
    }

    .btn-submit:hover, .btn-submit:focus {
        background-color: #982630 !important;
        border-color: #982630 !important;
        color: #fff !important;
        box-shadow: 0 8px 18px rgba(181, 46, 56, .2);
        transform: translateY(-1px);
    }

    .alert {
        border-radius: 8px;
        font-size: .86rem;
    }

    @media (max-width: 700px) {
        .voter-page {
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

<main class="voter-page">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Voter management</p>
            <h1>Update voter profile</h1>
            <p>Adjust the selected account details and photo.</p>
        </div>
        <span class="page-badge"><i class="bi bi-shield-check"></i> Admin only</span>
    </div>

    <?php if ($success): ?><div class="alert alert-success" role="alert"><?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger" role="alert"><?= $error ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="voter-card">
        <h2 class="section-label"><i class="bi bi-person-lines-fill"></i> User details</h2>

        <div class="profile-hero">
            <img src="<?= !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : 'images/default.jpg' ?>" alt="Current profile" class="profile-preview">
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <label class="form-label" for="name">Full name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>

            <div class="col-lg-6">
                <label class="form-label" for="email">Email address</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            <div class="col-lg-6">
                <label class="form-label" for="phone">Phone number</label>
                <input type="text" name="phone" id="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>" required>
            </div>

            <div class="col-lg-6">
                <label class="form-label" for="role">Account role</label>
                <select name="role" id="role" class="form-select">
                    <option value="student" <?= $user['role'] === 'student' ? 'selected' : '' ?>>Student</option>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>

            <div class="col-12">
                <div class="upload-field">
                    <label class="form-label" for="profile_picture"><i class="bi bi-image me-1"></i> Profile picture</label>
                    <input type="file" name="profile_picture" id="profile_picture" class="form-control">
                    <p class="field-help mb-0">Optional. Recommended image size: JPG, PNG, or WEBP.</p>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
            <button type="submit" class="btn btn-submit px-4"><i class="bi bi-check-circle me-2"></i>Save changes</button>
        </div>
    </form>
</main>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

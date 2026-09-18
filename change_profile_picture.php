<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include('connect.php');

if (!isset($_SESSION['admission_number']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit;
}

$admission_number = $_SESSION['admission_number'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];
    $target_dir = "uploads/";
    $file_name = basename($file["name"]);
    $target_file = $target_dir . time() . "_" . $file_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png'];

    if (in_array($file_type, $allowed_types)) {
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            $query = "UPDATE users SET profile_picture = ? WHERE admission_number = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $target_file, $admission_number);
            if ($stmt->execute()) {
                $message = "Profile picture updated successfully.";
            } else {
                $message = "Failed to update profile in database.";
            }
        } else {
            $message = "Failed to upload file.";
        }
    } else {
        $message = "Only JPG, JPEG, and PNG files are allowed.";
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

    .upload-field {
        padding: 16px;
        border: 1px dashed #b9c8d6;
        border-radius: 8px;
        background: #f8fafc;
    }

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
        background: white;
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
            <p class="eyebrow">Account settings</p>
            <h1>Edit profile picture</h1>
            <p>Choose a clear photo to personalize your voting profile.</p>
        </div>
        <span class="page-badge"><i class="bi bi-person-badge me-1"></i>Profile settings</span>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info" role="alert"><i class="bi bi-info-circle me-2"></i><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data" class="account-card">
        <h2 class="section-label"><i class="bi bi-image-fill"></i>Profile photo</h2>

        <div class="upload-field">
            <label for="profile_picture" class="form-label">Upload new profile picture</label>
            <input type="file" name="profile_picture" id="profile_picture" class="form-control" required>
            <p class="field-help">Accepted formats: JPG, JPEG, and PNG.</p>
        </div>

        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
            <button type="submit" class="btn btn-submit px-4"><i class="bi bi-cloud-arrow-up me-2"></i>Upload photo</button>
        </div>
    </form>
</main>

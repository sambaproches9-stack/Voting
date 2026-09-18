<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include('connect.php');

if (!isset($_SESSION['admission_number']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$message = '';
$message_type = 'success';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $election_type = $_POST['election_type'] ?? '';
    $president_name = trim($_POST['president_name'] ?? '');
    $vice_president_name = trim($_POST['vice_president_name'] ?? '');
    $president_file = $_FILES['president_image'] ?? null;
    $vice_president_file = $_FILES['vice_president_image'] ?? null;

    if ($election_type !== 'president' || $president_name === '' || $vice_president_name === '') {
        $message = 'Please select a position and enter both candidate names.';
        $message_type = 'danger';
    } elseif (!$president_file || !$vice_president_file || $president_file['error'] !== UPLOAD_ERR_OK || $vice_president_file['error'] !== UPLOAD_ERR_OK) {
        $message = 'Please upload a valid image for both candidates.';
        $message_type = 'danger';
    } else {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
        $president_extension = strtolower(pathinfo($president_file['name'], PATHINFO_EXTENSION));
        $vice_president_extension = strtolower(pathinfo($vice_president_file['name'], PATHINFO_EXTENSION));

        if (!in_array($president_extension, $allowed_extensions, true) || !in_array($vice_president_extension, $allowed_extensions, true)) {
            $message = 'Images must be JPG, JPEG, PNG, or WEBP files.';
            $message_type = 'danger';
        } else {
            $target_dir = 'images/';
            $president_img = $target_dir . uniqid('candidate_', true) . '.' . $president_extension;
            $vice_president_img = $target_dir . uniqid('candidate_', true) . '.' . $vice_president_extension;

            if (move_uploaded_file($president_file['tmp_name'], $president_img) && move_uploaded_file($vice_president_file['tmp_name'], $vice_president_img)) {
                $stmt = $conn->prepare("INSERT INTO candidates (president_name, vice_president_name, president_image, vice_president_image, election_type) VALUES (?, ?, ?, ?, 'president')");
                $stmt->bind_param('ssss', $president_name, $vice_president_name, $president_img, $vice_president_img);

                if ($stmt->execute()) {
                    $message = 'President and Vice President candidates added successfully.';
                    $president_name = '';
                    $vice_president_name = '';
                } else {
                    $message = 'The candidates could not be saved. Please try again.';
                    $message_type = 'danger';
                }
                $stmt->close();
            } else {
                $message = 'The images could not be uploaded. Please try again.';
                $message_type = 'danger';
            }
        }
    }
}
?>
  
<!DOCTYPE html>  
<html lang="en">  
<head>  
    <meta charset="UTF-8">  
    <title>Add Candidate | IAA Digital Voting System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        body { background: var(--canvas); color: var(--ink); font-family: 'DM Sans', sans-serif; }
        .candidate-page { width: min(100%, 980px); margin: 0 auto; padding: 12px 0 35px; }
        .page-heading { display: flex; align-items: end; justify-content: space-between; gap: 20px; margin-bottom: 24px; }
        .eyebrow { margin: 0 0 8px; color: var(--red); font-size: .72rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; }
        .page-heading h1 { margin: 0 0 6px; color: var(--navy); font-family: 'Space Grotesk', sans-serif; font-size: 2rem; }
        .page-heading p { margin: 0; color: var(--muted); font-size: .9rem; }
        .page-badge { display: flex; align-items: center; gap: 8px; padding: 9px 12px; border: 1px solid var(--line); border-radius: 7px; color: var(--blue); background: white; font-size: .78rem; font-weight: 700; white-space: nowrap; }
        .candidate-card { padding: clamp(22px, 4vw, 38px); border: 1px solid var(--line); border-radius: 12px; background: white; box-shadow: 0 12px 28px rgba(23, 44, 68, .05); }
        .section-label { display: flex; align-items: center; gap: 10px; margin: 0 0 22px; color: var(--navy); font-family: 'Space Grotesk', sans-serif; font-size: 1.08rem; }
        .section-label i { color: var(--red); }
        .form-label { margin-bottom: 8px; color: #344255; font-size: .82rem; font-weight: 700; }
        .form-control, .form-select { min-height: 49px; border: 1px solid var(--line); border-radius: 7px; color: var(--ink); font-size: .9rem; }
        .form-control:focus, .form-select:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(13, 70, 119, .12); }
        .field-help { margin-top: 7px; color: var(--muted); font-size: .75rem; }
        .candidate-block { margin-top: 30px; padding-top: 28px; border-top: 1px solid var(--line); }
        .candidate-block h3 { margin: 0 0 18px; color: var(--navy); font-family: 'Space Grotesk', sans-serif; font-size: 1rem; }
        .upload-field { padding: 16px; border: 1px dashed #b9c8d6; border-radius: 8px; background: #f8fafc; }
        .upload-field .form-control { background: white; }
        .btn-submit { min-height: 50px; border: 0; border-radius: 7px; background: var(--red); color: white; font-weight: 700; transition: transform .2s ease, background .2s ease, box-shadow .2s ease; }
        .btn-submit:hover, .btn-submit:focus { background: #982630; color: white; box-shadow: 0 8px 18px rgba(181, 46, 56, .2); transform: translateY(-1px); }
        .alert { border-radius: 8px; font-size: .86rem; }
        @media (max-width: 700px) {
            .candidate-page { padding-top: 0; }
            .page-heading { align-items: flex-start; flex-direction: column; }
            .page-heading h1 { font-size: 1.7rem; }
            .page-badge { display: none; }
        }
    </style>
    <script>
        function toggleForms() {
            const type = document.getElementById('election_type').value;
            document.getElementById('president_form').style.display = type === 'president' ? 'block' : 'none';
        }
    </script>
</head>  
<body>  
  
<main class="candidate-page">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Candidate management</p>
            <h1>Add candidate</h1>
            <p>Register the presidential team for the upcoming election.</p>
        </div>
        <span class="page-badge"><i class="bi bi-shield-check"></i> Admin only</span>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $message_type ?>" role="alert"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="candidate-card">
        <h2 class="section-label"><i class="bi bi-ui-checks-grid"></i> Election details</h2>
        <div class="mb-3">
            <label for="election_type" class="form-label">Election position</label>
            <select name="election_type" id="election_type" class="form-select" onchange="toggleForms()" required>
                <option value="">Choose a position</option>
                <option value="president" <?= ($_POST['election_type'] ?? '') === 'president' ? 'selected' : '' ?>>President and Vice President</option>
            </select>
            <p class="field-help">Choose the team position you are registering.</p>
        </div>

        <div id="president_form" class="candidate-block" style="display: <?= ($_POST['election_type'] ?? '') === 'president' ? 'block' : 'none' ?>;">
            <h3>Presidential team</h3>
            <div class="row g-4">
                <div class="col-lg-6">
                    <label for="president_name" class="form-label">President name</label>
                    <input type="text" name="president_name" id="president_name" class="form-control" value="<?= htmlspecialchars($president_name ?? '') ?>" placeholder="Enter full name" required>
                </div>
                <div class="col-lg-6">
                    <label for="vice_president_name" class="form-label">Vice President name</label>
                    <input type="text" name="vice_president_name" id="vice_president_name" class="form-control" value="<?= htmlspecialchars($vice_president_name ?? '') ?>" placeholder="Enter full name" required>
                </div>
                <div class="col-lg-6">
                    <div class="upload-field">
                        <label for="president_image" class="form-label"><i class="bi bi-image me-1"></i> President image</label>
                        <input type="file" name="president_image" id="president_image" class="form-control" accept=".jpg,.jpeg,.png,.webp" required>
                        <p class="field-help mb-0">JPG, PNG, or WEBP format.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="upload-field">
                        <label for="vice_president_image" class="form-label"><i class="bi bi-image me-1"></i> Vice President image</label>
                        <input type="file" name="vice_president_image" id="vice_president_image" class="form-control" accept=".jpg,.jpeg,.png,.webp" required>
                        <p class="field-help mb-0">JPG, PNG, or WEBP format.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
            <button type="submit" class="btn btn-submit px-4"><i class="bi bi-person-plus-fill me-2"></i>Add candidate team</button>
        </div>
    </form>
</main>
  
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>  
</body>  
</html>  
<?php include('footer.php');?>

<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include('connect.php');

if (!isset($_SESSION['admission_number']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['page']) || $_GET['page'] !== 'update_candidates') {
    $redirect_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    header("Location: admin_dashboard.php?page=update_candidates&id=" . urlencode($redirect_id));
    exit;
}

if (!isset($_GET['id'])) {
    echo "Invalid request.";
    exit;
}

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM candidates WHERE id = $id");

if ($result->num_rows === 0) {
    echo "Candidate not found.";
    exit;
}

$candidate = $result->fetch_assoc();

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $election_type = $candidate['election_type'];
    $updateQuery = "";

    if ($election_type === 'president') {
        $president_name = $_POST['president_name'];
        $vice_president_name = $_POST['vice_president_name'];

        // Image handling
        $president_image = $candidate['president_image'];
        $vice_president_image = $candidate['vice_president_image'];

        if (!empty($_FILES['president_image']['name'])) {
            $president_image = "images/" . basename($_FILES['president_image']['name']);
            move_uploaded_file($_FILES['president_image']['tmp_name'], $president_image);
        }

        if (!empty($_FILES['vice_president_image']['name'])) {
            $vice_president_image = "images/" . basename($_FILES['vice_president_image']['name']);
            move_uploaded_file($_FILES['vice_president_image']['tmp_name'], $vice_president_image);
        }

        $updateQuery = "UPDATE candidates SET 
                        president_name='$president_name',
                        vice_president_name='$vice_president_name',
                        president_image='$president_image',
                        vice_president_image='$vice_president_image'
                        WHERE id=$id";
    } 

    if ($conn->query($updateQuery)) {
        echo "<script>alert('Candidate updated successfully!'); window.location='admin_dashboard.php?page=view_candidates';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
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

    .candidate-page {
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

    .candidate-card {
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

    .current-image {
        width: 110px;
        height: 110px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid var(--line);
        background: #f8fafc;
        display: block;
        margin-bottom: 12px;
    }

    .upload-block {
        padding: 16px;
        border: 1px dashed #b9c8d6;
        border-radius: 8px;
        background: #f8fafc;
    }

    .btn-submit {
        min-height: 40px;
        width: auto;
        padding: 0.7rem 1.2rem;
        border: 0;
        border-radius: 7px;
        background: var(--red);
        color: white;
        font-weight: 700;
        transition: transform .2s ease, background .2s ease, box-shadow .2s ease;
    }

    .btn-submit:hover, .btn-submit:focus {
        background: #982630;
        color: white;
        box-shadow: 0 8px 18px rgba(181, 46, 56, .2);
        transform: translateY(-1px);
    }

    @media (max-width: 700px) {
        .candidate-page {
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

<main class="candidate-page">
    <div class="page-heading">
        <div>
            <p class="eyebrow">Candidate management</p>
            <h1>Edit candidate</h1>
            <p>Update the selected presidential team details.</p>
        </div>
        <span class="page-badge"><i class="bi bi-shield-check"></i> Admin only</span>
    </div>

    <form method="POST" enctype="multipart/form-data" class="candidate-card">
        <h2 class="section-label"><i class="bi bi-person-badge"></i> Candidate details</h2>

        <?php if ($candidate['election_type'] === 'president'): ?>
            <div class="row g-4">
                <div class="col-lg-6">
                    <label class="form-label">President name</label>
                    <input type="text" name="president_name" class="form-control" value="<?= htmlspecialchars($candidate['president_name']) ?>" required>
                </div>

                <div class="col-lg-6">
                    <label class="form-label">Vice president name</label>
                    <input type="text" name="vice_president_name" class="form-control" value="<?= htmlspecialchars($candidate['vice_president_name']) ?>" required>
                </div>

                <div class="col-lg-6">
                    <div class="upload-block">
                        <label class="form-label">President image</label>
                        <img src="<?= htmlspecialchars($candidate['president_image']) ?>" alt="President" class="current-image">
                        <input type="file" name="president_image" class="form-control">
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="upload-block">
                        <label class="form-label">Vice president image</label>
                        <img src="<?= htmlspecialchars($candidate['vice_president_image']) ?>" alt="Vice President" class="current-image">
                        <input type="file" name="vice_president_image" class="form-control">
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
            <button type="submit" class="btn btn-submit"><i class="bi bi-check-circle me-2"></i>Update candidate</button>
        </div>
    </form>
</main>

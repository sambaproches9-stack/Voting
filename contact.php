<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include('connect.php');

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $subject = trim($_POST["subject"]);
    $user_message = trim($_POST["message"]);

    if ($name && $email && $subject && $user_message) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $user_message);

        if ($stmt->execute()) {
            $message = "Your message has been sent successfully!";
        } else {
            $message = "Error sending your message. Please try again.";
        }
    } else {
        $message = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - IAA Voting</title>
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
            min-height: 100vh;
            margin: 0;
            background: var(--canvas);
            color: var(--ink);
            font-family: 'DM Sans', sans-serif;
        }

        .contact-page {
            width: min(100%, 980px);
            margin: 0 auto;
            padding: 48px 20px 56px;
        }

        .page-heading {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 28px;
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
            font-size: clamp(1.8rem, 4vw, 2.4rem);
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

        .contact-card {
            max-width: 760px;
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

        textarea.form-control { min-height: 132px; resize: vertical; }

        .form-control:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(13, 70, 119, .12);
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
            .contact-page { padding-top: 28px; }
            .page-heading { align-items: flex-start; flex-direction: column; }
            .page-badge { display: none; }
        }
    </style>
</head>
<body>

<main class="contact-page">
    <div class="page-heading">
        <div>
            <p class="eyebrow">IAA support</p>
            <h1>Contact us</h1>
            <p>Send a message to the voting system support team.</p>
        </div>
        <span class="page-badge"><i class="bi bi-envelope me-1"></i>We're here to help</span>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-info" role="alert"><i class="bi bi-info-circle me-2"></i><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" class="contact-card">
        <h2 class="section-label"><i class="bi bi-chat-left-text-fill"></i>Send a message</h2>

        <div class="row g-4">
            <div class="col-lg-6">
                <label class="form-label" for="name">Your name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="col-lg-6">
                <label class="form-label" for="email">Email address</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="col-12">
                <label class="form-label" for="subject">Subject</label>
                <input type="text" name="subject" id="subject" class="form-control" required>
            </div>

            <div class="col-12">
                <label class="form-label" for="message">Your message</label>
                <textarea name="message" id="message" class="form-control" required></textarea>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
            <button type="submit" class="btn btn-submit px-4"><i class="bi bi-send-fill me-2"></i>Send message</button>
        </div>
    </form>
</main>

<?php include('footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

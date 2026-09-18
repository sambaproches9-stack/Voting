<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us - IAA Voting System</title>
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
            background: var(--canvas);
            color: var(--ink);
            font-family: 'DM Sans', sans-serif;
        }

        .about-page {
            width: min(100%, 1080px);
            margin: 0 auto;
            padding: 48px 20px 56px;
        }

        .page-heading {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 30px;
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
            font-size: clamp(1.8rem, 4vw, 2.45rem);
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

        .about-section {
            margin-bottom: 22px;
            padding: clamp(22px, 4vw, 32px);
            border: 1px solid var(--line);
            border-radius: 11px;
            background: white;
            box-shadow: 0 10px 24px rgba(23, 44, 68, .05);
        }

        .section-heading {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 18px;
            color: var(--navy);
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.12rem;
        }

        .section-heading i { color: var(--red); }

        .about-section h3 {
            margin: 24px 0 7px;
            color: var(--navy);
            font-family: 'Space Grotesk', sans-serif;
            font-size: .98rem;
        }

        .about-section h3:first-of-type { margin-top: 0; }
        .about-section p { margin: 0; color: var(--muted); font-size: .9rem; line-height: 1.7; }

        .team-member img {
            width: 96px;
            height: 96px;
            object-fit: cover;
            border: 3px solid #edf3f8;
            border-radius: 50%;
        }

        .team-member h4 { margin: 12px 0 4px; color: var(--navy); font-family: 'Space Grotesk', sans-serif; font-size: .98rem; }
        .team-member small { color: var(--muted); font-size: .78rem; }

        .process-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
        .process-step { padding: 18px; border: 1px solid var(--line); border-radius: 8px; background: #f8fafc; }
        .process-step span { display: grid; width: 32px; height: 32px; margin-bottom: 12px; place-items: center; border-radius: 50%; color: white; background: var(--red); font-family: 'Space Grotesk', sans-serif; font-weight: 700; }
        .process-step h3 { margin: 0 0 6px; font-size: .9rem; }
        .process-step p { font-size: .82rem; line-height: 1.55; }

        .accordion { --bs-accordion-border-color: var(--line); --bs-accordion-btn-color: var(--navy); --bs-accordion-active-color: var(--navy); --bs-accordion-active-bg: #f8fafc; }
        .accordion-button { font-size: .88rem; font-weight: 700; }
        .accordion-body { color: var(--muted); font-size: .86rem; line-height: 1.65; }

        @media (max-width: 700px) {
            .about-page { padding-top: 28px; }
            .page-heading { align-items: flex-start; flex-direction: column; }
            .page-badge { display: none; }
            .process-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<main class="about-page">
    <div class="page-heading">
        <div>
            <p class="eyebrow">About IAA voting</p>
            <h1>Digital elections with confidence</h1>
            <p>Learn about the platform, the people behind it, and how it supports fair student leadership.</p>
        </div>
        <span class="page-badge"><i class="bi bi-info-circle me-1"></i>IAA Voting System</span>
    </div>

    <section class="about-section">
        <h2 class="section-heading"><i class="bi bi-shield-check"></i>System overview</h2>
        <h3>What we do</h3>
        <p>
            The IAA Voting System is an online platform designed to facilitate free and fair elections for students at the Institute of Accountancy Arusha. It replaces manual voting with a secure and transparent digital process that is accessible to all students.
        </p>

        <h3>Our mission</h3>
        <p>
            To promote fair student leadership through an innovative and secure digital voting experience that ensures transparency, trust, and participation.
        </p>

        <h3>Our vision</h3>
        <p>
            To lead the way in digital election systems in academic institutions, encouraging democratic values through technology-driven solutions.
        </p>
    </section>

    <section class="about-section">
        <h2 class="section-heading"><i class="bi bi-signpost-split-fill"></i>How voting works</h2>
        <div class="process-grid">
            <div class="process-step">
                <span>1</span>
                <h3>Sign in securely</h3>
                <p>Use your admission number and password to access your voter account.</p>
            </div>
            <div class="process-step">
                <span>2</span>
                <h3>Review candidates</h3>
                <p>Read the available presidential team information before making your choice.</p>
            </div>
            <div class="process-step">
                <span>3</span>
                <h3>Submit one vote</h3>
                <p>Select your preferred team and submit your ballot once during the voting period.</p>
            </div>
        </div>
    </section>

    <section class="about-section">
        <h2 class="section-heading"><i class="bi bi-people-fill"></i>Meet the development team</h2>
        <div class="row text-center g-4">
            <div class="col-md-4 team-member mb-4">
                <img src="images/OSTILDA.jpg" alt="Developer 1">
                <h4>Agathanice Kweka</h4>
                <small>Lead Developer</small>
            </div>
            <div class="col-md-4 team-member mb-4">
                <img src="images/Mwagala.jpg" alt="Developer 2">
                <h4>John Doe</h4>
                <small>UI/UX Designer</small>
            </div>
            <div class="col-md-4 team-member mb-4">
                <img src="images/Mesia.jpg" alt="Developer 3">
                <h4>Mary Smith</h4>
                <small>Backend Engineer</small>
            </div>
        </div>
    </section>

    <section class="about-section mb-0">
        <h2 class="section-heading"><i class="bi bi-question-circle-fill"></i>Frequently asked questions</h2>
        <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="faq1">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1">
                        How do I log in to vote?
                    </button>
                </h2>
                <div id="faqCollapse1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Use your admission number and password given by the admin to log in via the login page.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faq2">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2">
                        Can I vote more than once?
                    </button>
                </h2>
                <div id="faqCollapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        No. Each student is allowed to vote only once. The system automatically restricts multiple votes.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faq3">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3">
                        What if I forget my password?
                    </button>
                </h2>
                <div id="faqCollapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Contact the system administrator to reset your login credentials.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faq4">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4">
                        When can I vote?
                    </button>
                </h2>
                <div id="faqCollapse4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        You can vote only during the official voting period shown on your dashboard.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faq5">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse5">
                        How do I know if voting is open?
                    </button>
                </h2>
                <div id="faqCollapse5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        The dashboard displays the current voting status, start time, end time, and countdown when voting is open.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faq6">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse6">
                        Can I change my vote after submitting it?
                    </button>
                </h2>
                <div id="faqCollapse6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        No. Review your selection carefully before submitting because each student may vote only once.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faq7">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse7">
                        What should I do if my password does not work?
                    </button>
                </h2>
                <div id="faqCollapse7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Confirm that you entered your admission number and password correctly. Contact the system administrator if the problem continues.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faq8">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse8">
                        Can I update my profile picture?
                    </button>
                </h2>
                <div id="faqCollapse8" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Yes. Open Edit Photo from your dashboard and upload a JPG, JPEG, or PNG image.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faq9">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse9">
                        How can I view election results?
                    </button>
                </h2>
                <div id="faqCollapse9" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        After you have voted, open View Results from your dashboard to see the published election totals.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="faq10">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse10">
                        Who can I contact for assistance?
                    </button>
                </h2>
                <div id="faqCollapse10" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Use the Contact Us page to send a message to the voting system support team.
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include('footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

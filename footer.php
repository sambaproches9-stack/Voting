<style>
    .site-footer {
        margin-top: 48px;
        padding: 28px 20px;
        border-top: 1px solid #e2e8ef;
        background: #071c35;
        color: #b8c9d9;
        font-family: 'DM Sans', sans-serif;
        text-align: center;
    }

    .site-footer p {
        margin: 0;
        font-size: .78rem;
    }

    .site-footer p + p { margin-top: 8px; }

    .site-footer a {
        color: #f0a4a4;
        font-weight: 600;
        text-decoration: none;
        transition: color .2s ease;
    }

    .site-footer a:hover,
    .site-footer a:focus {
        color: white;
        text-decoration: underline;
    }
</style>

<footer class="site-footer">
    <p>&copy; <?= date('Y') ?> IAA Online Voting System. All rights reserved.</p>
    <p><a href="contact.php">Contact us</a><span aria-hidden="true"> &nbsp;|&nbsp; </span><a href="about.php">About the system</a></p>
</footer>
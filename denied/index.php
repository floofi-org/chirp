<?php $strictSession = false; require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php"; global $loggedIn; global $profile;

if ($loggedIn) {
    header("Location: /app");
    die();
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sunny Starbot</title>
    <script src="/assets/recaptcha.js"></script>
    <link href="/assets/bootstrap.min.css" rel="stylesheet">
    <script src="/assets/bootstrap.min.js"></script>
    <style>
        .grecaptcha-badge { visibility: hidden; }
        #mobile-separator { display: none; }

        @font-face {
            src: url("/assets/MLPFindYourSparkle.otf");
            font-family: "MLPFindYourSparkle";
        }

        @font-face {
            src: url("/assets/Jost-VariableFont_wght.ttf");
            font-family: "Jost";
            font-style: normal;
        }

        @font-face {
            src: url("/assets/Jost-Italic-VariableFont_wght.ttf");
            font-family: "Jost";
            font-style: italic;
        }

        #bottom, #bottom * {
            color: white !important;
        }

        *, .modal-title {
            font-family: Jost, -apple-system, system-ui, "Segoe UI", "Noto Sans", sans-serif;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: MLPFindYourSparkle, Jost, -apple-system, system-ui, "Segoe UI", "Noto Sans", sans-serif;
        }


        @media (max-width: 767px) {
            #mobile-separator { display: block; }
            #panes { grid-template-columns: 1fr !important; grid-gap: 0 !important; }
            #pane-left, #pane-right { order: 0 !important; }
        }

        .list-group, .list-group-item {
            border: none !important;
            border-radius: 0;
        }

        .list-group-item {
            border-radius: 10px;
        }

        .list-group-item:nth-child(1) {
            border-top-left-radius: 30px;
            border-top-right-radius: 30px;
        }

        .list-group-item:nth-last-child(1) {
            border-bottom-left-radius: 30px;
            border-bottom-right-radius: 30px;
            margin-bottom: 0 !important;
        }

        .list-group-item {
            background-color: rgba(255, 255, 255, .25);
            color: white;
            padding: 20px;
            margin-bottom: 10px;
        }

        main .text-muted {
            color: rgba(255, 255, 255, .75) !important;
        }

        textarea.form-control {
            border-radius: 30px !important;
            padding: 20px !important;
        }

        textarea.form-control[disabled], textarea.form-control {
            background-color: rgba(255, 255, 255, .25) !important;
            border: none !important;
        }

        textarea.form-control[disabled] {
            opacity: .5;
        }

        textarea.form-control, textarea.form-control::placeholder {
            color: white !important;
        }

        textarea.form-control::placeholder {
            opacity: .5;
        }

        .form-control:focus {
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(204, 39, 196, 0.5);
        }

        .btn-primary {
            border-radius: 30px !important;
            border: none !important;
            padding: 10px 20px !important;
            background-color: rgba(255, 255, 255, .25) !important;
        }

        .btn-primary:hover {
            background-color: rgba(255, 255, 255, .15) !important;
        }

        .btn-primary:active, .btn-primary:focus {
            background-color: rgba(255, 255, 255, .1) !important;
            box-shadow: 0 0 0 0.25rem rgba(204, 39, 196, 0.5) !important;
        }

        @keyframes pulse {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        .modal {
            backdrop-filter: blur(30px) !important;
            -webkit-backdrop-filter: blur(30px) !important;
        }

        .modal-content {
            background-color: #9c1d96 !important;
            color: white !important;
            border: none !important;
            border-radius: 30px !important;
        }

        .modal-header {
            border: none !important;
            padding: 20px !important;
        }

        .modal-content .btn-close {
            filter: invert(1);
        }

        .modal-body .alert:nth-last-child(1) {
            margin-bottom: 0 !important;
        }

        .alert {
            border-radius: 30px;
            padding: 20px;
            background-color: #691061 !important;
            color: white !important;
            border: none !important;
        }

        .history-player-action:hover img {
            background-color: rgba(0, 0, 0, .1);
        }

        .history-player-action:active img {
            background-color: rgba(0, 0, 0, .25);
        }
    </style>
    <link rel="shortcut icon" href="/assets/favicon.svg" type="image/svg+xml">
</head>
<body style="background-image: url('/assets/bg.png'); background-size: cover; background-position: center; background-attachment: fixed; background-color: #feaf91;">
<br><br>

<main class="container" style="background-color: #9c1d96; color: white; border-radius: 30px; padding: 30px;">
    <img src="/assets/banner.png" alt="Sunny Starbot" style="width: 100%; max-width: 768px; display: block; margin-left: auto; margin-right: auto; margin-bottom: 30px;">

    <div style="display: grid; grid-template-columns: 1fr 1fr; grid-gap: 30px;" id="panes">
        <div id="pane-right" style="order: 2;">
            <h2 style="margin-bottom: 30px;">You are not allowed to access Sunny Starbot</h2>

            <div class="alert alert-warning">
                It is currently in a private beta stage and is not accessible to everyone. If you believe you should have access to Sunny Starbot, <a href="https://equestria.dev/contact" target="_blank" style="color: white;">contact us</a>.
            </div>

            <a class="btn btn-primary" href="/auth/init">Use another account</a>

            <img src="/assets/sunny.png" style="max-width: 75%; margin-left: auto; display: block; margin-right: -30px;">
        </div>

        <div id="pane-left" style="order: 1;"></div>
    </div>
</main>


<div class="container" id="bottom">
    <hr>

    <div class="small text-muted">
        Protected by reCAPTCHA (see Google's <a href="https://policies.google.com/terms" target="_blank">Terms of service</a> or <a href="https://policies.google.com/privacy" target="_blank">Privacy policy</a>)<br>
        Made with ❤ by ponies in Equestria. My Little Pony is ™ and © Hasbro, All rights reserved. Artwork is the property of Hasbro.<br>
        © <?= date('Y') ?> <a href="https://equestria.dev" target="_blank">Equestria.dev Developers</a> · <a href="https://equestria.dev" target="_blank" data-bs-toggle="modal" data-bs-target="#terms">Terms of use</a> · Not created or endorsed by Hasbro.
    </div>

    <br><br>
</div>

</body>
</html>
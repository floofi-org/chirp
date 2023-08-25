<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sunny Starbot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="shortcut icon" href="/favicon.svg" type="image/svg+xml">
</head>
<body>
<div class="container">
    <br><br>
    <h1>Sunny Starbot</h1>

    <div class="alert alert-warning" style="margin-bottom: .5rem;">
        <b>You have been banned from Sunny Starbot.</b> It is currently in a private beta stage and is not accessible to everyone. If you believe you should have access to Sunny Starbot, <a href="https://equestria.dev/contact/" target="_blank">contact us</a>.<?php if (isset($_GET["user"])): ?> You have tried to log in as: <?= strip_tags(base64_decode($_GET["user"])) ?>.<?php endif; ?>
    </div>
    
    <span><a href="/auth/init" class="btn btn-primary">Use another account</a></span>
</div>
</html>
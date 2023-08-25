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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container">
    <br><br>
    <h1>Sunny Starbot</h1>
    <p>Sunny Starbot is currently in a private beta stage. If you are allowed to log in, click on the button below:</p>
    <a href="/auth/init" class="btn btn-outline-primary">Log in with your Equestria.dev account</a>
</div>
</html>
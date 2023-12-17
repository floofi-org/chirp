<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php"; global $loggedIn; global $profile; global $hasPlus ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Plus | Sunny Starbot</title>
    <link href="/assets/bootstrap.min.css" rel="stylesheet">
    <script src="/assets/bootstrap.min.js"></script>
    <link rel="shortcut icon" href="/assets/favicon.svg" type="image/svg+xml">
</head>
<body>
<div class="container">
    <br><br>
    <h1>Sunny Starbot Plus: extended pony text-to-speech</h1>
    <?php if ($hasPlus): ?>
    <div class="alert alert-secondary">
        Your Equestria.dev account already has Sunny Starbot Plus. <a href="/app/">Head over to the main page</a> and start taking advantage of all the features Sunny Starbot Plus can provide.
    </div>
    <?php endif; ?>
    <p>If you like your pony text-to-speech done well, you can get Sunny Starbot Plus, which gives you access to more features than the regular version of Sunny Starbot, for a truly unique text-to-speech experience. Here is what you get access to with Sunny Starbot Plus:</p>
    <ul>
        <li style="margin-top: 10px;"><b>Spectral frequency and inference graphs:</b> These graphs can be used to measure the general performance of AI model. The closer the inference graph looks to a straight line, the better quality your audio will be. Using the spectral frequency graph, you can also guess the amount of noise contained in the audio.</li>
        <li style="margin-top: 10px;"><b>Early access to improved models:</b> Whenever a new version of the Sunny Starbot model comes out, Plus users get access to it early while we tweak the last few things to make sure it works properly.</li>
        <li style="margin-top: 10px;"><b>Faster and longer generations:</b> Sunny Starbot Plus gives you access to faster and more capable processing units, to generate longer audios (up to 320 characters) in less time than without Plus. (*the actual speed depends on server load)</li>
        <li style="margin-top: 10px;"><b>Relaxed content restrictions:</b> With Plus, less content is censored by the content filter (729 words less). While you still have to use AI responsibly, you will be able to better express your creativity through Sunny Starbot.</li>
    </ul>
    <p>If you are interested in getting Sunny Starbot Plus, please get in touch with us at <a href="mailto:raindrops@equestria.dev">raindrops@equestria.dev</a>.</p>
</div>
</html>
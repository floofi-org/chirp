<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php"; global $loggedIn; global $profile;

$keys = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/keys.json"), true);

function getAPIKey(): string {
    global $profile;
    global $keys;

    if (!isset($keys[$profile["id"]])) {
        $keys[$profile["id"]] = substr(str_replace("+", ".", str_replace("/", "_", str_replace("=", "", base64_encode(random_bytes(64))))), 0, 48);
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/keys.json", json_encode($keys));
    }

    return $keys[$profile["id"]];
}

?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>API docs (v1) | Floofi Voice Generator</title>
    <link href="/assets/bootstrap/bootstrap.min.css" rel="stylesheet">
    <script src="/assets/bootstrap/bootstrap.min.js"></script>
    <link rel="shortcut icon" href="https://voice.floo.fi/assets/favicon.png" type="image/svg+xml">
    <style>
        #api-key {
            color: transparent;
            background-color: black;
        }

        #api-key:hover {
            background-color: transparent;
            color: inherit;
        }
    </style>
</head>
<body>
<div class="container">
    <br><br>
    <h1>Floofi Voice Generator API docs <span class="badge bg-secondary">v1</span></h1>

    <div class="alert alert-danger">
        Version 1 of the API is now unavailable, please use a later version instead. The latest version is version 2, which you can get docs for <a href="/docs/v2">here</a>.
    </div>

    <br><br><br>
</div>
</html>

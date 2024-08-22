<?php

$app = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens.json"), true);
$server = "account.equestria.dev";

if (!isset($_GET['origin'])) {
    $_GET['origin'] = "https://voice.floo.fi";
} else if ($_GET['origin'] !== "https://voice.floo.fi" && $_GET['origin'] !== "http://localhost:3000") {
    $_GET['origin'] = "https://voice.floo.fi";
}

setcookie("_callback", $_GET['origin'], 0, "/");

header("Location: https://$server/hub/api/rest/oauth2/auth?client_id=" . $app["oauth"]["id"] . "&response_type=code&redirect_uri=https://voice-api.floo.fi/auth/callback&scope=Hub&access_type=offline");
die();

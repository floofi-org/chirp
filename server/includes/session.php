<?php

global $strictSession;
global $profile;

if (!isset($strictSession)) $strictSession = true;
$loggedIn = false;

$token = $_COOKIE['SSB_SESSION_TOKEN'] ?? null;

if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    if (str_starts_with($_SERVER['HTTP_AUTHORIZATION'], "PrivateToken ")) {
        $token = substr($_SERVER['HTTP_AUTHORIZATION'], 13);
    }
}

if (isset($token) && str_starts_with($token, "ssb-") && !str_contains($token, "/") && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens/" . $token)) {
    $profile = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens/" . $token), true);
    $loggedIn = true;
}

if ($strictSession && !$loggedIn) {
    header("Location: /auth/init");
    die();
} elseif ($strictSession && in_array($profile["id"], json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens.json"), true)['oauth']['banned'])) {
    header("HTTP/1.1 400 Bad Request");
    header("Content-Type: text/plain");
    die("User is banned.");
}

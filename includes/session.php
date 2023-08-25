<?php

global $strictSession;
global $profile;

if (!isset($strictSession)) $strictSession = true;
$loggedIn = false;

if (isset($_COOKIE["SSB_SESSION_TOKEN"]) && str_starts_with($_COOKIE["SSB_SESSION_TOKEN"], "ssb-") && !str_contains($_COOKIE["SSB_SESSION_TOKEN"], "/") && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens/" . $_COOKIE["SSB_SESSION_TOKEN"])) {
    $profile = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens/" . $_COOKIE["SSB_SESSION_TOKEN"]), true);
    $loggedIn = true;
}

if ($strictSession && !$loggedIn) {
    header("Location: /");
    die();
}
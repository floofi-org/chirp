<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php"; global $loggedIn; global $profile;
$possible = true;

foreach (array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs"), function ($i) { return !str_starts_with($i, "."); }) as $item) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt")) {
        if (file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt") === $profile["id"]) {
            if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/complete.txt")) $possible = false;
        }
    }
}

die(json_encode($possible));
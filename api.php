<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/api/common.php";

if ($_SERVER['HTTP_X_FORWARDED_HOST'] !== "api.sunnystarbot.equestria.dev") {
    header("Location: /");
    die();
}

if (!isset($_GET["_"]) || trim($_GET["_"]) === "") {
    header("Location: https://sunnystarbot.equestria.dev/docs");
    die();
}

global $parts;
$parts = array_filter(explode("/", $_GET["_"]), function ($i) { return trim($i) !== ""; });

if (count($parts) < 2) {
    error(400);
}

if (str_starts_with($parts[0], "v") && !str_contains($parts[0], "/")) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/api/" . $parts[0])) {
        if (!str_contains($parts[1], "/")) {
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/api/" . $parts[0] . "/" . $parts[1] . ".php")) {
                require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/api/" . $parts[0] . "/" . $parts[1] . ".php";
            } else {
                error(404);
            }
        } else {
            error(400);
        }
    } else {
        error(501);
    }
} else {
    error(400);
}
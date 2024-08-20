<?php

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Cookie, Origin, User-Agent, Host, Authorization");

if ($_SERVER['HTTP_ORIGIN'] === "http://localhost:3000" ||
    $_SERVER['HTTP_ORIGIN'] === "https://voice.floo.fi") {
    header("Access-Control-Allow-Origin: $_SERVER[HTTP_ORIGIN]");
}

if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") die();

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/api/common.php";

if (!isset($_GET["_"]) || trim($_GET["_"]) === "") {
    header("Location: https://voice-api.floo.fi/docs");
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

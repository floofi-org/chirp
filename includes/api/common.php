<?php

$strictSession = false;
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php";
global $loggedIn; global $profile;

function _crash($errno, $message, $file, $line) {
    file_put_contents("/tmp/error.txt", "[$errno] $message\n    at " . $file . ":" . $line . "\n");
    error(500);
}

set_error_handler("_crash", E_ALL);

$request_raw = file_get_contents('php://input');
$json_object = $_POST = json_decode($request_raw, true);

global $data;
$data = [
    "error" => null,
    "output" => null
];
header("Content-Type: application/json");

function endpoint($methods = ["GET"], $needsId = false, $parameters = []) {
    global $parts; global $loggedIn;

    $keys = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/keys.json"), true);

    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        if (str_starts_with($_SERVER['HTTP_AUTHORIZATION'], "Bearer ")) {
            $token = substr($_SERVER['HTTP_AUTHORIZATION'], 7);

            if (in_array($token, array_values($keys))) {
                global $user;
                $user = array_keys($keys)[array_search($token, array_values($keys))];

                global $profile;
                $profile = [
                    "id" => $user
                ];
            } else {
                error(401);
            }
        } else {
            error(401);
        }
    } elseif (!$loggedIn) {
        error(401);
    }

    if (!in_array($_SERVER['REQUEST_METHOD'], $methods)) error(405);
    if ($needsId && !isset($parts[2])) error(400);

    foreach ($parameters as $name => $data) {
        if ($data["post"]) {
            if ($data["required"] && !isset($_POST[$name])) {
                error(400);
            }

            if (isset($_POST[$name])) {
                if (isset($data["length"]) && strlen($_POST[$name]) > $data["length"]) {
                    error(413);
                }
            }
        } else {
            if ($data["required"] && !isset($_GET[$name])) {
                error(400);
            }

            if (isset($_GET[$name])) {
                if (isset($data["length"]) && strlen($_GET[$name]) > $data["length"]) {
                    error(413);
                }
            }
        }
    }
}

function output($out) {
    global $data;
    $data["output"] = $out;
    die(json_encode($data));
}

function error($code) {
    switch ($code) {
        case 400:
            header("HTTP/1.1 400 Bad Request");
            die(json_encode([
                "error" => [
                    "code" => 400,
                    "name" => "Bad Request",
                    "see" => "https://sunnystarbot.equestria.dev/docs/"
                ],
                "output" => null
            ]));

        case 401:
            header("HTTP/1.1 401 Unauthorized");
            die(json_encode([
                "error" => [
                    "code" => 401,
                    "name" => "Unauthorized",
                    "see" => "https://sunnystarbot.equestria.dev/docs/"
                ],
                "output" => null
            ]));

        case 403:
            header("HTTP/1.1 403 Forbidden");
            die(json_encode([
                "error" => [
                    "code" => 403,
                    "name" => "Forbidden",
                    "see" => "https://sunnystarbot.equestria.dev/docs/"
                ],
                "output" => null
            ]));

        case 404:
            header("HTTP/1.1 404 Not Found");
            die(json_encode([
                "error" => [
                    "code" => 404,
                    "name" => "Not Found",
                    "see" => "https://sunnystarbot.equestria.dev/docs/"
                ],
                "output" => null
            ]));

        case 405:
            header("HTTP/1.1 405 Method Not Allowed");
            die(json_encode([
                "error" => [
                    "code" => 405,
                    "name" => "Method Not Allowed",
                    "see" => "https://sunnystarbot.equestria.dev/docs/"
                ],
                "output" => null
            ]));

        case 409:
            header("HTTP/1.1 409 Conflict");
            die(json_encode([
                "error" => [
                    "code" => 409,
                    "name" => "Conflict",
                    "see" => "https://sunnystarbot.equestria.dev/docs/"
                ],
                "output" => null
            ]));

        case 413:
            header("HTTP/1.1 413 Payload Too Large");
            die(json_encode([
                "error" => [
                    "code" => 413,
                    "name" => "Payload Too Large",
                    "see" => "https://sunnystarbot.equestria.dev/docs/"
                ],
                "output" => null
            ]));

        case 429:
            header("HTTP/1.1 429 Too Many Requests");
            die(json_encode([
                "error" => [
                    "code" => 429,
                    "name" => "Too Many Requests",
                    "see" => "https://sunnystarbot.equestria.dev/docs/"
                ],
                "output" => null
            ]));

        case 451:
            header("HTTP/1.1 451 Unavailable For Legal Reasons");
            die(json_encode([
                "error" => [
                    "code" => 451,
                    "name" => "Unavailable For Legal Reasons",
                    "see" => "https://sunnystarbot.equestria.dev/docs/"
                ],
                "output" => null
            ]));

        case 500:
            header("HTTP/1.1 500 Internal Server Error");
            die(json_encode([
                "error" => [
                    "code" => 500,
                    "name" => "Internal Server Error",
                    "see" => "https://sunnystarbot.equestria.dev/docs/"
                ],
                "output" => null
            ]));
    }
}

if (in_array($profile["id"], json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens.json"), true)['oauth']['banned'])) {
    error(403);
}

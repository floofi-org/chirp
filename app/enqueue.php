<?php

if (!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] !== "https://sunnystarbot.equestria.dev/app/") die();
if (!isset($_SERVER['HTTP_USER_AGENT']) || (!str_contains($_SERVER['HTTP_USER_AGENT'], "Chrome/") && !str_contains($_SERVER['HTTP_USER_AGENT'], "Safari/") && !str_contains($_SERVER['HTTP_USER_AGENT'], "Firefox/") && !str_contains($_SERVER['HTTP_USER_AGENT'], "Gecko"))) die();

function uuid() {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/updates.php"; global $loggedIn; global $profile;
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php"; global $loggedIn; global $profile;
if (!isset($_GET["text"])) die("false");
$possible = true;

foreach (array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs"), function ($i) { return !str_starts_with($i, "."); }) as $item) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt") && !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/blocked.txt")) {
        if (file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt") === $profile["id"]) {
            if (!(file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/complete.txt") && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/audio.wav"))) $possible = false;
        }
    }
}

if (!$possible) die("true");

/*if (!isset($_GET["token"])) {
    die("true");
} else {
    require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/recaptcha/src/autoload.php";

    $recaptcha = new \ReCaptcha\ReCaptcha(json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens.json"), true)["recaptcha"]["secret"]);
    $resp = $recaptcha->setExpectedHostname("sunnystarbot.equestria.dev")
        ->verify($_GET["token"], $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']);
    if (!$resp->isSuccess()) {
        die("true");
    }
}*/

$code = getFilterCode($_GET["text"]);

// ---------------------------

$modelText = substr(trim(preg_replace("/[^a-zA-Z':\d()[\].,?;\"! ~]/", "", $_GET["text"] ?? "")), 0, 160);
$uid = uuid();
$fid = str_replace("-", "", uuid() . "-" . $profile["id"] . "-" . $uid);

while (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid)) {
    $fid = str_replace("-", "", uuid() . "-" . $profile["id"] . "-" . $uid);
}

mkdir($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid);
file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid . "/author.txt", $profile["id"]);
file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid . "/timestamp.txt", time());
file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid . "/input_orig.txt", substr(trim($_GET["text"] ?? ""), 0, 160));

if ($profile["id"] === json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens.json"), true)['oauth']['admin'] && $code === 0) {
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid . "/reviewed.txt", "");
}

if ($code > 0) {
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid . "/held.txt", $code);
}

file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid . "/version.txt", trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/version-plus.txt")));

if ($code < 2) {
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid . "/input_plus.txt", $modelText);
} else {
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid . "/blocked.txt", "");
}

if ($code > 1) {
    die("false");
} else {
    die(json_encode($fid));
}

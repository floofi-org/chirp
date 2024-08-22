<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/updates.php";
global $profile;

function uuid() {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

endpoint(["POST"], false, [
    "input" => [
        "length" => 160,
        "required" => true,
        "post" => true
    ]
]);

$possible = true;

foreach (array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs"), function ($i) { return !str_starts_with($i, "."); }) as $item) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt") && !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/blocked.txt")) {
        if (file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt") === $profile["id"]) {
            if (!(file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/complete.txt") && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/audio.wav"))) $possible = false;
        }
    }
}

if (!$possible || !getPossible()) error(429);

$code = getFilterCode($_POST["input"]);

// ---------------------------

$modelText = substr(trim(preg_replace("/[^a-zA-Z':\d()[\].,?! ~]/", "", $_POST["input"] ?? "")), 0, 160);
$uid = uuid();
$fid = str_replace("-", "", uuid() . "-" . $profile["id"] . "-" . $uid);

while (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid)) {
    $fid = str_replace("-", "", uuid() . "-" . $profile["id"] . "-" . $uid);
}

mkdir($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid);
file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid . "/model.txt", "sunny");
file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid . "/author.txt", $profile["id"]);
file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid . "/timestamp.txt", time());
file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid . "/input_orig.txt", substr(trim($_POST["input"] ?? ""), 0, 160));

if ($profile["id"] === json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens.json"), true)['oauth']['admin']) {
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid . "/reviewed.txt", "");
}

if ($code > 0) {
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid . "/held.txt", $code);
}

file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid . "/version.txt", json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/models.json"), true)["sunny"]["version"]);

if ($code < 2) {
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid . "/inputtxt", $modelText);
} else {
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid . "/blocked.txt", "");
}

if ($code > 1) {
    error(451);
} else {
    output([
        "id" => $fid
    ]);
}

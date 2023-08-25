<?php

function uuid() {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php"; global $loggedIn; global $profile;
if (!isset($_GET["text"])) die("false");
$possible = true;

foreach (array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs"), function ($i) { return !str_starts_with($i, "."); }) as $item) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt")) {
        if (file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt") === $profile["id"]) {
            if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/complete.txt")) $possible = false;
        }
    }
}

if (!$possible) die("true");

if (!isset($_GET["token"])) {
    die("true");
} else {
    require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/recaptcha/src/autoload.php";

    $recaptcha = new \ReCaptcha\ReCaptcha(json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens.json"), true)["recaptcha"]["secret"]);
    $resp = $recaptcha->setExpectedHostname("sunnystarbot.equestria.dev")
        ->verify($_GET["token"], $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']);
    if (!$resp->isSuccess()) {
        die("true");
    }
}

$tokens = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens.json"), true);

$start = microtime(true);
$text = strtolower(trim($_GET["text"] ?? ""));
$list = explode("\n", trim(strtolower(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/list.txt"))));

if ($text === "") {
    die("false");
}

$data = [
    "class" => "text-success",
    "text" => "CLEAR — The text is family-friendly and does not trip any content filter. Query will be processed."
];

$code = 0;
$external = false;
$tripped = [];

$ptext = " " . preg_replace("/ +/", " ", preg_replace("/[^a-z]/", " ", $text)) . " ";

foreach ($list as $item) {
    $item = trim($item);

    if (str_contains($ptext, $item)) {
        if (strlen($item) > 4 || str_contains($ptext, " " . $item . " ")) {
            $code = 2;
            $tripped[] = $item;
        } else if (str_contains($ptext, " " . $item . " ")) {
            $code = 1;
            $tripped[] = $item;
        }
    }
}

if ($code !== 2 && file_get_contents("https://www.purgomalum.com/service/containsprofanity?text=" . rawurlencode($text)) === "true") {
    $code = 3;
}

if ($code !== 2 && $code !== 3) {
    $openai = json_decode(file_get_contents("https://api.openai.com/v1/moderations", false, stream_context_create([
        'http' => [
            'header' => "Content-type: application/json\r\n" .
                "Authorization: Bearer " . $tokens['openai'] . "\r\n",
            'method' => 'POST',
            'content' => json_encode([
                "input" => $text
            ])
        ],
    ])), true);

    if (isset($openai["results"][0]["flagged"]) && $openai["results"][0]["flagged"]) {
        $code = 4;
    }
}

if ($code === 1) {
    $data = [
        "class" => "text-warning",
        "text" => "FLAGGED — The text is potentially not family-friendly and needs further investigation. Query will still be processed, but the account holder will be flagged for potentially inappropriate behavior."
    ];
} else if ($code === 2) {
    $data = [
        "class" => "text-danger",
        "text" => "BLOCKED:1 — The text is not family-friendly and has been blocked. Query will not be processed and the account holder will be flagged for potentially inappropriate behavior (and may get terminated)."
    ];
} else if ($code === 3) {
    $data = [
        "class" => "text-danger",
        "text" => "BLOCKED:2 — The text is not family-friendly, according to external sources, and has been blocked. Query will not be processed and the account holder will be flagged for potentially inappropriate behavior (and may get terminated)."
    ];
} else if ($code === 4) {
    $data = [
        "class" => "text-danger",
        "text" => "BLOCKED:3 — The text is not family-friendly, according to OpenAI's content moderation model, and has been blocked. Query will not be processed and the account holder will be flagged for potentially inappropriate behavior (and may get terminated)."
    ];
}

$tripped = array_unique($tripped);

if (count($tripped) > 0) {
    $data["text"] .= " The following local filter(s) was/were triggered: " . implode(", ", $tripped) . " (this will not be shown to end users).";
}

$data["timing"] = microtime(true) - $start;

if ($code > 0) {
    // TODO: Save stuff
}

if ($code > 1) {
    die("false");
}

// ---------------------------

$modelText = substr(trim($_GET["text"] ?? ""), 0, 160);
$uid = uuid();
$fid = str_replace("-", "", uuid() . "-" . $profile["id"] . "-" . $uid);

while (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid)) {
    $fid = str_replace("-", "", uuid() . "-" . $profile["id"] . "-" . $uid);
}

mkdir($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid);
file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid . "/author.txt", $profile["id"]);
file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid . "/timestamp.txt", time());
file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid . "/input_orig.txt", substr(trim($_GET["text"] ?? ""), 0, 160));
file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $fid . "/input.txt", $modelText);

die(json_encode($fid));
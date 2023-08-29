<?php

function getList($api = false, $id = null) {
    global $profile;
    $data = [];

    if ($api && isset($id)) {
        $item = $id;

        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item) && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt") && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt") && !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/blocked.txt")) {
            return [
                "id" => $item,
                "author" => trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt")),
                "time" => date('c', (int)file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/timestamp.txt")),
                "status" => file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/removed.txt") ? "removed" : (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/complete.txt") ? "processed" : ((file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt") && !file_exists("/proc/" . trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt")))) ? "crashed" : (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt") ? "generating" : "queued"))),
                "input" => file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt"),
                "filename" => "sunnystarbot-" . substr("00000000", 0, 8 - strlen(dechex(crc32($item)))) . dechex(crc32($item)) . "-" . substr(preg_replace("/^-|-$/", "", preg_replace("/-+/", "-", preg_replace("/[^a-z]/", "-", strtolower(trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt")))))), 0, 30) . ".wav",
                "audio_url" => (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/complete.txt") && !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/removed.txt")) ? "https://sunnystarbot.equestria.dev/static/" . $item . "/audio.wav" : null
            ];
        } else {
            error(404);
        }
    }

    foreach (array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs"), function ($i) { return !str_starts_with($i, "."); }) as $item) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt") && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt") && !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/blocked.txt")) {
            if (file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt") === $profile["id"] && (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/removed.txt") || $api)) {
                if ($api) {
                    $data[] = [
                        "id" => $item,
                        "author" => trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt")),
                        "time" => date('c', (int)file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/timestamp.txt")),
                        "status" => file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/removed.txt") ? "removed" : (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/complete.txt") ? "processed" : ((file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt") && !file_exists("/proc/" . trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt")))) ? "crashed" : "queued")),
                        "input" => file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt"),
                        "filename" => "sunnystarbot-" . substr("00000000", 0, 8 - strlen(dechex(crc32($item)))) . dechex(crc32($item)) . "-" . substr(preg_replace("/^-|-$/", "", preg_replace("/-+/", "-", preg_replace("/[^a-z]/", "-", strtolower(trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt")))))), 0, 30) . ".wav",
                        "audio_url" => (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/complete.txt") && !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/removed.txt")) ? "https://sunnystarbot.equestria.dev/static/" . $item . "/audio.wav" : null
                    ];
                } else {
                    $data[] = [
                        "filename" => "sunnystarbot-" . substr("00000000", 0, 8 - strlen(dechex(crc32($item)))) . dechex(crc32($item)) . "-" . substr(preg_replace("/^-|-$/", "", preg_replace("/-+/", "-", preg_replace("/[^a-z]/", "-", strtolower(trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt")))))), 0, 30) . ".wav",
                        "id" => $item,
                        "time" => date('c', (int)file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/timestamp.txt")),
                        "processed" => file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/complete.txt"),
                        "queued" => !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt"),
                        "crashed" => file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt") && !file_exists("/proc/" . trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt"))),
                        "input" => file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt")
                    ];
                }
            }
        }
    }

    usort($data, function ($a, $b) {
        return strtotime($b["time"]) - strtotime($a["time"]);
    });

    return $data;
}

function getPossible() {
    global $profile;
    $possible = true;

    foreach (array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs"), function ($i) { return !str_starts_with($i, "."); }) as $item) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt") && !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/blocked.txt")) {
            if (file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt") === $profile["id"]) {
                if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/complete.txt") && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt") && file_exists("/proc/" . trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt")))) $possible = false;
            }
        }
    }

    return $possible;
}

function getFilterCode($original) {
    $tokens = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens.json"), true);

    $text = strtolower(trim($original ?? ""));
    $list = explode("\n", trim(strtolower(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/list.txt"))));

    if ($text === "") {
        die("false");
    }

    $code = 0;
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

    return $code;
}
<?php

function getVersionFromTimestamp($timestamp = 0) {
    $v2 = strtotime("2023-08-22");
    $v4 = strtotime("2023-08-23");
    $v6 = strtotime("2023-08-23");
    $v7 = strtotime("2023-08-24");
    $v8 = strtotime("2023-08-25");
    $v9 = strtotime("2023-09-02");
    $v10 = strtotime("2023-10-04");

    if ($timestamp > $v10) return "10.1077.50750";
    if ($timestamp > $v9) return "9.905.30250";
    if ($timestamp > $v8) return "8.527.10720";
    if ($timestamp > $v7) return "7.407.5000";
    if ($timestamp > $v6) return "6.226";
    if ($timestamp > $v4) return "4.150";
    if ($timestamp > $v2) return "2.81";
    return "1.0";
}

function getList($api = false, $id = null, $count = INF) {
    global $profile;
    $data = [];

    if ($api && isset($id)) {
        $item = $id;

        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item) && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt") && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt") && !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/blocked.txt")) {
            return [
                "id" => $item,
                "version" => file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/version.txt") ? trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/version.txt")) : getVersionFromTimestamp((int)file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/timestamp.txt")),
                "author" => trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt")),
                "time" => date('c', (int)file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/timestamp.txt")),
                "status" => file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/removed.txt") ? "removed" : (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/complete.txt") && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/audio.wav") ? "processed" : ((file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt") && !file_exists("/proc/" . trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt")))) ? (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_plus.txt") ? "generating" : "crashed") : (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt") ? "generating" : "queued"))),
                "input" => file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt"),
                "filename" => "sunnystarbot-" . substr("00000000", 0, 8 - strlen(dechex(crc32($item)))) . dechex(crc32($item)) . "-" . substr(preg_replace("/^-|-$/", "", preg_replace("/-+/", "-", preg_replace("/[^a-z]/", "-", strtolower(trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt")))))), 0, 30) . ".wav",
                "audio_url" => (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/complete.txt") && !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/removed.txt")) ? "https://cdn.equestria.dev/sunnystarbot/content/" . $item . "/audio.wav" : null,
                "explicit" => file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/held.txt")
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
                        "version" => file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/version.txt") ? trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/version.txt")) : getVersionFromTimestamp((int)file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/timestamp.txt")),
                        "author" => trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt")),
                        "time" => date('c', (int)file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/timestamp.txt")),
                        "status" => file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/removed.txt") ? "removed" : (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/complete.txt") ? "processed" : ((file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt") && !file_exists("/proc/" . trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt")))) ? (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_plus.txt") ? "generating" : "crashed") : "queued")),
                        "input" => file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt"),
                        "filename" => "sunnystarbot-" . substr("00000000", 0, 8 - strlen(dechex(crc32($item)))) . dechex(crc32($item)) . "-" . substr(preg_replace("/^-|-$/", "", preg_replace("/-+/", "-", preg_replace("/[^a-z]/", "-", strtolower(trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt")))))), 0, 30) . ".wav",
                        "audio_url" => (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/complete.txt") && !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/removed.txt")) ? "https://cdn.equestria.dev/sunnystarbot/content/" . $item . "/audio.wav" : null,
                        "explicit" => file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/held.txt")
                    ];
                } else {
                    $data[] = [
                        "filename" => "sunnystarbot-" . substr("00000000", 0, 8 - strlen(dechex(crc32($item)))) . dechex(crc32($item)) . "-" . substr(preg_replace("/^-|-$/", "", preg_replace("/-+/", "-", preg_replace("/[^a-z]/", "-", strtolower(trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt")))))), 0, 30) . ".wav",
                        "id" => $item,
                        "version" => file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/version.txt") ? trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/version.txt")) : getVersionFromTimestamp((int)file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/timestamp.txt")),
                        "time" => date('c', (int)file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/timestamp.txt")),
                        "processed" => file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/complete.txt") && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/audio.wav"),
                        "queued" => !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt"),
                        "crashed" => file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt") && !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_plus.txt") && !file_exists("/proc/" . trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt"))),
                        "input" => file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt"),
                        "explicit" => file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/held.txt")
                    ];
                }
            }
        }
    }

    usort($data, function ($a, $b) {
        return strtotime($b["time"]) - strtotime($a["time"]);
    });

    return array_slice($data, 0, $count < INF ? $count : null);
}

function getPossible() {
    global $profile;
    $possible = true;

    foreach (array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs"), function ($i) { return !str_starts_with($i, "."); }) as $item) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt") && !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/blocked.txt")) {
            if (file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt") === $profile["id"]) {
                if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/complete.txt") && ((file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt") && file_exists("/proc/" . trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt")))) || !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt"))) $possible = false;
            }
        }
    }

    return $possible;
}

function getFilterCode($original) {
    global $hasPlus;
    $tokens = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/tokens.json"), true);

    $text = strtolower(trim($original ?? ""));

    if (isset($hasPlus) && $hasPlus) {
        $list = explode("\n", trim(strtolower(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/list-plus.txt"))));
        $list2 = explode("\n", trim(strtolower(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/list.txt"))));
    } else {
        $list = $list2 = explode("\n", trim(strtolower(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/list.txt"))));
    }

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

    if ($code === 0) {
        foreach ($list2 as $item) {
            $item = trim($item);

            if (str_contains($ptext, $item)) {
                if (str_contains($ptext, " " . $item . " ")) {
                    $code = 1;
                    $tripped[] = $item;
                }
            }
        }
    }

    if (!isset($hasPlus) || !$hasPlus) {
        if (str_contains($text, "~") || trim($ptext) === "ah") {
            $code = 2;
            $tripped[] = "~";
        }

        if ($code !== 2 && file_get_contents("https://www.purgomalum.com/service/containsprofanity?text=" . rawurlencode($text)) === "true") {
            $code = 3;
        }
    }

    return $code;
}

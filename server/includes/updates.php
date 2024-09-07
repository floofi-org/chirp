<?php

function getVersionFromTimestamp($timestamp = 0): string {
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

function getList($id = null, $count = INF, $new = false): array {
    global $profile;
    $data = [];

    if (isset($id)) {
        $item = $id;

        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item) && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt") && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt") && !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/blocked.txt")) {
            if (($new && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/model.txt")) || !$new) {
                $i = [
                    "id" => $item,
                    "version" => file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/version.txt") ? trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/version.txt")) : getVersionFromTimestamp((int)file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/timestamp.txt")),
                    "author" => trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt")),
                    "time" => date('c', (int)file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/timestamp.txt")),
                    "status" => file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/removed.txt") ? "removed" : (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/complete.txt") && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/audio.wav") ? "processed" : ((file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt") && !file_exists("/proc/" . trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt")))) ? (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input.txt") ? "generating" : "crashed") : (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt") ? "generating" : "queued"))),
                    "input" => file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt"),
                    "filename" => "sunnystarbot-" . substr("00000000", 0, 8 - strlen(dechex(crc32($item)))) . dechex(crc32($item)) . "-" . substr(preg_replace("/^-|-$/", "", preg_replace("/-+/", "-", preg_replace("/[^a-z]/", "-", strtolower(trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt")))))), 0, 30) . ".wav",
                    "audio_url" => (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/complete.txt") && !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/removed.txt")) ? "https://cdn.equestria.dev/sunnystarbot/content/" . $item . "/audio.wav" : null,
                    "explicit" => file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/held.txt")
                ];

                if ($new) {
                    $i["model"] = trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/model.txt"));
                }

                return $i;
            } else {
                error(404);
            }
        } else {
            error(404);
        }
    }

    foreach (array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs"), function ($i) { return !str_starts_with($i, "."); }) as $item) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt") && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt") && !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/blocked.txt")) {
            if (file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt") === $profile["id"] && (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/removed.txt"))) {
                if (($new && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/model.txt")) || !$new) {
                    $i = [
                        "id" => $item,
                        "version" => file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/version.txt") ? trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/version.txt")) : getVersionFromTimestamp((int)file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/timestamp.txt")),
                        "author" => trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt")),
                        "time" => date('c', (int)file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/timestamp.txt")),
                        "status" => file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/removed.txt") ? "removed" : (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/complete.txt") ? "processed" : ((file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt")) ? (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input.txt") ? "generating" : (!file_exists("/proc/" . trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt"))) ? "crashed" : "generating")) : "queued")),
                        "input" => file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt"),
                        "filename" => "sunnystarbot-" . substr("00000000", 0, 8 - strlen(dechex(crc32($item)))) . dechex(crc32($item)) . "-" . substr(preg_replace("/^-|-$/", "", preg_replace("/-+/", "-", preg_replace("/[^a-z]/", "-", strtolower(trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt")))))), 0, 30) . ".wav",
                        "audio_url" => (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/complete.txt") && !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/removed.txt")) ? "https://cdn.equestria.dev/sunnystarbot/content/" . $item . "/audio.wav" : null,
                        "explicit" => file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/held.txt")
                    ];

                    if ($new) {
                        $i["model"] = trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/model.txt"));
                    }

                    $data[] = $i;
                }
            }
        }
    }

    usort($data, function ($a, $b) {
        return strtotime($b["time"]) - strtotime($a["time"]);
    });

    return array_slice($data, 0, $count < INF ? $count : null);
}

function getPossible(): bool {
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
    // Use some code thing?
    $code = 0;

    // For each item in the list
    foreach ($list as $item) {
        // Trim that item (already done in Kotlin)
        $item = trim($item);

        // If the processed text contains this item
        if (str_contains($ptext, $item)) {
            // And the item is longer than 4 characters, or it is an entire word
            if (strlen($item) > 4 || str_contains($ptext, " " . $item . " ")) {
                $code = 2; // Code 2?
            // Or if it is (shorter than) 4 characters and is an entire word
            } else if (str_contains($ptext, " " . $item . " ")) {
                $code = 1; // Code 1?
            }
        }
    }

    // If the code is still 0?
    if ($code === 0) {
        // Process the list again??
        foreach ($list2 as $item) {
            // This does literally the same stuff as above
            $item = trim($item);

            if (str_contains($ptext, $item)) {
                if (str_contains($ptext, " " . $item . " ")) {
                    $code = 1;
                }
            }
        }
    }

    // I think this was meant to block like "Ahhhh~"
    if (str_contains($text, "~") || trim($ptext) === "ah") {
        $code = 2;
    }

    // This uses a completely unneeded online profanity check service
    if ($code !== 2 && file_get_contents("https://www.purgomalum.com/service/containsprofanity?text=" . rawurlencode($text)) === "true") {
        $code = 3; // Code 3?
    }

    // And now we return this code, in the Kotlin version we want a boolean instead
    return $code;
}

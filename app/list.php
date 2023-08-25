<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php"; global $loggedIn; global $profile;
$data = [];
header("Content-Type: application/json");

foreach (array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs"), function ($i) { return !str_starts_with($i, "."); }) as $item) {
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt") && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt")) {
        if (file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt") === $profile["id"]) {
            $data[] = [
                "id" => $item,
                "time" => date('c', (int)file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/timestamp.txt")),
                "processed" => file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/complete.txt"),
                "input" => file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt")
            ];
        }
    }
}

usort($data, function ($a, $b) {
    return strtotime($a["time"]) - strtotime($b["time"]);
});

die(json_encode($data, JSON_PRETTY_PRINT));
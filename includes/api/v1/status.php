<?php

endpoint();

function getProcessingUnits() {
    $data = [];
    $out = [];

    exec("pgrep -a python3", $data);

    foreach ($data as $line) {
        if (str_ends_with($line, " python3 " . $_SERVER['DOCUMENT_ROOT'] . "/includes/runtime/main.py")) {
            $out[] = (int)explode(" ", $line)[0];
        }
    }

    return $out;
}

global $user;
output([
    "user" => $user,
    "version" => file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/version.txt"),
    "processingUnits" => array_map(function ($pid) {
        $busy = false;

        foreach (array_filter(scandir($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs"), function ($i) { return !str_starts_with($i, "."); }) as $item) {
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt")) {
                if ((int)(trim(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/process.txt"))) === $pid) {
                    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/complete.txt")) {
                        $busy = true;
                    }
                }
            }
        }

        return [
            "pid" => $pid,
            "busy" => $busy
        ];
    }, getProcessingUnits())
]);
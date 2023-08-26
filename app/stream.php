<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php"; global $loggedIn; global $profile;
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/updates.php";

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

function refresh() {
    echo "id: list" . PHP_EOL;
    echo "data: " . base64_encode(json_encode(getList())) . PHP_EOL;
    echo PHP_EOL;
    ob_flush();
    flush();

    echo "id: possible" . PHP_EOL;
    echo "data: " . base64_encode(json_encode(getPossible())) . PHP_EOL;
    echo PHP_EOL;
    ob_flush();
    flush();

    usleep(500000);
    refresh();
}

refresh();
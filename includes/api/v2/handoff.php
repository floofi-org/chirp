<?php

endpoint(["POST"], false, [
    "token" => [
        "required" => true,
        "post" => true
    ]
], false);

$validToken = false;

if (!str_contains($_POST["token"], "/") && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/handoff/" . $_POST["token"])) {
    $handoffData = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/handoff/" . $_POST["token"]), true);

    if (time() - strtotime($handoffData['date']) > 120) {
        error(401);
        return;
    }

    unlink($_SERVER['DOCUMENT_ROOT'] . "/includes/handoff/" . $_POST["token"]);
    output([
        "token" => $handoffData['token']
    ]);
}

error(401);

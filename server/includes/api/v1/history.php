<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/updates.php";

global $parts;

if (isset($parts[2])) {
    endpoint(["GET", "DELETE"]);

    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        output(getList(true, $parts[2]));
    } else {
        global $profile;

        if (str_contains($parts[2], ".") || str_contains($parts[2], "/") || !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $parts[2])) error(404);
        $item = $parts[2];
        if (!(file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt") && file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/input_orig.txt") && !file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/blocked.txt"))) error(404);
        if ( file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/author.txt") !== $profile["id"]) error(403);

        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $item . "/removed.txt")) error(409);

        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/outputs/" . $parts[2] . "/removed.txt", "");
        output(getList(true, $parts[2]));
    }
} else {
    endpoint(["GET"], false, [
        "amount" => [
            "required" => false,
            "post" => false
        ]
    ]);

    if (isset($_GET["amount"]) && is_numeric($_GET["amount"]) && (int)$_GET["amount"] == (float)$_GET["amount"]) {
        $list = getList(true, null, (int)($_GET["amount"]));
    } elseif (isset($_GET["amount"])) {
        error(400);
    } else {
        $list = getList(true);
    }

    output([
        "history" => $list,
        "count" => count($list)
    ]);
}
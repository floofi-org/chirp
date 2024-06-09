<?php

if (!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] !== "https://sunnystarbot.equestria.dev/docs/") die();

require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/session.php"; global $loggedIn; global $profile;
$keys = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/keys.json"), true);
$keys[$profile["id"]] = substr(str_replace("+", ".", str_replace("/", "_", str_replace("=", "", base64_encode(random_bytes(64))))), 0, 48);
file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/includes/keys.json", json_encode($keys));

header("Location: /docs");
die();